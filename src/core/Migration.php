<?php

class Migration {
    private $db;
    private $migrationDir;

    public function __construct($db) {
        $this->db = $db;
        $this->migrationDir = __DIR__ . '/../migrations/';
        
        // Create migrations directory if it doesn't exist
        if (!is_dir($this->migrationDir)) {
            mkdir($this->migrationDir, 0755, true);
        }
    }

    public function run($filename) {
        $filepath = $this->migrationDir . $filename;
        
        if (!file_exists($filepath)) {
            echo "Migration file {$filename} not found.
";
            return false;
        }

        $sql = file_get_contents($filepath);
        
        try {
            $this->db->exec($sql);
            echo "Migration {$filename} executed successfully.
";
            return true;
        } catch (PDOException $e) {
            echo "Error in migration {$filename}: " . $e->getMessage() . "
";
            return false;
        }
    }

    public function create($name) {
        $filename = date('Y-m-d_H-i-s') . '_' . $name . '.sql';
        $filepath = $this->migrationDir . $filename;
        
        $template = "-- Migration: {$name}
-- Created at: " . date('Y-m-d H:i:s') . "

-- Write your SQL here
";
        
        file_put_contents($filepath, $template);
        echo "Migration file {$filename} created.
";
        return $filename;
    }
}