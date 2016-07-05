<?php
    namespace Milantex\LimitlessBayou;

    use Milantex\LimitlessBayou\LimitlessBayou as LimitlessBayou;

    /**
     * The ApiResponse class is used to sent the API response instantly upon
     * creation of an ApiResponse object. Each object has an 
     */
    final class ApiResponse {
        /**
         * The reference to the main LimitlessBayou API application instance
         * @var LimitlessBayou
         */
        private $app;

        /**
         * The return status of the API response.
         * Currently, only three values are possible: ok, error and information
         * @var string
         */
        public $status;

        /**
         * The type of the returned content. This is set automatically.
         * Currently, only values array, object, string and number are possible
         * @var string
         */
        public $type;

        /**
         * The timestamp when the request processing began
         * @var float
         */
        public $timestampStart;

        /**
         * The timestamp when the request processing was finished
         * @var float
         */
        public $timestampEnd;

        /**
         * The duration of request processing
         * @var float
         */
        public $executionDuration;

        /**
         * The content of the API response
         * @var mixed
         */
        public $content;

        /*
         * The output that will be sent in the API response
         * @var string
         */
        public $output;

        /**
         * API response status used when everything was all right
         */
        const STATUS_OK = 'ok';

        /**
         * API response status used when there was an error
         */
        const STATUS_ERROR = 'error';

        /**
         * A special API response status used in certain situations
         */
        const STATUS_INFO = 'information';

        /**
         * API response content type indicator when the content is an array
         */
        const TYPE_ARRAY  = 'array';

        /**
         * API response content type indicator when the content is an object
         */
        const TYPE_OBJECT = 'object';

        /**
         * API response content type indicator when the content is a string
         */
        const TYPE_STRING = 'string';

        /**
         * API response content type indicator when the content is a number
         */
        const TYPE_NUMBER = 'number';

        /**
         * The ApiResponse constructor function takes the status type indicator
         * and the content that should be sent. If no arguments are given, the
         * response has the status value set to information and the content is
         * an empty array. When the ApiResponse object is created the response
         * is momentarily sent as a JSON structure.
         * @param string $status
         * @param mixed $content
         */
        public function __construct(LimitlessBayou &$app, $status = ApiResponse::STATUS_INFO, $content = []) {
            $this->app = $app;
            $this->status  = $status;
            $this->content = $content;
            $this->timestampStart = $this->app->getStartTime();
            $this->executionDuration = $this->timestampEnd - $this->app->getStartTime();

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
        }

        /**
         * Returns the output string which will be sent as the API response
         * @return string
         */
        function getOutput() {
            $this->timestampEnd = microtime(true);

            $this->output = json_encode($this, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            return $this->output;
        }

        /**
         * Send the API prepared response
         */
        public function send() {
            $this->sendHeaders();
            echo $this->getOutput();
            exit;
        }

        /**
         * Clears the output buffer and sends out the necessary HTTP headers
         * before the function can send the response payload.
         */
        private function sendHeaders() {
            ob_clean();
            header("Pragma: no-cache");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header('Content-type: text/json; charset=utf-8');
            header("Connection: close");
        }
    }
