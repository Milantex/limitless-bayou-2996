<?php
    namespace Milantex\LimitlessBayou;

    /**
     * The ActionParameter class stores the list of parameters used in the SQL
     * query generated by the action specification object parser method of the
     * BaseAction class. This class provides a mechanism for generating generic
     * parameter names in a sequence.
     */
    class ActionParameters {
        /**
         * The list of parameters will be stored in this associative array
         * @var array
         */
        private $parameters = [];

        /**
         * The index number of the next named parameter
         * @var int
         */
        private $nextGenericParameterIndex = 1;

        /**
         * Returns the name of the next generic parameter name in the sequence.
         * @return string
         */
        public function getNextGenericParameterName() {
            return ':gparam_' . $this->nextGenericParameterIndex++;
        }

        /**
         * Adds the value to the list of parameters with the name generated by
         * the generic parameter name provider method.
         * @param mixed $value
         */
        public function addParameterWithNextGenericName($value) {
            $this->addParameter($this->getNextGenericParameterName(), $value);
        }

        /**
         * Stores the value of an SQL query parameter with a specific name into
         * the list of action parameters.
         * @param string $name
         * @param mixed $value
         */
        public function addParameter($name, $value) {
            $this->parameters[$name] = $value;
        }

        /**
         * Returns an array of strings representing all parameter names
         * currently stored in the list of parameters.
         * @return array
         */
        public function getParameterNames() {
            return array_keys($this->parameters);
        }

        /**
         * Returns the value of the parameter with the given name or an empty
         * string if no such parameter exists.
         * @param string $name
         * @return mixed
         */
        public function getParameter($name) {
            return $this->parameters[$name] ?? '';
        }

        /**
         * Returns an associative array with all parameters where the array keys
         * are the names of those parameters.
         * @return array
         */
        public function getParameters() {
            return $this->parameters;
        }
    }
