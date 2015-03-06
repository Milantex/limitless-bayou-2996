<?php
    class TextualApiMapField extends ApiMapField {
        private $pattern;

        public function __construct(string $name, string $pattern = '/^.*$/') {
            parent::__construct($name);
            $this->pattern = $pattern;
        }

        public function isValid($value) : bool {
            if (!is_string($value)) {
                return FALSE;
            }

            return boolval(preg_match($this->pattern, $value));
        }

        public function describe() : string {
            return 'This field stores a text value which must match the following regular expression: ' . $this->pattern . '.';
        }
    }
