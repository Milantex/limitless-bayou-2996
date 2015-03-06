<?php
    abstract class ApiMapField {
        private $name;

        public function __construct(string $name) {
            $this->name = $name;
        }

        public function getName() : string {
            return $this->name;
        }

        public abstract function isValid($value) : bool;

        public abstract function describe() : string;
    }
