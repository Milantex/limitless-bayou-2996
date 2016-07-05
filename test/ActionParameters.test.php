<?php
    class ActionParametersTest extends PHPUnit_Framework_TestCase {
        public function testGetNextGenericParameterName() {
            $actionParameters = new \Milantex\LimitlessBayou\ActionParameters();

            $expectedNames = [
                ':gparam_1',
                ':gparam_2',
                ':gparam_3'
            ];

            foreach ($expectedNames as $expectedName) {
                $gotName = $actionParameters->getNextGenericParameterName();
                $this->assertSame($expectedName, $gotName);
            }
        }

        public function testAddParameterWithNextGenericName() {
            $actionParameters = new \Milantex\LimitlessBayou\ActionParameters();

            $values = [
                'Milan Tair',
                'milan.tair@gmail.com',
                1988
            ];

            foreach ($values as $value) {
                $actionParameters->addParameterWithNextGenericName($value);
            }

            $index = 1;
            foreach ($values as $value) {
                $gotValue = $actionParameters->getParameter(':gparam_' . $index++);
                $this->assertSame($value, $gotValue);
            }
        }

        public function testAddParameter() {
            $actionParameters = new \Milantex\LimitlessBayou\ActionParameters();

            $values = [
                'Param1' => 'Value 1',
                'Param2' => 1988,
                ':rt' => 'Value RT'
            ];

            foreach ($values as $name => $value) {
                $actionParameters->addParameter($name, $value);
            }

            foreach ($values as $name => $value) {
                $this->assertSame($value, $actionParameters->getParameter($name));
            }
        }

        public function testGetParameterNames() {
            $actionParameters = new \Milantex\LimitlessBayou\ActionParameters();

            $keys = [':gparam_1', 'Test', 'print'];

            foreach ($keys as $key) {
                $actionParameters->addParameter($key, $key . '-value');
            }

            $this->assertEquals($keys, $actionParameters->getParameterNames(), "", 0.0, 10, true);
        }

        public function testGetParameters() {
            $actionParameters = new \Milantex\LimitlessBayou\ActionParameters();

            $values = [
                'Param1' => 'Value 1',
                'Param2' => 1988,
                ':rt' => 'Value RT'
            ];

            foreach ($values as $name => $value) {
                $actionParameters->addParameter($name, $value);
            }

            $this->assertEquals($values, $actionParameters->getParameters(), "", 0.0, 10, true);
        }
    }
