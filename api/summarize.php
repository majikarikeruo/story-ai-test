<?php

require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../config/google_auth.php';



if (isset($_SESSION['access_token'])) {
    $client->setAccessToken($_SESSION['access_token']);
    $service = new Google_Service_Oauth2($client);
    $user = $service->userinfo->get();
} else {
    header('Location: dashboard.php');
    exit;
}





/**
 * OpenAI APIを叩いて、結果を返す 
 * 
 * @param string $text
 * @return string
 */
function getSummaryFromOpenAI($text)
{
    $api_key = $_ENV['OPEN_AI_KEY'];
    $url = 'https://api.openai.com/v1/chat/completions';
    // リクエストヘッダー
    $headers = array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key
    );

    // リクエストボディ
    $data = array(
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ["role" => "system", "content" => "200文字で要約して"],
            ['role' => 'user', 'content' => "$text"],
        ],
        'max_tokens' => 500,
    );

    // cURLを使用してAPIにリクエストを送信 
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    curl_close($ch);

    // 結果をデコード
    $result = json_decode($response, true);
    $result_message = $result["choices"][0]["message"]["content"];
    // var_dump($result_message);

    // 結果を出力 
    return $result_message;
}





if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $text = $_POST['text'];
    $result = getSummaryFromOpenAI($text);

    if ($result === FALSE) {
        // Handle error
        echo json_encode(['error' => 'There was an issue with the request']);
        http_response_code(500);
    } else {

        $service = new Google_Service_Slides($client);

        // 新規のプレゼンテーションを作成
        $presentation = new Google_Service_Slides_Presentation(array(
            'title' => 'Your Great Presentation'
        ));

        // Create a new slide
        $createdPresentation = $service->presentations->create($presentation);
        $presentationId = $createdPresentation->presentationId;

        // リクエストを準備（新しいスライドの作成）
        $requests = array();
        $requests[] = new Google_Service_Slides_Request(array(
            'createSlide' => array(
                'objectId' => 'slideId',
                'insertionIndex' => '1',
                'slideLayoutReference' => array(
                    'predefinedLayout' => 'BLANK'
                )
            )
        ));

        // スライド作成リクエストの実行
        $batchUpdateRequest = new Google_Service_Slides_BatchUpdatePresentationRequest(array(
            'requests' => $requests
        ));
        $response = $service->presentations->batchUpdate($presentationId, $batchUpdateRequest);

        // 作成したスライドのIDを取得
        $createdSlideId = $response->getReplies()[0]->getCreateSlide()->getObjectId();

        // テキストボックスの追加とテキストの設定
        $requests = array();
        $requests[] = new Google_Service_Slides_Request(array(
            'createShape' => array(
                'objectId' => 'myTextBox_01',
                'shapeType' => 'TEXT_BOX',
                'elementProperties' => array(
                    'pageObjectId' => $createdSlideId,
                    'size' => array(
                        'height' => array('magnitude' => 100, 'unit' => 'PT'),
                        'width' => array('magnitude' => 600, 'unit' => 'PT'),
                    ),
                    'transform' => array(
                        'scaleX' => 1,
                        'scaleY' => 1,
                        'translateX' => 350,
                        'translateY' => 100,
                        'unit' => 'PT'
                    )
                )
            )
        ));
        // // テキストボックスへのテキストの挿入
        $requests[] = new Google_Service_Slides_Request(array(
            'insertText' => array(
                'objectId' => 'myTextBox_01',
                'insertionIndex' => 0,
                'text' => $result
            )
        ));

        // // テキストボックス追加リクエストの実行
        $batchUpdateRequest = new Google_Service_Slides_BatchUpdatePresentationRequest(array(
            'requests' => $requests
        ));
        $response = $service->presentations->batchUpdate($presentationId, $batchUpdateRequest);


        echo 'スライドを作成しました！';
    }
}
