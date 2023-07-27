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
}


$authUrl = $client->createAuthUrl();
header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
