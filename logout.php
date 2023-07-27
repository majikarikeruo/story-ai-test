<?php
session_start();

if (isset($_SESSION['access_token'])) {
    unset($_SESSION['access_token']); // アクセストークンを削除
}

header('Location: index.php');
