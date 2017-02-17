<?php namespace Idmkr\Adwords\Operations\Directors;


interface DirectorInterface
{
    public function upload(Array $operations);
    public function getData();
    public function log($msg);
}