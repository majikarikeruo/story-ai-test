<?php

require_once __DIR__ . '/../lib/checkRequestMethod.php';
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../config/google_auth.php';


/**
 * ユーザーをGoogleの認証ページにリダイレクト
 */
$authUrl = $client->createAuthUrl();
header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
exit;
