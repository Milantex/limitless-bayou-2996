<?php
    use Milantex\LimitlessBayou\Map\Fields\DateTimeApiMapField;

    class DateTimeApiMapFieldTest extends PHPUnit_Framework_TestCase {
        private $field;

        public function setUp() {
            $this->field = new DateTimeApiMapField('timestamp', NULL, '2016-07-02 00:00:00');
        }

        public function testConstructor() {
            $this->assertInstanceOf('\\Milantex\\LimitlessBayou\\Map\\Fields\\DateTimeApiMapField', $this->field);
        }

        public function testIsValid() {
            $this->assertTrue($this->field->isValid('2016-07-01 09:47:00'));
            $this->assertNotTrue($this->field->isValid('2016-07-03 09:47:00'));
            $this->assertTrue($this->field->isValid('2000-01-01 09:47:00'));
            $this->assertNotTrue($this->field->isValid('20160703094700'));
        }

        public function testGetDatabaseTypeEquivalent() {
            $this->assertSame('DATETIME', $this->field->getDatabaseTypeEquivalent());
        }

        public function testDescribe() {
            $min = '2016-07-02 00:00:00';
            $max = '2016-07-02 23:59:59';

            $noMinNoMax   = new DateTimeApiMapField('field1');
            $hasMinNoMax  = new DateTimeApiMapField('field1', $min);
            $noMinHasMax  = new DateTimeApiMapField('field1', NULL, $max);
            $hasMinHasMax = new DateTimeApiMapField('field1', $min, $max);

            $noMinNoMaxDescription = 'This field stores a datetime value in the format YYYY-MM-DD HH:MM:SS' . (FALSE?' which cannot be smaller than ' . $min : '') . (FALSE?(FALSE?' and':' which') . ' cannot be larger than ' . $max : '') . '.';
            $this->assertEquals($noMinNoMaxDescription, $noMinNoMax->describe());

            $hasMinNoMaxDescription = 'This field stores a datetime value in the format YYYY-MM-DD HH:MM:SS' . (TRUE?' which cannot be smaller than ' . $min : '') . (FALSE?(TRUE?' and':' which') . ' cannot be larger than ' . $max : '') . '.';
            $this->assertEquals($hasMinNoMaxDescription, $hasMinNoMax->describe());

            $noMinHasMaxDescription = 'This field stores a datetime value in the format YYYY-MM-DD HH:MM:SS' . (FALSE?' which cannot be smaller than ' . $min : '') . (TRUE?(FALSE?' and':' which') . ' cannot be larger than ' . $max : '') . '.';
            $this->assertEquals($noMinHasMaxDescription, $noMinHasMax->describe());

            $hasMinHasMaxDescription = 'This field stores a datetime value in the format YYYY-MM-DD HH:MM:SS' . (TRUE?' which cannot be smaller than ' . $min : '') . (TRUE?(TRUE?' and':' which') . ' cannot be larger than ' . $max : '') . '.';
            $this->assertEquals($hasMinHasMaxDescription, $hasMinHasMax->describe());
        }
    }
