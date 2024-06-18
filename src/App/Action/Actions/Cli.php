<?php
namespace App\Action\Actions;

use App\Action\Actions\Cli\DailyCards;
use App\Action\Actions\Cli\MonthlyCards;
use App\Action\Actions\Cli\WeeklyCards;
use App\Action\BaseAction;

class Cli extends BaseAction
{

    private string $action;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();

        if (!$_SERVER['argv']) {
            exit;
        }

        if (!isset($_SERVER['argv'][1])) {
            exit;
        }

        $this->action = $_SERVER['argv'][1];

        if ($this->action === 'daily-cards') {
            (new DailyCards())->run();
        } elseif ($this->action === 'weekly-cards') {
            (new WeeklyCards())->run();
        } elseif ($this->action === 'monthly-cards') {
            (new MonthlyCards())->run();
        } else {
            echo 'Action not found.' . PHP_EOL;
        }
    }
}
