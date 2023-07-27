<?php

require_once __DIR__ . '/../lib/checkRequestMethod.php';
require_once __DIR__ . '/../config/init.php';
require_once __DIR__ . '/../config/google_auth.php';


/**
 * codeパラメータがあれば、アクセストークンを取得してセッションに保存 
 */
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $_SESSION['access_token'] = $token;

    header('Location: ../dashboard.php');
    exit;
} else {
    // 何らかの理由で認証コードが提供されていない場合は、エラーメッセージを表示するなどのエラーハンドリングを行います。
    header('Location: ../index.php');
    exit;
}
