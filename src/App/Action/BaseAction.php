<?php
namespace App\Action;

use App\Object\BaseObject;

abstract class BaseAction extends BaseObject
{
    public function __construct()
    {
        if (!is_cli()) {
            echo 'This is a terminal only application.';
            exit;
        }
    }

    public function run()
    {
        return $this;
    }
}
