<?php
$client = new Google_Client();
$client->setClientId($_ENV['YOUR_CLIENT_ID']);
$client->setClientSecret($_ENV['YOUR_CLIENT_SECRET']);
$client->setRedirectUri($_ENV['YOUR_REDIRECT_URL']);
$client->addScope("email");
$client->addScope("profile");