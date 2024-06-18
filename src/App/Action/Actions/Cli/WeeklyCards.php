<?php
namespace App\Action\Actions\Cli;

use App\Action\BaseAction;
use App\Service\TrelloCard;

class WeeklyCards extends BaseAction
{

    public function run()
    {
        $config = yaml_parse_file(ROOT . '/config/weekly-cards.yaml');
        $name = $config['card']['name'];
        $description = $config['card']['description'] ?? null;
        $items = $config['card']['items'];

        $trelloCard = new TrelloCard();
        $trelloCard->setBoardId(env('TRELLO_BOARD_ID'));
        $trelloCard->setListId(env('TRELLO_LIST_ID_THIS_WEEK'));

        $trelloCard->addLabel(env('TRELLO_LABEL_AUTO'));
        $trelloCard->addLabel(env('TRELLO_LABEL_PRIVATE'));
        $trelloCard->addDueDate(now()->endOfWeek()->endOfDay());

        $cardId = $trelloCard->addCard($name, $description);

        $trelloCard->setCardId($cardId);
        $trelloCard->addCheckList('Checklist', $items);
    }
}
