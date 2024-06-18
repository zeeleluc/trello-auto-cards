<?php
namespace App\Action\Actions\Cli;

use App\Action\BaseAction;
use App\Service\TrelloCard;

class DailyCards extends BaseAction
{

    public function run()
    {
        // you can also use yaml_parse_file and parse the yaml config files
        $config = yaml_parse(env('TRELLO_CARDS_DAILY'));
        
        foreach ($config['cards'] as $card) {
            $name = $card['name'];
            $description = $card['description'] ?? null;
            $items = $card['items'];

            $trelloCard = new TrelloCard();
            $trelloCard->setBoardId(env('TRELLO_BOARD_ID'));
            $trelloCard->setListId(env('TRELLO_LIST_ID_TODAY'));

            $trelloCard->addLabel(env('TRELLO_LABEL_AUTO'));
            $trelloCard->addLabel(env('TRELLO_LABEL_PRIVATE'));
            $trelloCard->addDueDate(now()->endOfDay());

            $cardId = $trelloCard->addCard($name, $description);

            $trelloCard->setCardId($cardId);
            $trelloCard->addCheckList('Checklist', $items);
        }
    }
}
