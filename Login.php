<?php

const DATABASE_USER = "data/user.txt";
const DATABASE_PASSWORD = "data/password.txt";

function getUser()
{
    $userBase = file(DATABASE_USER, FILE_IGNORE_NEW_LINES);
    $userData = [];
    foreach ($userBase as $line) {
        list ($id, $phone, $email, $login, $name) = explode(", ", $line);
        $userData[] = [
            "id" => $id,
            "phone" => $phone,
            "email" => $email,
            "login" => $login,
            "name" => $name
        ];
    }
    return $userData;
}

function getPassword()
{
    $passwordBase = file(DATABASE_PASSWORD, FILE_IGNORE_NEW_LINES);
    $passwordData = [];
    foreach ($passwordBase as $line) {
        list ($id, $password) = explode(", ", $line);
        $passwordData[] = [
            "id" => $id,
            "password" => $password,
        ];
    }
    return $passwordData;
}

function chekData($checkLogin, $password)
{
    $passwordBase = getPassword();
    $userBase = getUser();
    foreach ($userBase as $user) {
        if ($user["phone"] || $user["email"] === $checkLogin)
        {
            foreach ($passwordBase as $pb) {
                if ($pb["id"] === $user["id"] && $pb["password"] === $password) {
                    return $user["name"];
                } else {
                    return false;
                }
            }
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $phoneOrEmail = trim($_POST["phoneOrEmail"]);
    $password = $_POST["password"];
    $userName = chekData($phoneOrEmail, $password);
    if (!empty($userName) == false) {
        echo "Неверный логин и пароль";
    } else {
        echo "Вы вошли как {$userName}";
    }
}
   




?>

<form method="POST">
    <a href="Index.php"><--Назад</a><br>
    <a href="Registration.php"><--Регистрация</a><br>
    Телефон или почта: <input type="text" name="phoneOrEmail" required><br>
    Пароль: <input type="password" name="password" required><br>
    <input type="submit" value="Войти">
</form>