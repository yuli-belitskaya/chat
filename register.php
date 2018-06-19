<?php
session_start();
require_once 'db.php';

if(isset($_POST['session']))
{
    $err = array();

    $vkId = intval(mysqli_real_escape_string($dbLink, $_POST['session']['user']['id']));
    $login = mysqli_real_escape_string($dbLink, $_POST['session']['user']['first_name']);
    $login = iconv('utf-8', 'windows-1251', $login);
    $hash = md5($vkId);

    if (!$vkId) {
        $err = 'Неверный id вконтакте';
    }

    # проверяем, не сущестует ли пользователя с таким именем
    $query = mysqli_query($dbLink, "SELECT * FROM users WHERE vk_id=".$vkId);
    $userdata = mysqli_fetch_array($query);

    if($userdata) {
        $_SESSION['user_id'] = $userdata['id'];
        setcookie('id', $userdata['id'], time()+60*60*24*30, '/');
        setcookie('hash', $userdata['hash'], time()+60*60*24*30, '/');
        echo json_encode(['id' => $userdata['id'], 'hash' => $userdata['hash']]);
        header('Location: /');
        exit();
    }

    # Если нет ошибок, то добавляем в БД нового пользователя
    if(count($err) == 0)
    {
        $sql = "INSERT INTO users (vk_id, login, hash) VALUES ('{$vkId}', '{$login}', '{$hash}')";
        mysqli_query($dbLink, $sql);

        $id = mysqli_insert_id($dbLink);
        if (!$id) {
            $err[] = 'Ошибка записи в базу данных, свяжитесь с администратором';
        }
        $_SESSION['user_id'] = $id;

        setcookie('id', $id, time()+60*60*24*30, '/');
        setcookie('hash', $hash, time()+60*60*24*30, '/');
        echo json_encode(['id' => $id, 'hash' => $hash]);
        header('Location: /');
        exit();
    }

    http_response_code(400);
    echo json_encode(['errors' => $err]);
}
