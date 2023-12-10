<?php

class Book {
    private $bookId;
    private $readerId;
    private $title;
    private $link;
    private $description;

    public function __construct($bookId, $readerId, $title, $link, $description) {
        $this->bookId = $bookId;
        $this->readerId = $readerId;
        $this->title = $title;
        $this->link = $link;
        $this->description = $description;
    }

    // Геттеры
    public function getBookId() {
        return $this->bookId;
    }

    public function getReaderId() {
        return $this->readerId;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getLink() {
        return $this->link;
    }

    public function getDescription() {
        return $this->description;
    }

    // Сеттеры
    public function setReaderId($readerId) {
        $this->readerId = $readerId;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setLink($link) {
        $this->link = $link;
    }

    public function setDescription($description) {
        $this->description = $description;
    }
}
?>