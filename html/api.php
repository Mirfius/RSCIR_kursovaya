<?php
session_start();
require_once __DIR__ . '/repository/repository.php';
require_once __DIR__ . '/enities/User.php';
require_once __DIR__ . '/handlers/handlers.php';


$_SESSION['id'];
$_SESSION['role']; 

// Инициализация пользователя, если сессия уже существует
$user = null;
if (isset($_SESSION['id']) && isset($_SESSION['role'])) {
    $user = new User($_SESSION['id'], '', '', $_SESSION['role']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получение данных из тела запроса
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['action'])) {
        $action = $data['action'];

        switch ($action) {
            case 'register':
                $result = handleRegister($data, $user);
                echo json_encode($result, JSON_PRETTY_PRINT);
                break;
            case 'login':
                $result = handleLogin($data, $user);
                echo json_encode($result, JSON_PRETTY_PRINT);
                break;
            case 'logout':
                    $result = handleLogout();
                    echo json_encode(["result" => $result], JSON_PRETTY_PRINT);
                    break;
            case 'add_user':
                    $result = handleAddUser($data, $user);
                    echo json_encode(["result" => $result], JSON_PRETTY_PRINT);
                    break;
            case 'add_book':
                    $result = handleAddBook($data, $user);
                    echo json_encode(["result" => $result], JSON_PRETTY_PRINT);
                    break;
            default:
                    echo json_encode(["error" => "Invalid action."]);
                    break;
        }
    } else {
        echo json_encode(["error" => "Action not specified."]);
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Обработка GET-запросов
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['action'])) {
        $action = $data['action'];

        switch ($action) {
            case 'get_all_users':
                $result = handleGetAllUsers($user);
                    echo json_encode($result, JSON_PRETTY_PRINT);
                break;

            case 'get_user_by_id':
                $result = handleGetUserById($data, $user);
                    echo json_encode($result, JSON_PRETTY_PRINT);
                break;

            case 'get_all_books':
                $result = handleGetAllBooks($user);
                echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                break;

            case 'get_book_by_id':
                $result = handleGetBookById($data, $user);
                    echo json_encode( $result, JSON_PRETTY_PRINT);
                break;

            case 'get_books_by_user_id':
                $result = handleGetBooksByUserId($data, $user);
                    echo json_encode($result, JSON_PRETTY_PRINT);
                break;


            default:
                echo json_encode(["error" => "Invalid action."]);
                break;
        }
    } else {
        echo json_encode(["error" => "Action not specified."]);
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "PUT") {
    // Обработка PUT-запросов
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['action'])) {
        $action = $data['action'];

        switch ($action) {
            // Пользователи
        case 'update_user':
            $result = handleUpdateUser($data, $user);
            echo json_encode($result);
            break;

            // Книги
        case 'update_book':
            $result = handleUpdateBook($data, $user);
            echo json_encode($result, JSON_PRETTY_PRINT);
            break; 
            
        }
    } else {
        echo json_encode(["error" => "Action not specified."]);
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "DELETE") {
    // Обработка DELETE-запросов
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['action'])) {
        $action = $data['action'];

        switch ($action) {
            // Пользователи
        case 'delete_book':
            $result = handleDeleteBook($data, $user);
            echo json_encode($result, JSON_PRETTY_PRINT);
            break; 
        case 'delete_user':
            $result = handleDeleteUser($data, $user);
            echo json_encode($result, JSON_PRETTY_PRINT);
            break; 
        }
    } else {
        echo json_encode(["error" => "Action not specified."]);
    }
} else {
    echo json_encode(["error" => "Invalid request method."]);
}
?>

