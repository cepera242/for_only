<?php

const DATABASE = "data/base.txt";

function checkFile($file)
 {
    if (!file_exists($file)) {
        file_put_contents($file, "");
    }
}

checkFile(DATABASE);

function inString($string)
{
    return mb_strtolower(trim($string));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") 
{
    $name = $_POST["name"];
    $login = inString($_POST["login"]);
    $phone = inString($_POST["phone"]);
    $email = inString($_POST["email"]);
    $password = trim($_POST["password"]);
    $passwordCheck = $_POST["passwordCheck"];
    if ($password !== $passwordCheck) {
        echo "Пароли не совпадают";
        return;
    }
    if (empty($name) ||
    empty($login) ||
    empty($phone) ||
    empty($email) ||
    empty($password) ||
    empty($passwordCheck)) {
        echo "Заполните все поля";
        return;
    }
    $user = file(DATABASE, FILE_IGNORE_NEW_LINES);
    foreach ($user as $line) {
        list($dbTime, $dbId, $dbName, $dblogin, $dbPhone, $dbEmail) = explode(",", inString($line));
        if (inString($dblogin) === inString($login)) {
            echo "Пользователь уже зарегистрирован";
            return;
        } elseif (inString($dbPhone) === inString($phone)) {
            echo "Пользователь уже зарегистрирован";
            return;
        } elseif (inString($dbEmail) === inString($email)) {
            echo "Пользователь уже зарегистрирован";  
        }
    }
    $time = date("Y-m-d H:i:s");
    $id = count($user) + 1;
    file_put_contents(DATABASE, "{$time},{$id},{$name},{$login},{$phone},{$email},{$password}\n", FILE_APPEND);
    echo "Вы успешно зарегистрированы";
}
?>

<form method="POST">
    <a href="Index.php"><--Назад</a><br>
    <a href="Login.php"><--Авторизация</a><br>
    Имя: <input type="text" name="name" required><br>
    Логин: <input type="text" name="login" required><br>
    Телефон: <input type="text" name="phone" required><br>
    Почта: <input type="email" name="email" required><br>
    Пароль: <input type="password" name="password" required><br>
    Повторите пароль: <input type="password" name="passwordCheck" required><br>
    <input type="submit" value="Зарегистрироваться">
</form>
