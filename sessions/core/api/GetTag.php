<?php

class ZukiChat {
    private $api_key;
    private $api_backup_key;
    private $api_endpoint = 'https://zukijourney.xyzbot.net/v1/chat/completions';
    private $system_prompt;
    private $model;
    private $temperature;

    public function __construct($api_key, $api_backup_key = "", $model = "gpt-4", $system_prompt = "You define the type of task in the to do list", $temperature = 0.7) {
        $this->api_key = $api_key;
        $this->api_backup_key = $api_backup_key;
        $this->system_prompt = $system_prompt;
        $this->temperature = $temperature;
        $this->model = $model;
    }

    private function sendRequest($user_message, $endpoint) {
        $data = [
            "model" => $this->model,
            "messages" => [
                [
                    "role" => "user",
                    "content" => $user_message
                ]
            ],
            "max_tokens" => 150,
            "temperature" => $this->temperature,
            "system_prompt" => $this->system_prompt
        ];

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key
        ]);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }
        curl_close($ch);

        return json_decode($response, true);
    }

    public function sendMessage($user_name, $user_message) {
        return $this->sendRequest($user_message, $this->api_endpoint);
    }
}

function getTag($config, $text)
{
    try {
        $question = "{$text}, это задача в to-do list определи тип этой задачи и ответь НЕ БОЛЕЕ ЧЕМ ДВУМЯ СЛОВАМИ например: personal, work, school, programming, error, fix, и подобные этим. на английском. В случае если не распознана задача выводи task";
        $api_key = $config["site"]["api"];
        $zuki_chat = new ZukiChat($api_key);

        $response = $zuki_chat->sendMessage('user1', $question);

        $tag = $response['choices'][0]['message']['content'];

        return $tag;

    } catch (Exception $e) {
        echo 'Ошибка: ' . $e->getMessage();
    }
}

?>
