<?php
namespace App\Object;

use App\Action\Action;
use App\Request;

abstract class BaseObject
{
    public function getAbstractAction(): Action
    {
        return ObjectManager::getOne('App\Action\Action');
    }

    public function getRequest(): Request
    {
        return ObjectManager::getOne('App\Request');
    }
}
