<?php
require_once __DIR__ . '/../repository/repository.php';

// Обработка данных формы
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    if ($_POST["action"] == "login") {
        $result = authenticateUser($username, $password);

        if ($result === "1") {
            // Получаем информацию о пользователе
            $user = getUserByUsername($username);

            // Стартуем сессию
            session_start();

            // Сохраняем информацию о пользователе в сессии
            $_SESSION['id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];

            // Редирект на соответствующую страницу в зависимости от роли
            if ($_SESSION['role'] == 'reader') {
                header("Location: books_reader.php");
            } else{
                header("Location: index.php");
            }

            exit;
        } else {
            // Вывод сообщения об ошибке
            $errorMessage = $result;
        }
    } else {
        // Другие действия для других action, если необходимо
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="styles.css"> <!-- Подключение стилей -->
</head>
<body>

<div class="container">
    <h2>Login Form</h2>
    <form id="loginForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="hidden" name="action" value="login">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <input class="button1" type="submit" value="Login" style="background-color: green; color: white; padding: 10px 20px; border: none; cursor: pointer; font-size: 16px;">
    </form>

    <div id="errorMessage" class="error">
        <?php
        // Вывод сообщения об ошибке, если оно существует
        if (isset($errorMessage)) {
            echo $errorMessage;
        }
        ?>
    </div>
</div>

</body>
</html>
