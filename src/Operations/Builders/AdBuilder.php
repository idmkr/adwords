<?php namespace Idmkr\Adwords\Operations\Builders\Ad;

use Idmkr\Adwords\Operations\Builders\Builder;

abstract class AdBuilder extends Builder
{
    private $innerVarReg = '/\{(.+?)\}/';
    private $outerVarReg = '/\{.+?\}/';
    private $adwordsVarReg = '/\{(keyword):(.+?)\}/i';

    private $validationLengthRules = [
        'title1' => 30,
        'title2' => 30,
        'path1' => 15,
        'path2' => 15,
        'description' => 80,
    ];

    /**
     * @param array $adTemplate
     * @param array $data
     *
     * @return bool
     */
    protected function adPassValidation(array $adTemplate, array $data) : bool
    {
        foreach($this->validationLengthRules as $field => $maxLength) {
            $fieldWithoutVarLength = strlen(preg_replace($this->outerVarReg, '', data_get($adTemplate, $field)));
            $varsLength = 0;
            $varValuesLength = 0;
            $adwordsVarValueLength = 0;

            if(strpos(data_get($adTemplate, $field), '{')) {
                if(preg_match_all($this->innerVarReg, data_get($adTemplate, $field), $innerVarRegResult)) {
                    foreach($innerVarRegResult[1] as $i => $varName) {
                        $varsLength += strlen($innerVarRegResult[0][$i]);
                        $varValuesLength += strlen($data[$varName]);
                    }
                }

                if(preg_match($this->adwordsVarReg, data_get($adTemplate, $field), $adwordsVarRegResult)) {
                    $adwordsVarValueLength += strlen($adwordsVarRegResult[0]) - 10;
                }
            }

            $computedLength = $fieldWithoutVarLength + $varValuesLength + $adwordsVarValueLength;

            if($computedLength > $maxLength) {
                return false;
            }
        }
        return true;
    }

    protected function getAdTemplateFields()
    {
        return ["title1", "title2", "description", "path1", "path2"];
    }
}