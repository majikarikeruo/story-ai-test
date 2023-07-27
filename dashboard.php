<?php
require_once __DIR__ . '/config/init.php';
require_once __DIR__ . '/config/google_auth.php';


/**
 * access_tokenがない場合はログインしていないのでindex.phpにリダイレクト
 * 下記の場合、user情報を取得するためにif elseで分けている 
 */
if (isset($_SESSION['access_token'])) {
    $client->setAccessToken($_SESSION['access_token']);
    $service = new Google_Service_Oauth2($client);
    $user = $service->userinfo->get();
} else {
    header('Location: index.php');
    exit;
}
?>
<p>ただいまログイン中。<?= $user->name; ?>さん、こんにちは！</p>
<form action="auth/logout.php" method="POST">
    <button>ログアウト</button>
</form>


<div>
    <form action="" method="POST">
        <button>Googleスライドを作成するぞ</button>
    </form>
</div>