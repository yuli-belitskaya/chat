<?php
session_start();
require_once 'db.php';

if (!isset($_REQUEST['id']) and isset($_REQUEST['hash'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized request']);
    exit;
}

$query = mysqli_query($dbLink, "SELECT * FROM users WHERE id=".intval($_REQUEST['id'])." LIMIT 1");
$userdata = mysqli_fetch_assoc($query);

if(!$userdata || ($userdata['hash'] !== $_REQUEST['hash']) || ($userdata['id'] !== $_REQUEST['id']))
{
    setcookie("id", "", time() - 3600*24*30*12, "/");
    setcookie("hash", "", time() - 3600*24*30*12, "/");

    http_response_code(403);
    echo json_encode(['error' => 'Wrong user id and hash']);
    exit;
}

$userdata['login'] = iconv('windows-1251', 'utf-8', $userdata['login']);

$_SESSION['user_id'] = $userdata['id'];
echo json_encode(['status' => 'ok', 'user_name' => $userdata['login']]);
