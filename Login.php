<?php

session_start();

class User
{
    const DATABASE = "data/base.txt";

    public static function inString($string): string
    {
        return mb_strtolower(trim($string));
    }

    public static function getAllUsers(): array
    {
        $userBase = file(self::DATABASE, FILE_IGNORE_NEW_LINES);
        $userData = [];
        foreach ($userBase as $line) {
            $users = explode(",", trim($line));
            if (count($users) === 7) {
                list($time, $id, $name, $login, $phone, $email, $password) = $users;
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

    public static function checkData($phoneOrEmail, $passwordCheck): mixed
    {
        $users = self::getAllUsers();
        foreach ($users as $user) {
            if (
                ($user["phone"] === $phoneOrEmail || 
                $user["email"] === $phoneOrEmail || 
                $user["login"] === $phoneOrEmail) &&
                $user["password"] === $passwordCheck) {
                return $user;
            }
        }
        return null;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $phoneOrEmail = User::inString($_POST["phoneOrEmail"]);
    $passwordCheck = $_POST["passwordCheck"];
    $user = User::checkData($phoneOrEmail, $passwordCheck);
    if ($user === null) {
        $errorMessage = "Неверный логин и пароль";
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
    Телефон или почта: <input type="text" name="phoneOrEmail" required><br>
    Пароль: <input type="password" name="passwordCheck" required><br>
    <input type="submit" value="Войти">
    
    <?php if (isset($errorMessage)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($errorMessage); ?></p>
    <?php endif; ?>
</form>
