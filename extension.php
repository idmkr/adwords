<?php

use Illuminate\Foundation\Application;
use Cartalyst\Extensions\ExtensionInterface;
use Cartalyst\Settings\Repository as Settings;
use Cartalyst\Permissions\Container as Permissions;

return [

    /*
    |--------------------------------------------------------------------------
    | Name
    |--------------------------------------------------------------------------
    |
    | This is your extension name and it is only required for
    | presentational purposes.
    |
    */

    'name' => 'Adwords',

    /*
    |--------------------------------------------------------------------------
    | Slug
    |--------------------------------------------------------------------------
    |
    | This is your extension unique identifier and should not be changed as
    | it will be recognized as a new extension.
    |
    | Ideally, this should match the folder structure within the extensions
    | folder, but this is completely optional.
    |
    */

    'slug' => 'idmkr/adwords',

    /*
    |--------------------------------------------------------------------------
    | Author
    |--------------------------------------------------------------------------
    |
    | Because everybody deserves credit for their work, right?
    |
    */

    'author' => 'IDMKR',

    /*
    |--------------------------------------------------------------------------
    | Description
    |--------------------------------------------------------------------------
    |
    | One or two sentences describing the extension for users to view when
    | they are installing the extension.
    |
    */

    'description' => 'AdWords PHP library',

    /*
    |--------------------------------------------------------------------------
    | Version
    |--------------------------------------------------------------------------
    |
    | Version should be a string that can be used with version_compare().
    | This is how the extensions versions are compared.
    |
    */

    'version' => '0.1.0',

    /*
    |--------------------------------------------------------------------------
    | Requirements
    |--------------------------------------------------------------------------
    |
    | List here all the extensions that this extension requires to work.
    | This is used in conjunction with composer, so you should put the
    | same extension dependencies on your main composer.json require
    | key, so that they get resolved using composer, however you
    | can use without composer, at which point you'll have to
    | ensure that the required extensions are available.
    |
    */

    'require' => [

    ],

    /*
    |--------------------------------------------------------------------------
    | Autoload Logic
    |--------------------------------------------------------------------------
    |
    | You can define here your extension autoloading logic, it may either
    | be 'composer', 'platform' or a 'Closure'.
    |
    | If composer is defined, your composer.json file specifies the autoloading
    | logic.
    |
    | If platform is defined, your extension receives convetion autoloading
    | based on the Platform standards.
    |
    | If a Closure is defined, it should take two parameters as defined
    | bellow:
    |
    |	object \Composer\Autoload\ClassLoader      $loader
    |	object \Illuminate\Foundation\Application  $app
    |
    | Supported: "composer", "platform", "Closure"
    |
    */

    'autoload' => 'composer',

    /*
    |--------------------------------------------------------------------------
    | Service Providers
    |--------------------------------------------------------------------------
    |
    | Define your extension service providers here. They will be dynamically
    | registered without having to include them in app/config/app.php.
    |
    */

    'providers' => [

        'Idmkr\Adwords\Providers\CampaignsServiceProvider',
        'Idmkr\Adwords\Providers\FeedServiceProvider',
        'Idmkr\Adwords\Providers\AdgroupServiceProvider',
        'Idmkr\Adwords\Providers\AdServiceProvider',
        'Idmkr\Adwords\Providers\BatchServiceProvider',
        'Idmkr\Adwords\Providers\KeywordServiceProvider',
        'Idmkr\Adwords\Providers\UserServiceProvider',
        'Idmkr\Adwords\Providers\BudgetServiceProvider',

    ],

    /*
    |--------------------------------------------------------------------------
    | Routes
    |--------------------------------------------------------------------------
    |
    | Closure that is called when the extension is started. You can register
    | any custom routing logic here.
    |
    | The closure parameters are:
    |
    |	object \Cartalyst\Extensions\ExtensionInterface  $extension
    |	object \Illuminate\Foundation\Application        $app
    |
    */

    'routes' => function (ExtensionInterface $extension, Application $app) {
        Route::group([
            'prefix' => admin_uri() . '/adwords/feeds',
            'namespace' => 'Idmkr\Adwords\Controllers\Admin',
        ], function () {
            Route::get('/', ['as' => 'admin.idmkr.adwords.feeds.all', 'uses' => 'FeedsController@index']);
            Route::post('/', ['as' => 'admin.idmkr.adwords.feeds.all', 'uses' => 'FeedsController@executeAction']);

            Route::get('grid', ['as' => 'admin.idmkr.adwords.feeds.grid', 'uses' => 'FeedsController@grid']);

            Route::get('create', ['as' => 'admin.idmkr.adwords.feeds.create', 'uses' => 'FeedsController@create']);
            Route::post('create', ['as' => 'admin.idmkr.adwords.feeds.create', 'uses' => 'FeedsController@store']);

            Route::get('{id}', ['as' => 'admin.idmkr.adwords.feeds.edit', 'uses' => 'FeedsController@edit']);
            Route::post('{id}', ['as' => 'admin.idmkr.adwords.feeds.edit', 'uses' => 'FeedsController@update']);

            Route::delete('{id}', ['as' => 'admin.idmkr.adwords.feeds.delete', 'uses' => 'FeedsController@delete']);
        });

        Route::group([
            'prefix' => admin_uri() . '/adwords/adgroups',
            'namespace' => 'Idmkr\Adwords\Controllers\Admin',
        ], function () {
            Route::get('/', ['as' => 'admin.idmkr.adwords.adgroups.all', 'uses' => 'AdgroupsController@index']);
            Route::post('/', ['as' => 'admin.idmkr.adwords.adgroups.all', 'uses' => 'AdgroupsController@executeAction']);

            Route::get('grid', ['as' => 'admin.idmkr.adwords.adgroups.grid', 'uses' => 'AdgroupsController@grid']);

            Route::get('create', ['as' => 'admin.idmkr.adwords.adgroups.create', 'uses' => 'AdgroupsController@create']);
            Route::post('create', ['as' => 'admin.idmkr.adwords.adgroups.create', 'uses' => 'AdgroupsController@store']);

            Route::get('{id}', ['as' => 'admin.idmkr.adwords.adgroups.edit', 'uses' => 'AdgroupsController@edit']);
            Route::post('{id}', ['as' => 'admin.idmkr.adwords.adgroups.edit', 'uses' => 'AdgroupsController@update']);

            Route::delete('{id}', ['as' => 'admin.idmkr.adwords.adgroups.delete', 'uses' => 'AdgroupsController@delete']);
        });

        Route::group([
            'prefix' => admin_uri() . '/adwords/users',
            'namespace' => 'Idmkr\Adwords\Controllers\Admin',
        ], function () {
            Route::get('/', ['as' => 'admin.idmkr.adwords.users.all', 'uses' => 'UsersController@index']);
            Route::post('/', ['as' => 'admin.idmkr.adwords.users.all', 'uses' => 'UsersController@executeAction']);

            Route::get('grid', ['as' => 'admin.idmkr.adwords.users.grid', 'uses' => 'UsersController@grid']);

            Route::get('create', ['as' => 'admin.idmkr.adwords.users.create', 'uses' => 'UsersController@create']);
            Route::post('create', ['as' => 'admin.idmkr.adwords.users.create', 'uses' => 'UsersController@store']);

            Route::get('{id}', ['as' => 'admin.idmkr.adwords.users.edit', 'uses' => 'UsersController@edit']);
            Route::post('{id}', ['as' => 'admin.idmkr.adwords.users.edit', 'uses' => 'UsersController@update']);

            Route::delete('{id}', ['as' => 'admin.idmkr.adwords.users.delete', 'uses' => 'UsersController@delete']);
        });
    },

    /*
    |--------------------------------------------------------------------------
    | Database Seeds
    |--------------------------------------------------------------------------
    |
    | Platform provides a very simple way to seed your database with test
    | data using seed classes. All seed classes should be stored on the
    | `database/seeds` directory within your extension folder.
    |
    | The order you register your seed classes on the array below
    | matters, as they will be ran in the exact same order.
    |
    | The seeds array should follow the following structure:
    |
    |	Vendor\Namespace\Database\Seeds\FooSeeder
    |	Vendor\Namespace\Database\Seeds\BarSeeder
    |
    */

    'seeds' => [

    ],

    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    |
    | Register here all the permissions that this extension has. These will
    | be shown in the user management area to build a graphical interface
    | where permissions can be selected to allow or deny user access.
    |
    | For detailed instructions on how to register the permissions, please
    | refer to the following url https://cartalyst.com/manual/permissions
    |
    */

    'permissions' => function (Permissions $permissions) {
        $permissions->group('feed', function ($g) {
            $g->name = 'Feeds';

            $g->permission('feed.index', function ($p) {
                $p->label = trans('idmkr/adwords::feeds/permissions.index');

                $p->controller('Idmkr\Adwords\Controllers\Admin\FeedsController', 'index, grid');
            });

            $g->permission('feed.create', function ($p) {
                $p->label = trans('idmkr/adwords::feeds/permissions.create');

                $p->controller('Idmkr\Adwords\Controllers\Admin\FeedsController', 'create, store');
            });

            $g->permission('feed.edit', function ($p) {
                $p->label = trans('idmkr/adwords::feeds/permissions.edit');

                $p->controller('Idmkr\Adwords\Controllers\Admin\FeedsController', 'edit, update');
            });

            $g->permission('feed.delete', function ($p) {
                $p->label = trans('idmkr/adwords::feeds/permissions.delete');

                $p->controller('Idmkr\Adwords\Controllers\Admin\FeedsController', 'delete');
            });
        });

        $permissions->group('adgroup', function ($g) {
            $g->name = 'Adgroups';

            $g->permission('adgroup.index', function ($p) {
                $p->label = trans('idmkr/adwords::adgroups/permissions.index');

                $p->controller('Idmkr\Adwords\Controllers\Admin\AdgroupsController', 'index, grid');
            });

            $g->permission('adgroup.create', function ($p) {
                $p->label = trans('idmkr/adwords::adgroups/permissions.create');

                $p->controller('Idmkr\Adwords\Controllers\Admin\AdgroupsController', 'create, store');
            });

            $g->permission('adgroup.edit', function ($p) {
                $p->label = trans('idmkr/adwords::adgroups/permissions.edit');

                $p->controller('Idmkr\Adwords\Controllers\Admin\AdgroupsController', 'edit, update');
            });

            $g->permission('adgroup.delete', function ($p) {
                $p->label = trans('idmkr/adwords::adgroups/permissions.delete');

                $p->controller('Idmkr\Adwords\Controllers\Admin\AdgroupsController', 'delete');
            });
        });

        $permissions->group('batchjob', function ($g) {
            $g->name = 'Batchjobs';

            $g->permission('batchjob.index', function ($p) {
                $p->label = trans('idmkr/adwords::batchjobs/permissions.index');

                $p->controller('Idmkr\Adwords\Controllers\Admin\BatchjobsController', 'index, grid');
            });

            $g->permission('batchjob.create', function ($p) {
                $p->label = trans('idmkr/adwords::batchjobs/permissions.create');

                $p->controller('Idmkr\Adwords\Controllers\Admin\BatchjobsController', 'create, store');
            });

            $g->permission('batchjob.edit', function ($p) {
                $p->label = trans('idmkr/adwords::batchjobs/permissions.edit');

                $p->controller('Idmkr\Adwords\Controllers\Admin\BatchjobsController', 'edit, update');
            });

            $g->permission('batchjob.delete', function ($p) {
                $p->label = trans('idmkr/adwords::batchjobs/permissions.delete');

                $p->controller('Idmkr\Adwords\Controllers\Admin\BatchjobsController', 'delete');
            });
        });

        $permissions->group('generation', function ($g) {
            $g->name = 'Generations';

            $g->permission('generation.index', function ($p) {
                $p->label = trans('idmkr/adwords::generations/permissions.index');

                $p->controller('Idmkr\Adwords\Controllers\Admin\GenerationsController', 'index, grid');
            });

            $g->permission('generation.create', function ($p) {
                $p->label = trans('idmkr/adwords::generations/permissions.create');

                $p->controller('Idmkr\Adwords\Controllers\Admin\GenerationsController', 'create, store');
            });

            $g->permission('generation.edit', function ($p) {
                $p->label = trans('idmkr/adwords::generations/permissions.edit');

                $p->controller('Idmkr\Adwords\Controllers\Admin\GenerationsController', 'edit, update');
            });

            $g->permission('generation.delete', function ($p) {
                $p->label = trans('idmkr/adwords::generations/permissions.delete');

                $p->controller('Idmkr\Adwords\Controllers\Admin\GenerationsController', 'delete');
            });
        });

        $permissions->group('user', function ($g) {
            $g->name = 'Users';

            $g->permission('user.index', function ($p) {
                $p->label = trans('idmkr/adwords::users/permissions.index');

                $p->controller('Idmkr\Adwords\Controllers\Admin\UsersController', 'index, grid');
            });

            $g->permission('user.create', function ($p) {
                $p->label = trans('idmkr/adwords::users/permissions.create');

                $p->controller('Idmkr\Adwords\Controllers\Admin\UsersController', 'create, store');
            });

            $g->permission('user.edit', function ($p) {
                $p->label = trans('idmkr/adwords::users/permissions.edit');

                $p->controller('Idmkr\Adwords\Controllers\Admin\UsersController', 'edit, update');
            });

            $g->permission('user.delete', function ($p) {
                $p->label = trans('idmkr/adwords::users/permissions.delete');

                $p->controller('Idmkr\Adwords\Controllers\Admin\UsersController', 'delete');
            });
        });
    },

    /*
    |--------------------------------------------------------------------------
    | Widgets
    |--------------------------------------------------------------------------
    |
    | Closure that is called when the extension is started. You can register
    | all your custom widgets here. Of course, Platform will guess the
    | widget class for you, this is just for custom widgets or if you
    | do not wish to make a new class for a very small widget.
    |
    */

    'widgets' => function () {

    },

    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    |
    | Register any settings for your extension. You can also configure
    | the namespace and group that a setting belongs to.
    |
    */

    'settings' => function (Settings $settings, Application $app) {

    },

    /*
    |--------------------------------------------------------------------------
    | Menus
    |--------------------------------------------------------------------------
    |
    | You may specify the default various menu hierarchy for your extension.
    | You can provide a recursive array of menu children and their children.
    | These will be created upon installation, synchronized upon upgrading
    | and removed upon uninstallation.
    |
    | Menu children are automatically put at the end of the menu for extensions
    | installed through the Operations extension.
    |
    | The default order (for extensions installed initially) can be
    | found by editing app/config/platform.php.
    |
    */

    'menus' => [

        'admin' => [
            [
                'slug' => 'admin-idmkr-adwords',
                'name' => 'Adwords',
                'class' => 'fa fa-circle-o',
                'uri' => 'adwords',
                'regex' => '/:admin\/adwords/i',
                'children' => [
                    [
                        'class' => 'fa fa-circle-o',
                        'name' => 'Campaigns',
                        'uri' => 'adwords/campaigns',
                        'regex' => '/:admin\/adwords\/campaigns/i',
                        'slug' => 'admin-idmkr-adwords-campaigns',
                    ],
                    [
                        'class' => 'fa fa-circle-o',
                        'name' => 'Users',
                        'uri' => 'adwords/users',
                        'regex' => '/:admin\/adwords\/user/i',
                        'slug' => 'admin-idmkr-adwords-user',
                    ],
                ],
            ],
        ],
    ],
];