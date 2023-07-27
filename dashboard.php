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



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = $_ENV['GAS_APP_URL'];

    $ch = curl_init($url);

    $data = array();
    $payload = json_encode($data);

    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    curl_close($ch);

    $responseData = json_decode($result, true);

    if ($responseData && $responseData['success']) {
        echo $responseData['message'];
    } else {
        echo 'スライドがうまく生成されませんでした。';
    }
}

?>

<p>ただいまログイン中。<?= $user->name; ?>さん、こんにちは！</p>
<form action="auth/logout.php" method="POST">
    <button>ログアウト</button>
</form>


<div>
    <form action="dashboard.php" method="POST">
        <button>Googleスライドを作成するぞ</button>
    </form>
</div>