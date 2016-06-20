<?php
    namespace Milantex\LimitlessBayou\Sys\Map\Fields;

    use Milantex\LimitlessBayou\Sys\Map\ApiMapField as ApiMapField;

    /**
     * This is the DATETIME field type class. It supports range validation.
     * Normally, the value can be any string with an ISO formatted date value.
     * If the minimum and maximum value is set, the validation function will
     * additionally check if the value being checked fits within that range.
     */
    class DateTimeApiMapField extends ApiMapField {
        /**
         * This property will store the long (integer) UNIX timestamp value of
         * the minimum date this particular field can store. If there is no
         * minimum value, this property should remain NULL.
         * @var int
         */
        private $minimum = NULL;

        /**
         * This property will store the long (integer) UNIX timestamp value of
         * the maximum date this particular field can store. If there is no
         * maximum value, this property should remain NULL.
         * @var int
         */
        private $maximum = NULL;

        /**
         * The DateTimeApiMapField constructor function takes the name of the
         * field in the database table which its parent map defines, as well as
         * two additional, optional, parameters for the minimum and maximum date
         * that can be stored in this particular field. The minimum and maximum
         * date values should be strings with ISO formatted date values.
         * @param string $name The name of the field
         * @param string|NULL $minimum The smallest date that can be set
         * @param string|NULL $maximum The largest date that can be set
         */
        public function __construct(string $name, string $minimum = NULL, string $maximum = NULL) {
            parent::__construct($name);

            if ($minimum != NULL) {
                $this->minimum = strtotime($minimum);
            }

            if ($maximum != NULL) {
                $this->maximum = strtotime($maximum);
            }
        }

        /**
         * Performs validation of the $value parameter. If it is a string that
         * matches the ISO standard for the date it returns true. Also, if there
         * are a minimum and maximum date value, it compares them to check if
         * the value is within the defined range.
         * 
         * Note: This method does not check if the date is valid, other than to
         * see if its format is according to a simple regular expression.
         * 
         * The current implementation would theoretically allow the value such
         * as 3000-44-99 88:12:95 to be evaluated as valid.
         * TODO: Create better date validation
         * 
         * @param mixed $value The value to check
         * @return bool
         */
        public function isValid($value) : bool {
            if (!is_string($value) or !preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/', $value)) {
                return FALSE;
            }

            $timestamp = strtotime($value);

            if ($this->minimum !== NULL and $timestamp < $this->minimum) {
                return FALSE;
            }

            if ($this->maximum !== NULL and $this->maximum < $timestamp) {
                return FALSE;
            }

            return TRUE;
        }

        /**
         * Returns a human readable description of this field.
         * In case the minimum or maximum values are specified, it includes them
         * in the returned description as well.
         * @return string
         */
        public function describe() : string {
            return 'This field stores a datetime value in the format YYYY-MM-DD HH:MM:SS' .
                (($this->minimum) !== NULL?' which cannot be smaller than ' . date('Y-m-d H:i:s', $this->minimum) : '') .
                (($this->maximum !== NULL)?(($this->minimum !== NULL)?' and':' which') . ' cannot be larger than ' . date('Y-m-d H:i:s', $this->maximum) : '') . '.';
        }

        /**
         * Returns the database equivalent type name of the type of this field
         * @return string
         */
        public function getDatabaseTypeEquivalent() : string {
            return 'DATETIME';
        }
    }
