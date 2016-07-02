<?php
    use Milantex\LimitlessBayou\Sys\Map\Fields\TextualApiMapField;

    class TextualApiMapFieldTest extends PHPUnit_Framework_TestCase {
        private $field;

        public function setUp() {
            $this->field = new TextualApiMapField('model', '|^[A-z0-9]{2,3}$|');
        }

        public function testConstructor() {
            $this->assertInstanceOf('\\Milantex\\LimitlessBayou\\Sys\\Map\\Fields\\TextualApiMapField', $this->field);
        }

        public function testIsValid() {
            $this->assertTrue($this->field->isValid('AB'));
            $this->assertTrue($this->field->isValid('ABC'));
            $this->assertTrue($this->field->isValid('aB'));
            $this->assertTrue($this->field->isValid('AbC'));
            $this->assertTrue($this->field->isValid('12'));
            $this->assertTrue($this->field->isValid('123'));
            $this->assertNotTrue($this->field->isValid('A'));
            $this->assertNotTrue($this->field->isValid('a'));
            $this->assertNotTrue($this->field->isValid('ABCDE'));
            $this->assertNotTrue($this->field->isValid('abcde'));
            $this->assertNotTrue($this->field->isValid('1'));
            $this->assertNotTrue($this->field->isValid('12345'));
        }

        public function testGetDatabaseTypeEquivalent() {
            $this->assertSame('TEXT', $this->field->getDatabaseTypeEquivalent());
        }

        public function testDescribe() {
            $noPattern = new TextualApiMapField('model');
            $hasPattern = $this->field;

            $noPatternDescription = 'This field stores a text value.';
            $this->assertEquals($noPatternDescription, $noPattern->describe());

            $hasPatternDescription = 'This field stores a text value which must match the following regular expression: |^[A-z0-9]{2,3}$|.';
            $this->assertEquals($hasPatternDescription, $hasPattern->describe());
        }
    }
