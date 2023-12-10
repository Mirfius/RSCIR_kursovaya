<?php
// Подключение к базе данных
$mysqli = new mysqli("db", "user", "password", "appDB");
$mysqli->set_charset("latin1");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

function registerUser($username, $password) {
    global $mysqli;

    $checkUserQuery = "SELECT * FROM users WHERE login = '$username'";
    $result = $mysqli->query($checkUserQuery);

    if ($result && $result->num_rows == 0) {
        // $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $insertUserQuery = "INSERT INTO users (login, password, role) VALUES ('$username', '$password', 'reader')";
        $insertResult = $mysqli->query($insertUserQuery);

        if ($insertResult) {
            return "1";
        } else {
            return "Error during registration.";
        }
    } else {
        return "Username already exists.";
    }
}
function authenticateUser($username, $password) {
    global $mysqli;

    $checkUserQuery = "SELECT * FROM users WHERE login = '$username' AND password = '$password'";
    $result = $mysqli->query($checkUserQuery);

    if ($result && $result->num_rows == 1) {

                
        return "1";
    } else {
        return "Invalid login or password.";
    }
}
function logoutUser() {
    // Начинаем сессию
    session_start();

    // Уничтожаем сессию
    session_destroy();

    // Перенаправляем на страницу входа
    header("Location: login.php");
    exit;
}

// юзеры

function getAllUsers() {
    global $mysqli;
    $query = "SELECT * FROM users";
    $result = $mysqli->query($query);

    if ($result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return false;
    }
}
function getUserById($userId) {
    global $mysqli;

    $userId = $mysqli->real_escape_string($userId);

    $query = "SELECT * FROM users WHERE user_id = '$userId'";
    $result = $mysqli->query($query);

    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return false;
    }
}
function addUser($login, $password, $role) {
    global $mysqli;
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $query = "INSERT INTO users (login, password, role) VALUES ('$login', '$password', '$role')";
    return $mysqli->query($query);
}
function updateUser($userId, $login, $password, $role) {
    global $mysqli;
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $query = "UPDATE users SET login='$login', password='$password', role='$role' WHERE user_id='$userId'";
    $result =  $mysqli->query($query);

    return $result !== false;
}
function deleteUser($userId) {
    global $mysqli;
    $query = "DELETE FROM users WHERE user_id='$userId'";
    return $mysqli->query($query);
}

// книги

function getAllBooks() {
    global $mysqli;
    $query = "SELECT * FROM books";
    $result = $mysqli->query($query);

    if ($result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return false;
    }
}
function getBookById($bookId) {
    global $mysqli;

    $bookId = $mysqli->real_escape_string($bookId);

    $query = "SELECT * FROM books WHERE book_id = '$bookId'";
    $result = $mysqli->query($query);

    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return false;
    }
}
function addBook($readerId, $title, $link, $description) {
    global $mysqli;
    $query = "INSERT INTO books (reader_id, title, link, description) VALUES ('$readerId', '$title', '$link', '$description')";
    return $mysqli->query($query);
}
function updateBook($bookId, $readerId, $title, $link, $description) {
    global $mysqli;
    
    $query = "UPDATE books SET reader_id='$readerId', title='$title', link='$link', description='$description' WHERE book_id='$bookId'";
    $result = $mysqli->query($query);

    // Возвращаем true, если запрос выполнен успешно, иначе false
    return $result !== false;
}

function deleteBook($bookId) {
    global $mysqli;
    $query = "DELETE FROM books WHERE book_id='$bookId'";
    return $mysqli->query($query);
}


function getBooksByUserId($userId) {
    global $mysqli;

    $userId = $mysqli->real_escape_string($userId);

    $query = "SELECT * FROM books WHERE reader_id = '$userId'";
    $result = $mysqli->query($query);

    if ($result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return false;
    }
}
function getUserByUsername($username) {
    global $mysqli;

    $username = $mysqli->real_escape_string($username);

    $query = "SELECT * FROM users WHERE login = '$username'";
    $result = $mysqli->query($query);

    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return false;
    }
}


?>
