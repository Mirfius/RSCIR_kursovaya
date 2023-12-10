<?php
require_once __DIR__ . '/../repository/repository.php';

session_start();

$userId = $_SESSION['id'];
$userRole = $_SESSION['role'];


if (!isset($_SESSION['id']) || (isset($_SESSION['role']) && $_SESSION['role'] == 'reader')) {
    // Пользователь не авторизован или имеет роль читателя, перенаправляем на страницу авторизации
    header("Location: books_reader.php");
    exit;
}

$mysqli = new mysqli("db", "user", "password");
$mysqli->set_charset("latin1");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}


// Обработка данных формы выхода
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == 'logout') {
    logoutUser();
}



// Получаем роль пользователя из сессии
$userRole = $_SESSION['role'];

// Вывод списка доступных баз данных
$queryDatabases = "SHOW DATABASES";
$resultDatabases = $mysqli->query($queryDatabases);

if ($resultDatabases) {
    echo "<h2>List of Databases:</h2>";
    echo "<ul>";
    while ($row = $resultDatabases->fetch_array()) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";
    $resultDatabases->free();
} else {
    echo "Error: " . $mysqli->error;
}

// Вывод таблиц в зависимости от роли
$selectedDatabase = $mysqli->select_db("appDB");

if ($selectedDatabase) {
    echo "<h2>Tables in appDB:</h2>";
    echo "<ul>";

    $queryTables = "SHOW TABLES";
    $resultTables = $mysqli->query($queryTables);

    if ($resultTables) {
        while ($row = $resultTables->fetch_array()) {
            $tableName = $row[0];

            // Проверяем роль пользователя и имя таблицы
            if (($userRole == 'admin' && $tableName == 'books') || ($userRole == 'superadmin') || ($userRole == 'admin' && $tableName == 'users')) {
                echo "<li><h3>$tableName</a></h3>";
                echo "<table border='1'>";

                if ($tableName == 'users' && $userRole == 'admin') {
                    // Обычный админ видит только user_id, username, и role, но не password
                    $queryTableData = "SELECT user_id, login, role FROM $tableName";
                } else {
                    // Для других таблиц или ролей выбираем все поля
                    $queryTableData = "SELECT * FROM $tableName";
                }

                $resultTableData = $mysqli->query($queryTableData);

                if ($resultTableData) {
                    if ($resultTableData->num_rows > 0) {
                        echo "<tr>";
                        $firstRow = $resultTableData->fetch_assoc();
                        foreach ($firstRow as $key => $value) {
                            echo "<th>$key</th>";
                        }
                        echo "</tr>";

                        echo "<tr>";
                        foreach ($firstRow as $value) {
                            echo "<td>$value</td>";
                        }
                        echo "</tr>";

                        while ($row = $resultTableData->fetch_assoc()) {
                            echo "<tr>";
                            foreach ($row as $value) {
                                echo "<td>$value</td>";
                            }
                            echo "</tr>";
                        }
                    } else {
                        echo "<p>No data in this table.</p>";
                    }
                    $resultTableData->free();
                } else {
                    echo "Error: " . $mysqli->error;
                }
                echo "</table></li>";
            }
        }
        echo "</ul>";
        $resultTables->free();
    } else {
        echo "Error: " . $mysqli->error;
    }
} else {
    echo "Failed to select database appDB: " . $mysqli->error;
}


$mysqli->close();


 // Добавим HTML кнопки для перехода на страницы books_admin и users
 echo '<button onclick="window.location.href=\'books_admin.php\'" class="button">Books Admin</button>';
 echo '<button onclick="window.location.href=\'users.php\'" class="button">Users</button>';
 


?>

<!-- Форма для выхода из аккаунта -->
<form id="logoutForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="position: fixed; top: 10px; right: 10px;">
    <input type="hidden" name="action" value="logout">
    <input type="submit" value="Logout" style="background-color: yellow; padding: 10px 20px; font-size: 16px; border: none; cursor: pointer; color: black;">
</form>

