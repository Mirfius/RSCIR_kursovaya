CREATE DATABASE IF NOT EXISTS appDB CHARACTER SET latin1 COLLATE latin1_swedish_ci;
CREATE USER IF NOT EXISTS 'user'@'%' IDENTIFIED BY 'password';
GRANT SELECT,UPDATE,INSERT ON appDB.* TO 'user'@'%';
FLUSH PRIVILEGES;

-- Предоставляем пользователю "user" разрешения на выполнение операций CRUD в базе данных "appDB"
GRANT SELECT, INSERT, UPDATE, DELETE ON appDB.* TO 'user'@'%';


-- Обновляем привилегии
FLUSH PRIVILEGES;

USE appDB;

CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('reader', 'admin', 'superadmin') NOT NULL
);

CREATE TABLE IF NOT EXISTS books (
    book_id INT AUTO_INCREMENT PRIMARY KEY,
    reader_id INT,
    title VARCHAR(255) NOT NULL,
    link VARCHAR(255) NOT NULL,
    description TEXT,
    FOREIGN KEY (reader_id) REFERENCES users(user_id)
);

INSERT IGNORE INTO users (login, password, role) VALUES
    ('superadmin', '111', 'superadmin'),
    ('admin', '111', 'admin'),
    ('user', '111', 'reader'),
    ('user1', '111', 'reader'),
    ('user2', '111', 'reader'),
    ('user3', '111', 'reader');


INSERT IGNORE INTO books (reader_id, title, link, description) VALUES
    (1, 'громовая поступь 1', 'https://author.today/reader/161265', 'фентези'),
    (1, 'временщик', 'https://author.today/work/12956', 'фантастика'),
    (1, 'антимаг', 'https://author.today/work/123452', 'фантастика'),
    (2, 'громовая поступь 1', 'https://author.today/reader/161265', 'фентези'),
    (2, 'временщик', 'https://author.today/work/12956', 'фантастика'),
    (2, 'антимаг', 'https://author.today/work/123452', 'фантастика'),
    (3, 'громовая поступь 1', 'https://author.today/reader/161265', 'фентези'),
    (3, 'временщик', 'https://author.today/work/12956', 'фантастика'),
    (3, 'антимаг', 'https://author.today/work/123452', 'фантастика'),
    (3, 'проводник хаоса', 'https://author.today/work/310700', 'фантастика'),
    (3, 'алхимик 1', 'https://author.today/work/48986', 'фентези, литрпг');