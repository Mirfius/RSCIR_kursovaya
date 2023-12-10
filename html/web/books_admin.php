<?php
require_once __DIR__ . '/../repository/repository.php';

// Проверяем, авторизован ли пользователь и имеет ли он роль читателя
session_start();
if (!isset($_SESSION['id']) || (isset($_SESSION['role']) && $_SESSION['role'] == 'reader')) {
    // Пользователь не авторизован или имеет роль читателя, перенаправляем на страницу авторизации
    header("Location: books_reader.php");
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
    $readerId = $_POST['reader_id'];

    $result = addBook($readerId, $title, $link, $description);

    if ($result) {
        header("Location: books_admin.php");
        exit;
    } else {
        $errorMessage = "Error adding book.";
    }
}

// Обработка данных формы редактирования
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == 'edit') {
    $bookId = $_POST['book_id'];

    // Получаем данные книги для редактирования
    $bookToEdit = getBookById($bookId);

    // Обновление данных после отправки формы
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == 'edit') {
        $bookId = $_POST['book_id'];
        $title = $_POST['title'];
        $link = $_POST['link'];
        $description = $_POST['description'];
        $readerId = $_POST['reader_id'];

        // Обновление данных в базе данных
        $result = updateBook($bookId, $readerId, $title, $link, $description);

        if ($result) {
            // Редирект после успешного обновления
            header("Location: books_admin.php");
            exit;
        } else {
            // Обработка ошибки обновления
            $errorMessage = "Error updating book.";
        }
    }

    // Отображаем форму редактирования с заполненными данными
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit Book</title>
        <link rel="stylesheet" href="styles2.css">
    </head>
    <body>

    <div class="container">
        <h2>Edit Book</h2>
        <form id="editBookForm" method="post">
            <label for="reader_id">Reader ID:</label>
            <input type="text" id="reader_id" name="reader_id" value="<?php echo $bookToEdit['reader_id']; ?>" required>
            <br>
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="<?php echo $bookToEdit['title']; ?>" required>
            <br>
            <label for="link">Link:</label>
            <input type="text" id="link" name="link" value="<?php echo $bookToEdit['link']; ?>" required>
            <br>
            <label for="description">Description:</label>
            <input type="text" id="description" name="description" value="<?php echo $bookToEdit['description']; ?>">
            <br>
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="book_id" value="<?php echo $bookId; ?>">
            <input type="submit" value="Update Book">
        </form>
    </div>

    </body>
    </html>
    <?php
    exit; // Завершаем выполнение скрипта после отображения формы редактирования
}

// Обработка данных формы удаления
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == 'delete') {
    $bookId = $_POST['book_id'];

    $result = deleteBook($bookId);

    if ($result) {
        header("Location: books_admin.php");
        exit;
    } else {
        $errorMessage = "Error deleting book.";
    }
}

// Получение списка всех книг
$books = getAllBooks();
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
        <label for="reader_id">Reader ID:</label>
        <input type="text" id="reader_id" name="reader_id" required>
        <br>
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
     // Добавим HTML кнопки для перехода на страницы books_admin и users
    echo '<button onclick="window.location.href=\'index.php\'" class="button">назад</button>';

    if (isset($errorMessage)) {
        echo "<div class='error'>$errorMessage</div>";
    }
    ?>
</div>

</body>
</html>
