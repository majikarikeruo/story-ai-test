<?php
require_once 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


session_start();

$client = new Google_Client();
$client->setClientId($_ENV['YOUR_CLIENT_ID']);
$client->setClientSecret($_ENV['YOUR_CLIENT_SECRET']);
$client->setRedirectUri($_ENV['YOUR_REDIRECT_URL']);
$client->addScope("email");
$client->addScope("profile");

var_dump($_SESSION['access_token']);
if (isset($_SESSION['access_token'])) {
    $client->setAccessToken($_SESSION['access_token']);
    $service = new Google_Service_Oauth2($client);
    $user = $service->userinfo->get();

    // ユーザ情報を表示
    echo "<pre>";
    var_dump($user);
    echo "</pre>";
}
?>
<p>ただいまログイン中。<?= $user->name; ?>さん、こんにちは！</p>
<form action="logout.php" method="POST">
    <button>ログアウト</button>
</form>