<?php
    namespace Milantex\LimitlessBayou\Map\Fields;

    use Milantex\LimitlessBayou\Map\ApiMapField as ApiMapField;

    /**
     * This is the TEXT field type class. It implements value validation with
     * the help of regular expression matching. By default, any sequence of
     * characters is allowed.
     */
    class TextualApiMapField extends ApiMapField {
        /**
         * The regular expression pattern used for matching the string value
         * @var string
         */
        private $pattern;

        /**
         * The TextualApiMapField constructor function takes the name of the
         * field in the database table which its parent map defines, as well as
         * the pattern for value matching. If omitted, the default pattern will
         * allow for any sequence of characters to be given.
         * @param string $name The name of the field
         * @param string|NULL $pattern
         */
        public function __construct(string $name, string $pattern = NULL) {
            parent::__construct($name);
            $this->pattern = $pattern;
        }

        /**
         * Returns true if the value is a string and if it matches a particular
         * regular expression pattern (if the pattern is specified - not NULL).
         * @param mixed $value
         * @return bool
         */
        public function isValid($value) : bool {
            if (!is_string($value)) {
                return FALSE;
            }

            if ($this->pattern !== NULL) {
                return boolval(preg_match($this->pattern, $value));
            } else {
                return TRUE;
            }
        }

        /**
         * Returns a human readable description of this field.
         * If the pattern is set, includes it in the description.
         * @return string
         */
        public function describe() : string {
            return 'This field stores a text value' . (($this->pattern !== NULL)?' which must match the following regular expression: ' . $this->pattern:'') . '.';
        }

        /**
         * Returns the database equivalent type name of the type of this field
         * @return string
         */
        public function getDatabaseTypeEquivalent() : string {
            return 'TEXT';
        }
    }
