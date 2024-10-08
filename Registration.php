<?php

const DATABASE_USER = "data/user.txt";
const DATABASE_PASSWORD = "data/password.txt";

function checkFile($file)
 {
    if (!file_exists($file)) {
        file_put_contents($file, "");
    }
}

checkFile(DATABASE_USER);
checkFile(DATABASE_PASSWORD);

if ($_SERVER["REQUEST_METHOD"] === "POST") 
{
    $name = $_POST["name"];
    $login = $_POST["login"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $passwordCheck = $_POST["passwordCheck"];
    $user = file(DATABASE_USER, FILE_IGNORE_NEW_LINES);
    $storedPasswords = file(DATABASE_PASSWORD, FILE_IGNORE_NEW_LINES);
    foreach ($user as $users) {
        list($dblogin, $dbPhone, $dbEmail) = explode(", ", $users);
        if ($dblogin === $login || $dbPhone === $phone || $dbEmail === $email) {
            echo "Пользователь уже зарегистрирован";
            return;
        }
    }
    if ($password !== $passwordCheck) {
        echo "Пароли не совпадают";
        return;
    }
    $time = date("Y-m-d H:i:s");
    $id = count($user) + 1;
    file_put_contents(DATABASE_USER, "{$time}, {$id}, {$name}, {$login}, {$phone}, {$email}\n", FILE_APPEND);
    file_put_contents(DATABASE_PASSWORD, "{$time}, {$id}, {$password}\n", FILE_APPEND);
    echo "Вы успешно зарегистрированы";
}
?>

<form method="POST">
    <a href="Index.php"><--Назад</a><br>
    Имя: <input type="text" name="name" required><br>
    Логин: <input type="text" name="login" required><br>
    Телефон: <input type="text" name="phone" required><br>
    Почта: <input type="email" name="email" required><br>
    Пароль: <input type="password" name="password" required><br>
    Повторите пароль: <input type="password" name="passwordCheck" required><br>
    <input type="submit" value="Зарегистрироваться">
</form>
