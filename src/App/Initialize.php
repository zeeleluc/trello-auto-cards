<?php
namespace App;

use App\Action;
use App\Action\Action as AbstractAction;
use App\Action\BaseAction;
use App\Object\BaseObject;
use App\Object\ObjectManager;

class Initialize extends BaseObject
{
    public function __construct()
    {
        ObjectManager::set(new AbstractAction());
        ObjectManager::set(new Request());
    }

    public function action(): Initialize
    {
        $this->getAbstractAction()->setAction($this->resolveAction());
        $this->getAbstractAction()->getAction()->run();

        return $this;
    }

    public function output(): void
    {
        $variables = $this->getAbstractAction()->getAction()->getVariables();

        var_dump($variables);

    }

    /**
     * @return BaseAction
     * @throws \Exception
     */
    private function resolveAction(): BaseAction
    {
        return new Action\Actions\Cli();
    }

}
