<?php

class Book extends Model {
    
    public function getAllBooks() {
        $sql = "SELECT * FROM books";
        return $this->fetchAll($sql);
    }
    
    public function getBookById($id) {
        $sql = "SELECT * FROM books WHERE id = :id";
        return $this->fetch($sql, ['id' => $id]);
    }
    
    public function getBookByISBN($isbn) {
        $sql = "SELECT * FROM books WHERE isbn = :isbn";
        return $this->fetch($sql, ['isbn' => $isbn]);
    }
    
    public function searchBooks($term) {
        $sql = "SELECT * FROM books WHERE title LIKE :term OR author LIKE :term";
        return $this->fetchAll($sql, ['term' => "%{$term}%"]);
    }
    
    public function createBook($data) {
        $sql = "INSERT INTO books (title, author, isbn, published_year, genre, copies_total, copies_available) 
                VALUES (:title, :author, :isbn, :published_year, :genre, :copies_total, :copies_available)";
        return $this->execute($sql, $data);
    }
    
    public function updateBook($id, $data) {
        $setParts = [];
        foreach ($data as $key => $value) {
            $setParts[] = "{$key} = :{$key}";
        }
        $setClause = implode(', ', $setParts);
        
        $sql = "UPDATE books SET {$setClause} WHERE id = :id";
        $data['id'] = $id;
        return $this->execute($sql, $data);
    }
    
    public function deleteBook($id) {
        $sql = "DELETE FROM books WHERE id = :id";
        return $this->execute($sql, ['id' => $id]);
    }
}