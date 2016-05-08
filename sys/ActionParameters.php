<?php
    class ActionParameters {
        private $parameters = [];
        private $nextGenericParameterIndex = 1;

        public function getNextGenericParameterName() {
            return ':gparam_' . $this->nextGenericParameterIndex++;
        }

        public function addParameterWithNextGenericName($value) {
            $this->addParameter($this->getNextGenericParameterName(), $value);
        }

        public function addParameter($name, $value) {
            $this->parameters[$name] = $value;
        }

        public function getParameterNames() {
            return array_keys($this->parameters);
        }

        public function getParameter($name) {
            return $this->parameters[$name] ?? '';
        }

        public function getParameters() {
            return $this->parameters;
        }
    }
