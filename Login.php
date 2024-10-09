<?php

session_start();

const DATABASE = "data/base.txt";

function inString($string)
{
    return mb_strtolower(trim($string));
}

function getData()
{
    $userBase = file(DATABASE, FILE_IGNORE_NEW_LINES);
    $userData = [];
    foreach ($userBase as $line) {
        list ($time, $id, $name, $login, $phone, $email, $password) = explode(",", trim($line));
        $userData[] = [
            "time" => $time,
            "id" => $id,
            "name" => $name,
            "login" => $login,
            "phone" => $phone,
            "email" => $email,
            "password" => $password
        ];
    }
    return $userData;
}

function checkData($phoneOrEmail, $passwordCheck)
{
    $userBase = getData();
    foreach ($userBase as $user) {
        if (($user["phone"] === inString($phoneOrEmail) ||
        $user["email"] === inString($phoneOrEmail) ||
        $user["login"] === inString($phoneOrEmail)) &&
        $user["password"] === trim($passwordCheck)) {
            return $user;
        } 
    }
    return null;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $phoneOrEmail = inString($_POST["phoneOrEmail"]);
    $passwordCheck = trim($_POST["passwordCheck"]);
    $user = checkData($phoneOrEmail, $passwordCheck);
    if ($user === null) {
        echo "Неверный логин и пароль";
    } else {  
        $_SESSION["user"] = $user;
        header("Location: UserPage.php");
        exit();
    }
}

?>

<form method="POST">
    <a href="Index.php"><--Назад</a><br>
    <a href="Registration.php"><--Регистрация</a><br>
    <a href="UserPage.php">->-Перейти на свою страницу</a><br>
    Телефон или почта: <input type="text" name="phoneOrEmail" required><br>
    Пароль: <input type="password" name="passwordCheck" required><br>
    <input type="submit" value="Войти">
</form>
