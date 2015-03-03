<?php
    abstract class ApiMapField {
        private $name;

        public function __construct($name) {
            $this->name = $name;
        }

        public function getName() {
            return $this->name;
        }

        public abstract function isValid($value);

        public abstract function describe();
    }
