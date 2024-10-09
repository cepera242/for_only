<?php
session_start();

const DATABASE = "data/base.txt";

if(!isset($_SESSION["user"])) {
    header("Location: Index.php");
    exit();
}

$name = $_SESSION["user"]["name"];
$login = $_SESSION["user"]["login"];
$phone = $_SESSION["user"]["phone"];
$email = $_SESSION["user"]["email"];
$password = $_SESSION["user"]["password"];

function inString($string)
{
    return mb_strtolower(trim($string));
}

function getData()
{
    $userBase = file(DATABASE, FILE_IGNORE_NEW_LINES);
    $userData = [];
    foreach ($userBase as $line) {
        list($time, $id, $name, $login, $phone, $email, $password) = explode(",", trim($line));
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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userData = getData();
    $oldPassword = trim($_POST["oldPassword"]);
    $newPassword = trim($_POST["password"]);
    $isUnique = true;
    if ($oldPassword !== $password) {
        echo "Неверный старый пароль.";
        $isUnique = false;
    }
    foreach ($userData as $user) {
        if ($user["login"] === $_POST["login"] && $user["login"] !== $login) {
            echo "Логин занят.";
            $isUnique = false;
        }
        if ($user["email"] === $_POST["email"] && $user["email"] !== $email) {
            echo "Почта уже используется";
            $isUnique = false;
        }
        if ($user["phone"] === $_POST["phone"] && $user["phone"] !== $phone) {
            echo "Телефон используется";
            $isUnique = false;
        }
    }
    if ($isUnique) {
        foreach ($userData as &$user) {
            if ($user["name"] === $name) {
                $user["name"] = $_POST["name"] ?? $user["name"];
                $user["phone"] = $_POST["phone"] ?? $user["phone"];
                $user["email"] = $_POST["email"] ?? $user["email"];
                $user["password"] = $newPassword;
            }
        }
        $fileContent = array_map(function ($user) {
            return implode(",", $user);
        }, $userData);
        file_put_contents(DATABASE, implode(PHP_EOL, $fileContent));
        echo "Данные изменены";
    }
}
?>

<h1><?php echo "{$name}, можете внести изменения" ?></h1>
<a href="index.php">На главную</a><br>
<a href="Exit.php">Выйти</a><br>

<form method="POST">
    Имя: <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required><br>
    Телефон: <input type="text" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required><br>
    Email: <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required><br>
    Логин: <input type="text" name="login" value="<?php echo htmlspecialchars($login); ?>" required><br>
    Старый пароль: <input type="password" name="old_password" required><br>
    Новый пароль: <input type="password" name="password" placeholder="Введите новый пароль"><br>
    <input type="submit" value="Сохранить изменения">
</form>
