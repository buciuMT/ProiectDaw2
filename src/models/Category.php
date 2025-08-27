<?php

class Category extends Model {
    
    public function getAllCategories() {
        $sql = "SELECT * FROM categories";
        return $this->fetchAll($sql);
    }
    
    public function getCategoryById($id) {
        $sql = "SELECT * FROM categories WHERE id = :id";
        return $this->fetch($sql, ['id' => $id]);
    }
    
    public function getCategoryByName($name) {
        $sql = "SELECT * FROM categories WHERE name = :name";
        return $this->fetch($sql, ['name' => $name]);
    }
    
    public function createCategory($data) {
        $sql = "INSERT INTO categories (name, description) VALUES (:name, :description)";
        return $this->execute($sql, $data);
    }
    
    public function updateCategory($id, $data) {
        $setParts = [];
        foreach ($data as $key => $value) {
            $setParts[] = "{$key} = :{$key}";
        }
        $setClause = implode(', ', $setParts);
        
        $sql = "UPDATE categories SET {$setClause} WHERE id = :id";
        $data['id'] = $id;
        return $this->execute($sql, $data);
    }
    
    public function deleteCategory($id) {
        $sql = "DELETE FROM categories WHERE id = :id";
        return $this->execute($sql, ['id' => $id]);
    }
    
    public function getCategoriesForBook($bookId) {
        $sql = "SELECT c.* FROM categories c 
                JOIN book_categories bc ON c.id = bc.category_id 
                WHERE bc.book_id = :book_id";
        return $this->fetchAll($sql, ['book_id' => $bookId]);
    }
    
    public function addCategoryToBook($bookId, $categoryId) {
        $sql = "INSERT INTO book_categories (book_id, category_id) VALUES (:book_id, :category_id)";
        return $this->execute($sql, ['book_id' => $bookId, 'category_id' => $categoryId]);
    }
    
    public function removeCategoryFromBook($bookId, $categoryId) {
        $sql = "DELETE FROM book_categories WHERE book_id = :book_id AND category_id = :category_id";
        return $this->execute($sql, ['book_id' => $bookId, 'category_id' => $categoryId]);
    }
}