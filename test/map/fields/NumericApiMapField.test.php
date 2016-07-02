<?php
    use Milantex\LimitlessBayou\Sys\Map\Fields\NumericApiMapField;

    class NumericApiMapFieldTest extends PHPUnit_Framework_TestCase {
        private $field;

        public function setUp() {
            $this->field = new NumericApiMapField('author_id', TRUE, 10, 100);
        }

        public function testConstructor() {
            $this->assertInstanceOf('\\Milantex\\LimitlessBayou\\Sys\\Map\\Fields\\NumericApiMapField', $this->field);
        }

        public function testIsValid() {
            $this->assertTrue($this->field->isValid(10));
            $this->assertTrue($this->field->isValid(100));
            $this->assertNotTrue($this->field->isValid(0));
            $this->assertNotTrue($this->field->isValid(-1));
            $this->assertNotTrue($this->field->isValid(101));
            $this->assertNotTrue($this->field->isValid('ABC'));
            $this->assertNotTrue($this->field->isValid([]));
            $this->assertNotTrue($this->field->isValid($this->field));
        }

        public function testGetDatabaseTypeEquivalent() {
            $this->assertSame('INTEGER', $this->field->getDatabaseTypeEquivalent());
        }

        public function testDescribe() {
            $options = [
                # unsigned, min,  max, description
                [ FALSE,    NULL, NULL, 'This field stores a signed numeric value.' ],
                [ FALSE,    NULL,  101, 'This field stores a signed numeric value which cannot be larger than 101.' ],
                [ FALSE,       1, NULL, 'This field stores a signed numeric value which cannot be smaller than 1.' ],
                [ FALSE,       1,  101, 'This field stores a signed numeric value which cannot be smaller than 1 and cannot be larger than 101.' ],
                [  TRUE,    NULL, NULL, 'This field stores an unsigned numeric value.' ],
                [  TRUE,    NULL,  101, 'This field stores an unsigned numeric value which cannot be larger than 101.' ],
                [  TRUE,       1, NULL, 'This field stores an unsigned numeric value which cannot be smaller than 1.' ],
                [  TRUE,       1,  101, 'This field stores an unsigned numeric value which cannot be smaller than 1 and cannot be larger than 101.' ]
            ];

            foreach ($options as $option) {
                $field = new NumericApiMapField('number', $option[0], $option[1], $option[2]);
                $this->assertSame($option[3], $field->describe());
            }
        }
    }
