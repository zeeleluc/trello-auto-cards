<?php
namespace App\Service;

use App\Slack;
use Carbon\Carbon;

class TrelloCard
{

    private string $url;

    private array $data = [];

    private array $labels = [];

    private ?Carbon $dueDate = null;

    public function __construct()
    {
        $this->data['key'] = env('TRELLO_API_KEY');
        $this->data['token'] = env('TRELLO_TOKEN');
    }

    public function addDueDate(Carbon $dueDate): TrelloCard
    {
        $this->dueDate = $dueDate->addHours(4);

        return $this;
    }

    public function addLabel(string $labelId): TrelloCard
    {
        $this->labels[] = $labelId;

        return $this;
    }

    public function setBoardId(string $boardId): TrelloCard
    {
        $this->data['idBoard'] = $boardId;

        return $this;
    }

    public function setListId(string $listId): TrelloCard
    {
        $this->data['idList'] = $listId;

        return $this;
    }

    public function setCardId(string $cardId): TrelloCard
    {
        $this->data['idCard'] = $cardId;

        return $this;
    }

    public function addCard(string $name, string $description = null):? string
    {
        $this->url = env('TRELLO_URL') . 'cards';

        $additionalData = [];
        $additionalData['name'] = $name;
        if ($description) {
            $additionalData['desc'] = $description;
        }

        if ($this->labels) {
            $additionalData['idLabels'] = [];
            foreach ($this->labels as $label) {
                $additionalData['idLabels'][] = $label;
            }
        }

        if ($this->dueDate) {
            $additionalData['due'] = $this->dueDate->format('Y-m-d H:i:s');
        }

        if ($response = $this->post($additionalData)) {
            $cardId = $response['id'];

            $this->url = env('TRELLO_URL') . 'cards/' . $cardId;
            $this->put(['pos' => 'top']);

            return $cardId;
        }
    }

    public function addCheckList(string $name, array $items): void
    {
        $this->url = env('TRELLO_URL') . 'checklists';

        $additionalData = [];
        $additionalData['name'] = $name;

        if ($response = $this->post($additionalData)) {
            $idChecklist = $response['id'];

            foreach ($items as $item) {
                $this->url = env('TRELLO_URL') . 'checklists/' . $idChecklist . '/checkItems';
                $this->post(['name' => $item]);
            }
        }

    }

    private function post(array $additionalData)
    {
        return $this->call('post', $additionalData);
    }

    private function put(array $additionalData)
    {
        return $this->call('put', $additionalData);
    }

    private function call(string $type, array $additionalData)
    {
        $data = array_merge($this->data, $additionalData);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        if ($type === 'post') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        } elseif ($type === 'put') {
            curl_setopt($ch, CURLOPT_PUT, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            (new Slack())->sendErrorMessage($error);
        } else {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode !== 200) {
                (new Slack())->sendSuccessMessage($httpCode . ': ' . $response);
            } else {
                return (array) json_decode($response, true);
            }
        }

        curl_close($ch);
    }
}
