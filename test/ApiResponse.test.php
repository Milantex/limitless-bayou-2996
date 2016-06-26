<?php
    use Milantex\LimitlessBayou\Sys\LimitlessBayou as LimitlessBayou;
    use Milantex\LimitlessBayou\Sys\ApiResponse as ApiResponse;
    
    class ApiResponsetTest extends PHPUnit_Framework_TestCase {
        /**
         * @runInSeparateProcess
         */
        public function testConstructor() {
            $app = new LimitlessBayou('localhost', 'bayou', 'root', '', 'examples/example-001/maps');
            
            ob_start();
            new ApiResponse($app, ApiResponse::STATUS_ERROR, []);

            $headers = headers_list();
            $responseJson = ob_get_clean();

            $this::doTestHeaders($headers);
            $this::doTestResponse($responseJson);
        }

        private static function doTestHeaders(&$headers) {
            $mandatoryHeaders = [
                'Pragma: no-cache',
                'Cache-Control: post-check=0, pre-check=0',
                'Cache-Control: no-store, no-cache, must-revalidate, max-age=0',
                'Content-type: text/json; charset=utf-8',
                'Connection: close'
            ];

            foreach ($mandatoryHeaders as $mandatoryHeader) {
                $this->assertArrayHasKey($mandatoryHeader, $headers);
            }
        }

        private static function doTestResponse(&$responseJson) {
            $response = json_decode($responseJson);

            $this->assertTrue(is_object($response), 'The response is not a valid JSON object.');

            $this->assertTrue(property_exists($response, 'status'), 'The status property does not exist.');
            $this->assertTrue(property_exists($response, 'type'), 'The type property does not exist.');
            $this->assertTrue(property_exists($response, 'content'), 'The content property does not exist.');
            $this->assertTrue(property_exists($response, 'timestampStart'), 'The timestampStart property does not exist.');
            $this->assertTrue(property_exists($response, 'timestampEnd'), 'The timestampEnd property does not exist.');
            $this->assertTrue(property_exists($response, 'executionDuration'), 'The executionDuration property does not exist.');

            $this->assertTrue(in_array($response->status, ['error', 'ok', 'info']), 'The status property does not have a valid value.');
            $this->assertTrue(in_array($response->type, ['array', 'object', 'string', 'number']), 'The status property does not have a valid value.');
            $this->assertTrue(is_long($response->timestampStart));
            $this->assertTrue(is_long($response->timestampEnd));
            $this->assertTrue(is_long($response->executionDuration));
            $this->assertTrue($response->timestampEnd < $response->timestampStart, 'The end timestamp is smaller then the start timestmap.');
        }
    }
