<?php
/**
 * Database Connection - Docker Version
 * 
 * Sử dụng biến môi trường (environment variables) từ docker-compose.yml
 * thay vì hardcode thông tin kết nối.
 */
class connectDB {
    protected $con;

    public function __construct() {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $host = getenv('DB_HOST') ?: 'localhost';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';
        $name = getenv('DB_NAME') ?: 'web_hotel_mngt';
        $port = (int)(getenv('DB_PORT') ?: 3306);

        $this->con = mysqli_connect($host, $user, $pass, $name, $port);

        if (!$this->con) {
            die("Kết nối DB thất bại: " . mysqli_connect_error());
        }

        mysqli_set_charset($this->con, "utf8");
    }

    public function select($sql, $params = []) {
        $stmt = $this->prepareStatement($sql, $params);
        $result = mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $data;
    }

    public function selectOne($sql, $params = []) {
        $stmt = $this->prepareStatement($sql, $params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $data;
    }

    public function execute($sql, $params = []) {
        $stmt = $this->prepareStatement($sql, $params);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }

    public function insertId() {
        return mysqli_insert_id($this->con);
    }

    private function prepareStatement($sql, $params = []) {
        $stmt = mysqli_prepare($this->con, $sql);
        if (!$stmt) {
            die("Lỗi prepare statement: " . mysqli_error($this->con));
        }

        if (!empty($params)) {
            $types = '';
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
            }
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }

        return $stmt;
    }

    // Backward compatibility methods (deprecated - use prepared statements)
    public function selectUnsafe($sql) {
        $result = mysqli_query($this->con, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }

    public function selectOneUnsafe($sql) {
        $result = mysqli_query($this->con, $sql);
        return mysqli_fetch_assoc($result);
    }

    public function executeUnsafe($sql) {
        return mysqli_query($this->con, $sql);
    }
}
