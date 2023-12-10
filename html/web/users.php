<?php
require_once __DIR__ . '/../repository/repository.php';
// Проверяем, авторизован ли пользователь и его роль
session_start();


if (!isset($_SESSION['id']) || (isset($_SESSION['role']) && $_SESSION['role'] !== 'superadmin')) {
    // Перенаправляем на страницу авторизации
    header("Location: index.php");
    exit;
}
// Обработка данных формы выхода
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == 'logout') {
    logoutUser();
}


// Обработка данных формы добавления
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == 'add') {
    $login = $_POST['login'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $result = addUser($login, $password, $role);

    if ($result) {
        header("Location: users.php");
        exit;
    } else {
        $errorMessage = "Error adding user.";
    }
}

// Обработка данных формы редактирования
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == 'edit') {
    $userId = $_POST['user_id'];

    // Получаем данные пользователя для редактирования
    $userToEdit = getUserById($userId);

    // Обновление данных после отправки формы
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == 'edit') {
        $userId = $_POST['user_id'];
        $login = $_POST['login'];
        $password = $_POST['password'];
        $role = $_POST['role'];

        // Обновление данных в базе данных
        $result = updateUser($userId, $login, $password, $role);

        if ($result) {
            // Редирект после успешного обновления
            header("Location: users.php");
            exit;
        } else {
            // Обработка ошибки обновления
            $errorMessage = "Error updating user.";
        }
    }

    // Отображаем форму редактирования с заполненными данными
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit User</title>
        <link rel="stylesheet" href="styles2.css">
    </head>
    <body>

    <div class="container">
        <h2>Edit User</h2>
        <form id="editUserForm" method="post">
            <label for="login">Login:</label>
            <input type="text" id="login" name="login" value="<?php echo $userToEdit['login']; ?>" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" value="<?php echo $userToEdit['password']; ?>" required>
            <br>
            <label for="role">Role:</label>
            <select id="role" name="role">
                <option value="reader" <?php echo ($userToEdit['role'] == 'reader') ? 'selected' : ''; ?>>Reader</option>
                <option value="admin" <?php echo ($userToEdit['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                <option value="superadmin" <?php echo ($userToEdit['role'] == 'superadmin') ? 'selected' : ''; ?>>Superadmin</option>
            </select>
            <br>
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
            <input type="submit" value="Update User">
        </form>
    </div>

    </body>
    </html>
    <?php
    exit; // Завершаем выполнение скрипта после отображения формы редактирования
}

// Обработка данных формы удаления
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == 'delete') {
    $userId = $_POST['user_id'];

    $result = deleteUser($userId);

    if ($result) {
        header("Location: users.php");
        exit;
    } else {
        $errorMessage = "Error deleting user.";
    }
}

// Получение списка всех пользователей
$users = getAllUsers();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Page</title>
    <link rel="stylesheet" href="styles2.css">
</head>
<body>

<div class="container">
    <h2>Users List</h2>
    <table border='1'>
        <tr>
            <th>User ID</th>
            <th>Login</th>
            <th>Password</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        <?php
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . $user['user_id'] . "</td>";
            echo "<td>" . $user['login'] . "</td>";
            echo "<td>" . $user['password'] . "</td>";
            echo "<td>" . $user['role'] . "</td>";
            echo "<td>
                    <form method='post' style='display:inline;'>
                        <input type='hidden' name='action' value='edit'>
                        <input type='hidden' name='user_id' value='{$user['user_id']}'>
                        <input type='submit' value='Edit'>
                    </form>
                    <form method='post' style='display:inline;'>
                        <input type='hidden' name='action' value='delete'>
                        <input type='hidden' name='user_id' value='{$user['user_id']}'>
                        <input type='submit' value='Delete'>
                    </form>
                </td>";
            echo "</tr>";
        }
        ?>
    </table>

    <!-- Форма для добавления пользователя -->
    <h2>Add User</h2>
    <form id="addUserForm" method="post">
        <label for="login">Login:</label>
        <input type="text" id="login" name="login" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <label for="role">Role:</label>
        <select id="role" name="role">
            <option value="reader">Reader</option>
            <option value="admin">Admin</option>
            <option value="superadmin">Superadmin</option>
        </select>
        <br>
        <input type="hidden" name="action" value="add">
        <input type="submit" value="Add User">
    </form>


     <!-- Форма для выхода из аккаунта -->
     <form id="logoutForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="position: fixed; top: 10px; right: 10px;">
        <input type="hidden" name="action" value="logout">
        <input type="submit" value="Logout" style="background-color: yellow; padding: 10px 20px; font-size: 16px; border: none; cursor: pointer; color: black;">
    </form>

    <?php
    echo '<button onclick="window.location.href=\'index.php\'" class="button">назад</button>';
    if (isset($errorMessage)) {
        echo "<div class='error'>$errorMessage</div>";
    }
    ?>
</div>

</body>
</html>
