<?php namespace Idmkr\Adwords\Repositories\User;

use AdWordsConstants;
use Cartalyst\Support\Traits;
use Idmkr\Adwords\Handlers\User\UserDataHandler;
use Idmkr\Adwords\Handlers\User\UserDataHandlerInterface;
use Idmkr\Adwords\Repositories\AdwordsRepository;
use Illuminate\Support\Collection;
use LaravelGoogleAds\AdWords\AdWordsUser;
use ManagedCustomer;
use Paging;
use Selector;

class UserRepository extends AdwordsRepository
{
    /**
     * @param AdWordsUser $user
     *
     * @return Collection
     */
    public function findAll(AdWordsUser $user) : Collection
	{
        // Get the service, which loads the required classes.
        $managedCustomerService = $user->GetService('ManagedCustomerService', $this->getAdwordsApiVersion());

        return $this->container['cache']->rememberForever('idmkr.adwords.user.all.'.$user->GetClientCustomerId(), function() use($managedCustomerService) {
            // Create selector.
            $selector = new Selector();
            // Specify the fields to retrieve.
            $selector->fields = array('CustomerId', 'Name');
            $selector->paging = new Paging(0, AdWordsConstants::RECOMMENDED_PAGE_SIZE);

            // Create map from customerID to account.
            $accounts = array();
            // Create map from customerId to parent and child links.
            $childLinks = array();
            $parentLinks = array();
            do {
                // Make the get request.
                $graph = $managedCustomerService->get($selector);

                // Create links between manager and clients.
                if (isset($graph->entries)) {
                    if (isset($graph->links)) {
                        foreach ($graph->links as $link) {
                            $childLinks[$link->managerCustomerId][] = $link;
                            $parentLinks[$link->clientCustomerId] = $link;
                        }
                    }
                    foreach ($graph->entries as $account) {
                        $accounts[$account->customerId] = $account;
                    }
                }
                $selector->paging->startIndex += AdWordsConstants::RECOMMENDED_PAGE_SIZE;
            } while ($selector->paging->startIndex < $graph->totalNumEntries);

            $rootAccount = null;
            foreach ($accounts as $account) {
                if (!array_key_exists($account->customerId, $parentLinks)) {
                    $rootAccount = $account;
                    break;
                }
            }

            if ($rootAccount !== null) {
                return collect([$this->getAccountTree($rootAccount, $accounts, $childLinks)]);
            } else {
                return ("No accounts were found.\n");
            }
        });

	}

    /**
     * Displays an account tree, starting at the account provided, and recursing to
     * all child accounts.
     * 
     * @param ManagedCustomer $account the account to display
     * @param array $accounts a map from customerId to account
     * @param array $links a map from customerId to child links
     * @param int $depth the depth of the current account in the tree
     */
    public function getAccountTree(ManagedCustomer $account, $accounts, $links) : Array
    {
        return [
            'name' => $account->name,
            'id' => $account->customerId,
            'accounts' => array_key_exists($account->customerId, $links) ?
                array_map(function (\ManagedCustomerLink $childLink) use($accounts, $links) {
                    return $this->getAccountTree($accounts[$childLink->clientCustomerId], $accounts, $links);
                }, $links[$account->customerId]) : []
        ];
    }

    /**
     * @param     $accounts
     * @param int $depth
     *
     * @return array
     */
    public function getFlatTree($accounts, $depth = 0) : Array
    {
        $flattenedAccounts = [];
        foreach($accounts as $account) {
            $account['depth'] = $depth;
            $flattenedAccounts[] = array_except($account, "accounts");
            if($account['accounts']) {
                $flattenedAccounts = array_merge($flattenedAccounts, $this->getFlatTree($account['accounts'], $depth+1));
            }
        }
        return $flattenedAccounts;
    }

    /**
     * @param $customerId
     * @param $adwordsAccounts
     *
     * @return array|null
     */
    public function getAccount($customerId, $adwordsAccounts)
    {
        foreach($adwordsAccounts as $account) {
            if($account['id'] == $customerId) {
                return $account;
            }
            if($found = $this->getAccount($customerId, $account['accounts'])) {
                return $found;
            }
        }
        return null;
    }


    /**
     * @return AdWordsUser|null
     */
    public function getDefaultUser()
    {
        return new AdWordsUser();
    }

    /**
     * @param $clientCustomerId
     *
     * @return AdWordsUser|null
     */
    public function getNewUser($clientCustomerId)
    {
        $user = new AdWordsUser();
        $user->SetClientCustomerId($clientCustomerId);
        return $user;
    }

    protected function getEntityClassName() : string
    {
        return 'ManagedCustomer';
    }

    protected function getEventNamespace() : string
    {
        return 'idmkr.adwords.user';
    }

    protected function getDataHandler() : UserDataHandler
    {
        return app('idmkr.adwords.user.handler.data');
    }
}
