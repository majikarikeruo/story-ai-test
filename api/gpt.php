<?php

require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../config/google_auth.php';



$messageHistory = json_decode(urldecode($_POST['messageHistory']), true);

$api_key = $_ENV['OPEN_AI_KEY'];
$url = 'https://api.openai.com/v1/chat/completions';

// リクエストヘッダー

$headers = array(
    'Content-Type: application/json',
    'Authorization: Bearer ' . $api_key
);

// リクエストボディ
$data = array(
    "model" => "gpt-3.5-turbo",
    "stream" => true,
    "messages" => $messageHistory,
);



$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

echo $result;
