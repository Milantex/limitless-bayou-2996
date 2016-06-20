<?php
    namespace Milantex\LimitlessBayou\Sys\Map\Fields;

    use Milantex\LimitlessBayou\Sys\Map\ApiMapField as ApiMapField;

    /**
     * This is the NUMBER field type class. It supports range validation and
     * can check if the value is positive or negative, in case the unsigned
     * flag is set or not.
     */
    class NumericApiMapField extends ApiMapField {
        /**
         * This flag specifies if the field can store only unsigned values if
         * it is set to TRUE or if negative (signed) values are also permitted
         * if its value is set to FALSE. By default, this value is FASLE.
         * @var bool
         */
        private $unsigned;

        /**
         * The smallest value that can be stored in this field.
         * If NULL, this range indicator will not be considered for validation.
         * @var int
         */
        private $minimum;

        /**
         * The biggest value that can be stored in this field.
         * If NULL, this range indicator will not be considered for validation.
         * @var int
         */
        private $maximum;

        /**
         * The NumericApiMapField constructor function takes the name of the
         * field in the database table which its parent map defines, as well as
         * three additional, optional, parameters for the unsigned flag and the
         * minimum and maximum number values that can be stored in this field.
         * @param string $name The name of the field
         * @param bool $unsigned Are only positive values allowed
         * @param int $minimum The smallest value that can be stored
         * @param int $maximum The biggest value that can be stored
         */
        public function __construct(string $name, bool $unsigned = FALSE, int $minimum = NULL, int $maximum = NULL) {
            parent::__construct($name);
            $this->unsigned = $unsigned;
            $this->minimum = $minimum;
            $this->maximum = $maximum;
        }

        /**
         * Performs validation of the $value parameter. If it is a numeric value
         * between the minimum and maximum range values (if they are set) and if
         * the value is positive if the unsigned flag is set, this method will
         * return true. Otherwise, it will return false.
         * 
         * @param mixed $value The value to check
         * @return bool
         */
        public function isValid($value) : bool {
            if (!is_numeric($value) or
                ($this->minimum !== NULL and $value < $this->minimum) or
                ($this->maximum !== NULL and $this->maximum < $value) or
                ($this->unsigned === TRUE and $value < 0)) {
                return FALSE;
            }

            return TRUE;
        }

        /**
         * Returns a human readable description of this field.
         * In case the minimum, maximum or unsigned values are specified, it
         * includes them in the returned description as well.
         * @return string
         */
        public function describe() : string {
            return 'This field stores ' . (($this->unsigned !== FALSE)?'an unsigned':'a signed') .
                ' numeric value' . (($this->minimum) !== NULL?' which cannot be smaller than ' . $this->minimum : '') .
                (($this->maximum !== NULL)?(($this->minimum !== NULL)?' and':' which') . ' cannot be larger than ' . $this->maximum : '') . '.';
        }

        /**
         * Returns the database equivalent type name of the type of this field
         * @return string
         */
        public function getDatabaseTypeEquivalent() : string {
            return 'INTEGER';
        }
    }
