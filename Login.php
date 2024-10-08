<?php

//const DATABASE_USER = "data/user.txt";
//const DATABASE_PASSWORD = "data/password.txt";

function checkFile($file) {
    if (!file_exists($file)) {
        file_put_contents($file, "");
    }
}

checkFile(DATABASE_USER);
checkFile(DATABASE_PASSWORD);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phoneOrEmail = $_POST['phoneOrEmail'];
    $password = $_POST['password'];

    $users = file(DATABASE_USER, FILE_IGNORE_NEW_LINES);
    $passwords = file(DATABASE_PASSWORD, FILE_IGNORE_NEW_LINES);
    $id = -1;

    foreach ($users as $user) {
        list($dbId, $dbPhone, $dbEmail) = explode(',', $user);
        if ($dbPhone === $phoneOrEmail || $dbEmail === $phoneOrEmail) {
            $id = $dbId;
            break;
        }
    }

    if ($id === -1) {
        echo "Пользователь не найден.";
        exit;
    }

    foreach ($passwords as $passwd) {
        list($dbId, $dbPassword) = explode(',', $passwd);
        if ($dbId == $id && $dbPassword === $password) {
            echo "Авторизация успешна. Ваш ID: $id";
            // Здесь можно установить куку или что-то еще
            exit;
        }
    }

    echo "Неверный пароль.";
}
?>

<form method="POST">
    Телефон или почта: <input type="text" name="phoneOrEmail" required><br>
    Пароль: <input type="password" name="password" required><br>
    <input type="submit" value="Войти">
</form>