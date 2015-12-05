<?php
    final class ApiResponse {
        public $status;
        public $type;
        public $timestamp;
        public $content;

        const STATUS_OK = 'ok';
        const STATUS_ERROR = 'error';

        const TYPE_ARRAY  = 'array';
        const TYPE_OBJECT = 'object';
        const TYPE_STRING = 'string';
        const TYPE_NUMBER = 'number';

        public function __construct($status = ApiResponse::STATUS_OK, $content = []) {
            $this->status  = $status;
            $this->content = $content;
            $this->timestamp = microtime(true);

            if (is_object($content)) {
                $this->type = ApiResponse::TYPE_OBJECT;
            } elseif (is_array($content)) {
                $this->type = ApiResponse::TYPE_ARRAY;
            } elseif (is_numeric($content)) {
                $this->type = ApiResponse::TYPE_NUMBER;
            } else {
                $this->type = ApiResponse::TYPE_STRING;
                $this->content = strval($content);
            }

            ob_clean();
            header('Content-type: text/json; charset=utf-8');
            echo json_encode($this, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
