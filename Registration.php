<?php

class User
{
    const DATABASE = "data/base.txt";

    public static function checkFile(): void
    {
        if (!file_exists(self::DATABASE)) {
            file_put_contents(self::DATABASE, "");
        }
    }

    public static function inString($string): string
    {
        return mb_strtolower(trim($string));
    }

    public static function register($name, $login, $phone, $email, $password, $passwordCheck): string
    {
        if ($password !== $passwordCheck) {
            return "Пароли не совпадают";
        }
        if (empty($name) || empty($login) || empty($phone) || empty($email) || empty($password)) {
            return "Заполните все поля";
        }
        $users = self::getAllUsers();
        foreach ($users as $user) {
            if (self::inString($user['login']) === self::inString($login) ||
                self::inString($user['phone']) === self::inString($phone) ||
                self::inString($user['email']) === self::inString($email)) {
                return "Пользователь уже существует";
            }
        }
        $time = date("Y-m-d H:i:s");
        $id = count($users) + 1;
        file_put_contents(self::DATABASE, "{$time},{$id},{$name},{$login},{$phone},{$email},{$password}\n", FILE_APPEND);
        return "Вы зарегистрированы";
    }

    public static function getAllUsers(): array
    {
        $userBase = file(self::DATABASE, FILE_IGNORE_NEW_LINES);
        $userData = [];

        foreach ($userBase as $line) {
            $data = explode(",", trim($line));
            if (count($data) === 7) {
                list($time, $id, $name, $login, $phone, $email, $password) = $data;
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
        }
        return $userData;
    }
}

User::checkFile();

$result = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"] ?? '';
    $login = User::inString($_POST["login"] ?? '');
    $phone = User::inString($_POST["phone"] ?? '');
    $email = User::inString($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';
    $passwordCheck = $_POST["passwordCheck"] ?? '';
    $result = User::register($name, $login, $phone, $email, $password, $passwordCheck);
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

<?php if ($result): ?>
    <p><?php echo htmlspecialchars($result); ?></p>
<?php endif; ?>
