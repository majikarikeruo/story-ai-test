<?php
require_once __DIR__ . '/../lib/checkRequestMethod.php';


session_start();

/**
 * Delete Access Token
 */
if (isset($_SESSION['access_token'])) {
    unset($_SESSION['access_token']); // アクセストークンを削除
}

header('Location: ../index.php');
