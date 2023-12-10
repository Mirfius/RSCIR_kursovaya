<?php
session_start();
require_once __DIR__ . '/../repository/repository.php';
require_once __DIR__ . '/../enities/User.php';

$_SESSION['id'];
$_SESSION['role']; 

function handleRegister($data, $user) {
    $username = $data['username'];
    $password = $data['password'];
    $result = registerUser($username, $password);

    if ($result) {
        // Получаем информацию о зарегистрированном пользователе
        $user = getUserByUsername($username);
        // Сохраняем информацию о пользователе в сессии
        $_SESSION['id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
    }

    return ["result" => $result];
}
function handleLogin($data, $user) {
    $username = $data['username'];
    $password = $data['password'];

    $result = authenticateUser($username, $password);

    if ($result) {
        // Получаем информацию о зарегистрированном пользователе
        $user = getUserByUsername($username);
        // Сохраняем информацию о пользователе в сессии
        $_SESSION['id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
    }

    return ["result" => $result];
}
function handleLogout() {
    session_start();
    // Уничтожаем все данные сессии
    session_destroy();
    return "Logged out";
}
function handleAddUser($data, $user) {
    if ($user && $user->getRole() == 'superadmin') {
        $login = $data['login'];
        $password = $data['password'];
        $role = $data['role'];
        return addUser($login, $password, $role);
    } else {
        return "Net prav. vasha rol: " . ($user ? $user->getRole() : 'undefined');
    }
}
function handleAddBook($data, $user) {
    if ($user && ($user->getRole() == 'admin' || $user->getRole() == 'superadmin')) {
        $title = $data['title'];
        $link = $data['link'];
        $description = $data['description'];
        $readerId = $data['reader_id'];
        return addBook($readerId, $title, $link, $description);
    } else {
        return "Net prav. vasha rol: " . ($user ? $user->getRole() : 'undefined');
    }
}
function handleGetAllUsers($user) {
    if ($user && $user->getRole() == 'superadmin') {
        $result = getAllUsers();
        return ["result" => $result];
    } elseif ($user) {
        $currentRole = $user->getRole();
        $currentUser = getUserById($user->getUserId());
        return ["result" => $currentUser];
    } else {
        return ["result" => "Not authorized. Please log in."];
    }
}
function handleGetUserById($data, $user) {
    if ($user && $user->getRole() == 'superadmin') {
        $userId = $data['id'];
        $result = getUserById($userId);
        return ["result" => $result];
    } else {
        $currentRole = $user ? $user->getRole() : 'undefined';
        return ["result" => "Net prav. vasha rol: $currentRole"];
    }
}
function handleGetAllBooks($user) {
    if ($user && ($user->getRole() == 'admin' || $user->getRole() == 'superadmin')) {
        $result = getAllBooks();
        return ["result" => $result];
    } elseif ($user) {
        $userId = $user->getUserId();
        $result = getBooksByUserId($userId);
        return ["result" => $result];
    } else {
        return ["result" => "Not authorized. Please log in."];
    }
}
function handleGetBookById($data, $user) {
    if ($user && ($user->getRole() == 'admin' || $user->getRole() == 'superadmin')) {
        $bookId = $data['id'];
        $result = getBookById($bookId);
        return ["result" => $result];
    } elseif ($user) {
        $userId = $user->getUserId();
        $bookId = $data['id'];
        $book = getBookById($bookId);

        if ($book && $book['reader_id'] == $userId) {
            return ["result" => $book];
        } else {
            return ["result" => "You do not have permission to view this book."];
        }
    } else {
        return ["result" => "Not authorized. Please log in."];
    }
}
function handleGetBooksByUserId($data, $user) {
    if ($user && $user->getRole() == 'reader') {
        $userId = $user->getUserId();
    } else {
        $userId = $data['id'];
    }
    $books = getBooksByUserId($userId);

    if ($books !== false) {
        return ["result" => $books];
    } else {
        return ["result" => "not found."];
    }
}
function handleUpdateUser($data, $user) {
    if (!$user) {
        echo json_encode(["result" => "Not authorized. Please log in."], JSON_PRETTY_PRINT);
        return;
    }
    
    $userId = $data['user_id'];
    $login = $data['login'];
    $password = $data['password'];
    $role = $data['role'];
    
    if ($user->getRole() == 'superadmin') {
        // Суперадмин может изменять все данные
        $success = updateUser($userId, $login, $password, $role);
        return["result" => $success];
    } elseif ($user->getRole() == 'admin' || $user->getRole() == 'reader') {
        // Если роль - обычный читатель или админ, то пользователь может изменить только логин и пароль
        if ($userId == $user->getUserId()) {
            $success = updateUser($userId, $login, $password, $user->getRole());
            return["result" => $success];
        } else {
            return["result" => "You do not have permission to update this user."];
        }
    }
    
}
function handleUpdateBook($data, $user) {
    if (!$user) {
        echo json_encode(["result" => "Not authorized. Please log in."], JSON_PRETTY_PRINT);
        return;
    }

    $bookId = $data['book_id'];
    $readerId = $data['reader_id'];
    $title = $data['title'];
    $link = $data['link'];
    $description = $data['description'];

    if ($user->getRole() == 'admin' || $user->getRole() == 'superadmin') {
        // Админ и суперадмин могут изменять все данные
        $result = updateBook($bookId, $readerId, $title, $link, $description);
        return["result" => $result];
    } elseif ($user->getRole() == 'reader') {
        // Если роль - обычный читатель, то пользователь может изменить только свои книги
        $book = getBookById($bookId);

        if ($book && $book['reader_id'] == $user->getUserId()) {
            $result = updateBook($bookId, $readerId, $title, $link, $description);
            return["result" => $result];
        } else {
            return["result" => "You do not have permission to update this book."];
        }
    }
}
function handleDeleteBook($data, $user) {
    if (!$user) {
        echo json_encode(["result" => "Not authorized. Please log in."], JSON_PRETTY_PRINT);
        return;
    }

    $bookId = $data['book_id'];

    if ($user->getRole() == 'admin' || $user->getRole() == 'superadmin') {
        // Админ и суперадмин могут удалять любые книги
        $result = deleteBook($bookId);
        return["result" => $result];
    } elseif ($user->getRole() == 'reader') {
        // Если роль - обычный читатель, то пользователь может удалить только свои книги
        $book = getBookById($bookId);

        if ($book && $book['reader_id'] == $user->getUserId()) {
            $result = deleteBook($bookId);
            return["result" => $result];
        } else {
            return["result" => "You do not have permission to delete this book."];
        }
    }
}
function handleDeleteUser($data, $user) {
    if (!$user) {
        return["result" => "Not authorized. Please log in."];
        
    }

    if ($user->getRole() == 'superadmin') {
        $userId = $data['user_id'];
        $result = deleteUser($userId);
        return["result" => $result];
    } else {
        return["result" => "You do not have permission to delete a user."];
    }
}
?>
