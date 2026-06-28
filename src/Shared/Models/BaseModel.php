<?php
namespace Shared\Models;

class BaseModel {
    protected $db;
    protected $dbInstance;
    protected $table = '';
    protected $primaryKey = 'id';

    public function __construct() {
        // Get database connection
        if (!class_exists('connectDB')) {
            require_once __DIR__ . '/../../MVC/Core/connectDB.php';
        }
        
        // Get the connection from connectDB
        $this->dbInstance = new \connectDB();
        // Accessing the protected property via reflection or just using methods from connectDB
    }

    /**
     * Get mysqli connection from protected property
     */
    protected function getConnection() {
        // Use reflection to access the protected $con property
        $reflection = new \ReflectionClass($this->dbInstance);
        $property = $reflection->getProperty('con');
        $property->setAccessible(true);
        return $property->getValue($this->dbInstance);
    }

    /**
     * Lấy tất cả records
     */
    public function all() {
        try {
            $sql = "SELECT * FROM {$this->table}";
            $result = $this->dbInstance->select($sql);
            return $result;
        } catch (\Exception $e) {
            throw new \Exception("Error fetching from {$this->table}: " . $e->getMessage());
        }
    }

    /**
     * Tìm 1 record theo ID
     */
    public function getPrimaryKey() {
        return $this->primaryKey;
    }

    public function find($id) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
            $result = $this->dbInstance->selectOne($sql, [$id]);
            return $result;
        } catch (\Exception $e) {
            throw new \Exception("Error finding record: " . $e->getMessage());
        }
    }

    /**
     * Lấy records với pagination
     */
    public function paginate($page = 1, $perPage = 10) {
        try {
            $offset = ($page - 1) * $perPage;
            $sql = "SELECT * FROM {$this->table} LIMIT ? OFFSET ?";
            $result = $this->dbInstance->select($sql, [(int)$perPage, (int)$offset]);
            return $result;
        } catch (\Exception $e) {
            throw new \Exception("Error paginating records: " . $e->getMessage());
        }
    }

    /**
     * Lấy tổng số records
     */
    public function count() {
        try {
            $sql = "SELECT COUNT(*) as count FROM {$this->table}";
            $result = $this->dbInstance->selectOne($sql);
            return $result['count'] ?? 0;
        } catch (\Exception $e) {
            throw new \Exception("Error counting records: " . $e->getMessage());
        }
    }

    /**
     * Thêm record mới
     */
    public function create($data) {
        try {
            $columns = array_filter(array_keys($data), function($column) {
                return trim($column) !== '';
            });

            if (empty($columns)) {
                throw new \Exception('No data provided to create record');
            }

            $placeholders = implode(', ', array_fill(0, count($columns), '?'));
            $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") VALUES ({$placeholders})";
            $params = [];
            foreach ($columns as $column) {
                $params[] = $data[$column];
            }

            $this->dbInstance->execute($sql, $params);

            // Return the inserted record from database when possible
            $insertedId = $this->dbInstance->insertId();
            if ($insertedId) {
                return $this->find($insertedId) ?: $data;
            }
            if (isset($data[$this->primaryKey])) {
                return $this->find($data[$this->primaryKey]) ?: $data;
            }
            return $data; // Return the data as-is if can't determine the inserted key
        } catch (\Exception $e) {
            throw new \Exception("Error creating record: " . $e->getMessage());
        }
    }

    /**
     * Cập nhật record
     */
    public function update($id, $data) {
        try {
            $columns = array_filter(array_keys($data), function($column) {
                return trim($column) !== '';
            });

            if (empty($columns)) {
                throw new \Exception('No data provided to update record');
            }

            $placeholders = implode(' = ?, ', $columns) . ' = ?';
            $sql = "UPDATE {$this->table} SET {$placeholders} WHERE {$this->primaryKey} = ?";
            $params = [];
            foreach ($columns as $column) {
                $params[] = $data[$column];
            }
            $params[] = $id;

            $this->dbInstance->execute($sql, $params);
            return $this->find($id);
        } catch (\Exception $e) {
            throw new \Exception("Error updating record: " . $e->getMessage());
        }
    }

    /**
     * Xóa record
     */
    public function delete($id) {
        try {
            $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
            return $this->dbInstance->execute($sql, [$id]);
        } catch (\Exception $e) {
            throw new \Exception("Error deleting record: " . $e->getMessage());
        }
    }

    /**
     * Tìm records theo condition
     */
    public function where($column, $condition, $value = null) {
        // Support: where('name', 'John') or where('age', '>', 18)
        if ($value === null) {
            $value = $condition;
            $condition = '=';
        }

        try {
            $sql = "SELECT * FROM {$this->table} WHERE {$column} {$condition} ?";
            return $this->dbInstance->select($sql, [$value]);
        } catch (\Exception $e) {
            throw new \Exception("Error in where clause: " . $e->getMessage());
        }
    }

    /**
     * Tìm record đầu tiên theo condition
     */
    public function firstWhere($column, $condition, $value = null) {
        if ($value === null) {
            $value = $condition;
            $condition = '=';
        }

        try {
            $sql = "SELECT * FROM {$this->table} WHERE {$column} {$condition} ? LIMIT 1";
            return $this->dbInstance->selectOne($sql, [$value]);
        } catch (\Exception $e) {
            throw new \Exception("Error in firstWhere clause: " . $e->getMessage());
        }
    }

    /**
     * Raw query execution
     */
    public function query($sql, $params = []) {
        try {
            return $this->dbInstance->select($sql, $params);
        } catch (\Exception $e) {
            throw new \Exception("Error executing query: " . $e->getMessage());
        }
    }
}
?>
