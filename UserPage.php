<?php
session_start();

class User
{
    const DATABASE = "data/base.txt";

    public static function inString(string $string): string
    {
        return mb_strtolower(trim($string));
    }

    public static function getAllUsers(): array
    {
        $userBase = file(self::DATABASE, FILE_IGNORE_NEW_LINES);
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

    public static function updateUser($newData): string
    {
        $userData = self::getAllUsers();
        $currentUser = array_filter($userData, fn($user) => $user['id'] == $_SESSION["user"]["id"]);
        if (!$currentUser) {
            return "Пользователь не найден.";
        }
        $isUnique = true;
        foreach ($userData as $user) {
            if ($user["login"] === $newData["login"] && $user["login"] !== $_SESSION["user"]["login"]) {
                $isUnique = false;
                return "Логин занят.";
            }
            if ($user["email"] === $newData["email"] && $user["email"] !== $_SESSION["user"]["email"]) {
                $isUnique = false;
                return "Почта занята";
            }
            if ($user["phone"] === $newData["phone"] && $user["phone"] !== $_SESSION["user"]["phone"]) {
                $isUnique = false;
                return "Телефон занят";
            }
        }
        if ($isUnique) {
            foreach ($userData as &$user) {
                if ($user["id"] == $_SESSION["user"]["id"]) {
                    if (self::inString($_POST["old_password"]) !== $user["password"]) {
                        return "Неверный старый пароль.";
                    }
                    $user["name"] = $newData["name"] ?? $user["name"];
                    $user["phone"] = $newData["phone"] ?? $user["phone"];
                    $user["email"] = $newData["email"] ?? $user["email"];
                    $user["login"] = $newData["login"] ?? $user["login"];
                    $user["password"] = $newData["password"] != "" ? $newData["password"] : $user["password"];
                }
            }
            file_put_contents(self::DATABASE, implode(PHP_EOL, array_map(function ($user) {
                return implode(",", $user);
            }, $userData)));
            return "Данные изменены.";
        }
        return "Ошибка при обновлении данных.";
    }
}

if (!isset($_SESSION["user"])) {
    header("Location: Index.php");
    exit();
}

$currentUser = $_SESSION["user"];
$name = $currentUser["name"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newData = [
        'name' => $_POST["name"],
        'phone' => $_POST["phone"],
        'email' => $_POST["email"],
        'login' => $_POST["login"],
        'password' => $_POST["password"] ?? ""
    ];
    $result = User::updateUser($newData);
    echo $result;
}

?>

<h1><?php echo "{$name}, можете внести изменения" ?></h1>
<a href="index.php">На главную</a><br>
<a href="Exit.php">Выйти</a><br>

<form method="POST">
    Имя: <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required><br>
    Логин: <input type="text" name="login" value="<?php echo htmlspecialchars($currentUser["login"]); ?>" required><br>
    Телефон: <input type="text" name="phone" value="<?php echo htmlspecialchars($currentUser["phone"]); ?>" required><br>
    Email: <input type="email" name="email" value="<?php echo htmlspecialchars($currentUser["email"]); ?>" required><br>
    Старый пароль: <input type="password" name="old_password" required><br>
    Новый пароль: <input type="password" name="password" placeholder="Введите новый пароль"><br>
    <input type="submit" value="Сохранить изменения">
</form>
