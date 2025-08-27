<?php

class Loan extends Model {
    
    public function getAllLoans() {
        $sql = "SELECT * FROM loans";
        return $this->fetchAll($sql);
    }
    
    public function getLoanById($id) {
        $sql = "SELECT * FROM loans WHERE id = :id";
        return $this->fetch($sql, ['id' => $id]);
    }
    
    public function getLoansByUserId($userId) {
        $sql = "SELECT * FROM loans WHERE user_id = :user_id";
        return $this->fetchAll($sql, ['user_id' => $userId]);
    }
    
    public function getLoansByBookId($bookId) {
        $sql = "SELECT * FROM loans WHERE book_id = :book_id";
        return $this->fetchAll($sql, ['book_id' => $bookId]);
    }
    
    public function getActiveLoans() {
        $sql = "SELECT * FROM loans WHERE status = 'active'";
        return $this->fetchAll($sql);
    }
    
    public function createLoan($data) {
        $sql = "INSERT INTO loans (user_id, book_id, loan_date, due_date) 
                VALUES (:user_id, :book_id, :loan_date, :due_date)";
        return $this->execute($sql, $data);
    }
    
    public function returnLoan($id) {
        $sql = "UPDATE loans SET return_date = CURDATE(), status = 'returned' WHERE id = :id";
        return $this->execute($sql, ['id' => $id]);
    }
    
    public function updateLoan($id, $data) {
        $setParts = [];
        foreach ($data as $key => $value) {
            $setParts[] = "{$key} = :{$key}";
        }
        $setClause = implode(', ', $setParts);
        
        $sql = "UPDATE loans SET {$setClause} WHERE id = :id";
        $data['id'] = $id;
        return $this->execute($sql, $data);
    }
    
    public function deleteLoan($id) {
        $sql = "DELETE FROM loans WHERE id = :id";
        return $this->execute($sql, ['id' => $id]);
    }
}