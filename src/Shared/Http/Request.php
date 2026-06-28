<?php
namespace Shared\Http;

class Request {
    private $method;
    private $path;
    private $input;
    private $headers;

    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->path = $this->parsePath();
        $this->input = $this->parseInput();
        $this->headers = $this->parseHeaders();
    }

    /**
     * Lấy HTTP method (GET, POST, PUT, DELETE, PATCH)
     */
    public function getMethod() {
        return strtoupper($this->method);
    }

    /**
     * Lấy request path (/api/guests/1)
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Lấy một giá trị input
     */
    public function input($key = null, $default = null) {
        if ($key === null) {
            return $this->input;
        }
        return $this->input[$key] ?? $default;
    }

    /**
     * Lấy tất cả input
     */
    public function all() {
        return $this->input;
    }

    /**
     * Lấy một header
     */
    public function header($name, $default = null) {
        $name = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return $this->headers[$name] ?? $default;
    }

    /**
     * Kiểm tra header có tồn tại không
     */
    public function hasHeader($name) {
        return $this->header($name) !== null;
    }

    /**
     * Lấy URL segments
     */
    public function segments() {
        $path = trim($this->path, '/');
        return $path === '' ? [] : explode('/', $path);
    }

    /**
     * Lấy query string parameter
     */
    public function query($key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }

    /**
     * Kiểm tra dữ liệu có tồn tại không
     */
    public function has($key) {
        return isset($this->input[$key]);
    }

    /**
     * Kiểm tra method
     */
    public function isMethod($method) {
        return strtoupper($method) === $this->getMethod();
    }

    /**
     * Set URL parameter
     */
    public function setParam($key, $value) {
        $this->input[$key] = $value;
        return $this;
    }

    /**
     * Lấy URL parameter
     */
    public function param($key, $default = null) {
        return $this->input[$key] ?? $default;
    }

    // ============ Private Methods ============

    private function parsePath() {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $script = $_SERVER['SCRIPT_NAME'];
        $scriptDir = dirname($script);

        $pathLower = strtolower($path);
        $scriptDirLower = strtolower($scriptDir);
        $scriptLower = strtolower($script);

        // Nếu ứng dụng nằm trong thư mục con, loại bỏ phần đường dẫn gốc
        if ($scriptDir !== '/' && stripos($pathLower, $scriptDirLower) === 0) {
            $path = substr($path, strlen($scriptDir));
            $pathLower = strtolower($path);
        }

        if (stripos($pathLower, $scriptLower) === 0) {
            $path = substr($path, strlen($script));
        } elseif (!empty($_SERVER['PATH_INFO'])) {
            $path = $_SERVER['PATH_INFO'];
        }

        $path = '/' . trim($path, '/');
        return $path === '/' ? '/' : $path;
    }

    private function parseInput() {
        $input = [];

        // Lấy từ $_GET
        $input += $_GET ?? [];

        // Lấy từ $_POST
        $input += $_POST ?? [];

        // Lấy từ JSON body
        if (in_array($this->getMethod(), ['POST', 'PUT', 'PATCH'])) {
            $json = json_decode(file_get_contents('php://input'), true);
            $input += $json ?? [];
        }

        return $input;
    }

    private function parseHeaders() {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $headers[$key] = $value;
            }
        }
        return $headers;
    }
}
?>
