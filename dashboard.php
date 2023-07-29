<?php
require_once __DIR__ . '/config/init.php';
require_once __DIR__ . '/config/google_auth.php';


/**
 * access_tokenがない場合はログインしていないのでindex.phpにリダイレクト
 * 下記の場合、user情報を取得するためにif elseで分けている 
 */


try {
    if (isset($_SESSION['access_token'])) {
        $client->setAccessToken($_SESSION['access_token']);
        $service = new Google_Service_Oauth2($client);
        $user = $service->userinfo->get();
    } else {
        throw new Exception('No access token');
    }
} catch (Exception $e) {
    unset($_SESSION['access_token']);
    header('Location: index.php');
    exit;
}




?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>データ登録</title>
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>

    <header>

    </header>


    <p>ただいまログイン中。<?= $user->name; ?>さん、こんにちは！</p>
    <form action="auth/logout.php" method="POST">
        <button>ログアウト</button>
    </form>







    <div>
        <h3>あなたの事業を一緒に考えよう！</h3>
    </div>

    <form id="chat-form" autocomplete="off">
        <input type="text" id="chat-input" placeholder="Enterで送信" />
        <div id="chat-window">
            <pre id="chat-history"></pre>
    </form>
    </div>

    <div class="wordcount">
        <div class="length">0</div>
        <div>/250文字</div>
    </div>

    <button id=ref>スライド生成</button><br>
    <textArea name="story" id=storyarea rows="4" cols="40"></textArea>

    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="./js/script.js"></script>

</body>

</html>