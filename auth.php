<?php
$oConnection = new mysqli('localhost', 'root', '', 'todo_list_task');
$oQueryWithLogin = $oConnection->query('select login from users where login = \'' . $_POST['login'] . '\'');
if ($aResult = $oQueryWithLogin->fetch_assoc()) {
    $oQueryWithLoginAndPassword = $oConnection->query('select login from users where login = \'' . $_POST['login'] . '\' and password = \'' . hash('md5', $_POST['password']) . '\'');
    if ($aResult = $oQueryWithLoginAndPassword->fetch_assoc()) {
        $sSessionCookie = bin2hex(random_bytes(5));
        setcookie('session', $sSessionCookie);
        $oConnection->query('update users set session_id = \'' . $sSessionCookie . '\' where login = \'' . $_POST['login'] . '\' and password = \'' . hash('md5', $_POST['password']) . '\'');
        header("Location: tasks.html");
    } else {
        echo 'Пользователь ввел неверный пароль';
    }
} else {
    echo 'Пользователя с таким логином не существует';
}
$oConnection->close();
