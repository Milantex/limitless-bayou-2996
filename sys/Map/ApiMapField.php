<?php
    namespace Milantex\LimitlessBayou\Map;

    /**
     * This is the base class for all API map field types.
     * Specific field types should be defined by extending this base class.
     * Each field type class must implement the validation function and the
     * description function for the particular field type.
     */
    abstract class ApiMapField {
        /**
         * The name of this particular field
         * @var string
         */
        private $name;

        /**
         * The API map field constructor function takes in the name of the field
         * @param string $name
         */
        public function __construct(string $name) {
            $this->name = $name;
        }

        /**
         * Returns the name of this field
         * @return string
         */
        public function getName() : string {
            return $this->name;
        }

        /**
         * This method should perform validation of the given value for the
         * specific field type which implements this method and return a boolean
         * value depending on the validity of the value.
         * @return bool
         */
        public abstract function isValid($value) : bool;

        /**
         * Returns a human readable description of the field type.
         * @return string
         */
        public abstract function describe() : string;

        /**
         * Returns the database equivalent type name of the type of this field
         * @return string
         */
        public abstract function getDatabaseTypeEquivalent() : string;
    }
