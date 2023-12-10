<?php
require_once __DIR__ . '/../repository/repository.php';

// Проверяем, авторизован ли пользователь
session_start();
if (!isset($_SESSION['id'])) {
    // Пользователь не авторизован, перенаправляем на страницу авторизации
    header("Location: login.php");
    exit;
}

// Получаем ID текущего пользователя и его роль из сессии
$userId = $_SESSION['id'];
$userRole = $_SESSION['role'];

// Обработка данных формы выхода
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == 'logout') {
    logoutUser();
}

// Обработка данных формы добавления
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == 'add') {
    $title = $_POST['title'];
    $link = $_POST['link'];
    $description = $_POST['description'];

    $result = addBook($userId, $title, $link, $description);

    if ($result) {
        header("Location: books_reader.php");
        exit;
    } else {
        $errorMessage = "Error adding book.";
    }
}

// Обработка данных формы редактирования
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == 'edit') {
    // Если book_id передан, отображаем форму редактирования
    if (isset($_POST['book_id'])) {
        $bookId = $_POST['book_id'];

        // Получаем данные книги для редактирования
        $bookToEdit = getBookById($bookId);

        // Отображаем форму редактирования с заполненными данными
        include 'edit_form.php'; // Файл с формой редактирования
        exit; // Завершаем выполнение скрипта после отображения формы редактирования
    } else { // Иначе, если book_id не передан, обрабатываем данные после отправки формы
        $bookId = $_POST['edited_book_id'];
        $title = $_POST['title'];
        $link = $_POST['link'];
        $description = $_POST['description'];
        $readerId = $userId;

        // Обновление данных в базе данных
        $result = updateBook($bookId, $readerId, $title, $link, $description);

        if ($result) {
            // Редирект после успешного обновления
            header("Location: books_reader.php");
            exit;
        } else {
            // Обработка ошибки обновления
            $errorMessage = "Error updating book.";
        }
    }
}

// Обработка данных формы удаления
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == 'delete') {
    $bookId = $_POST['book_id'];

    $result = deleteBook($bookId);

    if ($result) {
        header("Location: books_reader.php");
        exit;
    } else {
        $errorMessage = "Error deleting book.";
    }
}

// Получение списка всех книг
$books = getBooksByUserId($userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books Page</title>
    <link rel="stylesheet" href="styles2.css">
</head>
<body>

<div class="container">
    <h2>Books List</h2>
    <table border='1'>
        <tr>
            <th>Book ID</th>
            <th>Reader ID</th>
            <th>Title</th>
            <th>Link</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        <?php
        foreach ($books as $book) {
            echo "<tr>";
            echo "<td>" . $book['book_id'] . "</td>";
            echo "<td>" . $book['reader_id'] . "</td>";
            echo "<td>" . $book['title'] . "</td>";
            echo "<td><a href='" . $book['link'] . "' target='_blank'>" . $book['link'] . "</a></td>";
            echo "<td>" . $book['description'] . "</td>";
            echo "<td>
                    <form method='post' style='display:inline;'>
                        <input type='hidden' name='action' value='edit'>
                        <input type='hidden' name='book_id' value='{$book['book_id']}'>
                        <input type='submit' value='Edit'>
                    </form>
                    <form method='post' style='display:inline;'>
                        <input type='hidden' name='action' value='delete'>
                        <input type='hidden' name='book_id' value='{$book['book_id']}'>
                        <input type='submit' value='Delete'>
                    </form>
                </td>";
            echo "</tr>";
        }
        ?>
    </table>

    <!-- Форма для добавления книги -->
    <h2>Add Book</h2>
    <form id="addBookForm" method="post">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>
        <br>
        <label for="link">Link:</label>
        <input type="text" id="link" name="link" required>
        <br>
        <label for="description">Description:</label>
        <input type="text" id="description" name="description">
        <br>
        <input type="hidden" name="action" value="add">
        <input type="submit" value="Add Book">
    </form>
    
    <!-- Форма для выхода из аккаунта -->
    <form id="logoutForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="position: fixed; top: 10px; right: 10px;">
        <input type="hidden" name="action" value="logout">
        <input type="submit" value="Logout" style="background-color: yellow; padding: 10px 20px; font-size: 16px; border: none; cursor: pointer; color: black;">
    </form>

    <?php
    if (isset($errorMessage)) {
        echo "<div class='error'>$errorMessage</div>";
    }
    ?>
</div>

</body>
</html>
