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

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $_SESSION['access_token'] = $token;

    // 最終的には、ログイン成功後にユーザーをリダイレクトする必要があります。
    // 以下のコードは、ユーザーをメインページにリダイレクトします。
    header('Location: dashboard.php');
    exit;
} else {
    // 何らかの理由で認証コードが提供されていない場合は、エラーメッセージを表示するなどのエラーハンドリングを行います。
    die('No code provided');
}
