<?php
session_start();
require_once 'db.php';

$message = $_POST['message'];
 
if($message != "")
{
    $message = iconv('utf-8', 'windows-1251', $message);
    $sql = "INSERT INTO `chat` (text, user_id) VALUES('$message', {$_SESSION['user_id']})";
    mysqli_query($dbLink, $sql);
}
 
$sql = "SELECT chat.text, users.login, chat.created_at FROM `chat` JOIN `users` on chat.user_id = users.id ORDER BY chat.created_at DESC";
$result = mysqli_query($dbLink, $sql);

$json = [];
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $row['login'] = iconv('windows-1251', 'utf-8', $row['login']);
    $row['text'] = iconv('windows-1251', 'utf-8', $row['text']);
    $json[] = $row;

}


echo json_encode(['messages' => $json]);