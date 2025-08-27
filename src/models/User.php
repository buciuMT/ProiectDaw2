<?php

class User extends Model {
    
    public function getAllUsers() {
        $sql = "SELECT id, name, email, role, verified, created_at FROM users ORDER BY created_at DESC";
        return $this->fetchAll($sql);
    }
    
    public function getUserById($id) {
        $sql = "SELECT id, name, email, role, verified, created_at FROM users WHERE id = :id";
        return $this->fetch($sql, ['id' => $id]);
    }
    
    public function getUserByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email";
        return $this->fetch($sql, ['email' => $email]);
    }
    
    public function createUser($data) {
        $sql = "INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)";
        return $this->execute($sql, $data);
    }
    
    public function verifyUser($id) {
        $sql = "UPDATE users SET verified = CURDATE() WHERE id = :id";
        return $this->execute($sql, ['id' => $id]);
    }
    
    public function updateUser($id, $data) {
        $setParts = [];
        foreach ($data as $key => $value) {
            $setParts[] = "{$key} = :{$key}";
        }
        $setClause = implode(', ', $setParts);
        
        $sql = "UPDATE users SET {$setClause} WHERE id = :id";
        $data['id'] = $id;
        return $this->execute($sql, $data);
    }
    
    public function deleteUser($id) {
        $sql = "DELETE FROM users WHERE id = :id";
        return $this->execute($sql, ['id' => $id]);
    }
}