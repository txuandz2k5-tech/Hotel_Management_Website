<?php
namespace Api;

use Shared\Http\Request;
use Shared\Http\Response;

class BaseApiController {
    protected $request;
    protected $response;

    public function __construct(Request $request, Response $response) {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Lấy model
     */
    protected function model($modelName) {
        $modelClass = "Shared\\Models\\$modelName";
        if (!class_exists($modelClass)) {
            throw new \Exception("Model $modelName not found");
        }
        return new $modelClass();
    }

    
    protected function validate($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            
            // Check required
            if (in_array('required', $fieldRules) && (empty($value) || $value === '')) {
                $errors[$field] = "Trường $field là bắt buộc";
            }
            
            // Check email
            if (in_array('email', $fieldRules) && !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = "Trường $field phải là email hợp lệ";
            }
            
            // Check numeric
            if (in_array('numeric', $fieldRules) && !empty($value) && !is_numeric($value)) {
                $errors[$field] = "Trường $field phải là số";
            }
            
            // Check min length
            foreach ($fieldRules as $rule) {
                if (strpos($rule, 'min:') === 0) {
                    $minLength = intval(substr($rule, 4));
                    if (!empty($value) && strlen($value) < $minLength) {
                        $errors[$field] = "Trường $field phải dài ít nhất $minLength ký tự";
                    }
                }
                
                // Check max length
                if (strpos($rule, 'max:') === 0) {
                    $maxLength = intval(substr($rule, 4));
                    if (!empty($value) && strlen($value) > $maxLength) {
                        $errors[$field] = "Trường $field không được quá $maxLength ký tự";
                    }
                }
            }
        }
        
        if (count($errors) > 0) {
            $this->response->validationError($errors);
            return false;
        }

        return true;
    }

    /**
     * Lấy ID từ URL params
     */
    protected function getId() {
        return $this->request->param('id');
    }


/**------------------------------------------------------------------------------------------------------------*/
    /**
     * Lấy page từ query
     */

    protected function getPage() {
        return max(1, intval($this->request->query('page', 1)));
    }

    /**
     * Lấy per_page từ query
     */
    protected function getPerPage() {
        return min(100, max(1, intval($this->request->query('per_page', 10))));
    }

    /**
     * Lấy search query từ query
     */
    protected function getSearch() {
        return trim($this->request->query('search', ''));
    }
/**------------------------------------------------------------------------------------------------------------*/
    /**
     * Log error
     */
    protected function logError($message, $exception = null) {
        $logFile = __DIR__ . '/../../storage/logs/api.log';
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] ERROR: $message";
        
        if ($exception) {
            $logMessage .= "\n" . $exception->getMessage() . "\n" . $exception->getTraceAsString();
        }
        
        error_log($logMessage . "\n", 3, $logFile);
    }
}
?>
