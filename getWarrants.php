<?php
// Ganti dengan kredensial akunmu
$username = 'Dean Mcdavian';
$password = 'uverworld12345';
$response = curl_exec($ch);
file_put_contents('login_debug.html', $response);  // simpan HTML login response


// URL yang relevan
$loginUrl = 'https://police-state.site/ucp.php?mode=login';
$targetUrl = 'https://police-state.site/viewforum.php?f=136';

// Temp file untuk menyimpan cookie sesi login
$cookieFile = tempnam(sys_get_temp_dir(), 'cookie');

// Step 1: Login
$loginData = http_build_query([
    'username' => $username,
    'password' => $password,
    'login' => 'Login', // Sesuai dengan tombol di form login
    'autologin' => 'on',
    'redirect' => './index.php',
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $loginUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $loginData);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_exec($ch);

// Step 2: Akses halaman target setelah login
curl_setopt($ch, CURLOPT_URL, $targetUrl);
$html = curl_exec($ch);
curl_close($ch);

// Step 3: Parsing HTML (regex kasar, sesuaikan jika perlu)
$matches = [];
preg_match_all('/<a href="viewtopic\.php\?t=(\d+)" class="topictitle">(.+?)<\/a>.*?<span class="topictitle">.*?<\/span>.*?<span class=".*?">(.+?)<\/span>/s', $html, $results, PREG_SET_ORDER);

$data = [];
foreach ($results as $r) {
    $data[] = [
        "name" => strip_tags(html_entity_decode($r[2])),
        "date" => strip_tags($r[3]),
        "link" => "https://police-state.site/viewtopic.php?t=" . $r[1],
        "risk" => "Standard Risk"
    ];
}

// Output JSON
header('Content-Type: application/json');
echo json_encode($data);
