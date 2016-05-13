<?php
    /**
     * The BaseAction class, which implements the ActionInterface implements
     * a number of method to be shared by specific action classes which extend
     * this base class. An action class is used to parse action specifications
     * sent to the API and return an action string, which is, in case of this
     * API, an SQL statement query string and a list of named parameters used
     * in the generated SQL string.
     */
    abstract class BaseAction implements ActionInterface {
        /**
         * The ApiMap object that holds information about the map which was
         * requested by the current API call whose action specification the
         * base action methods are parsing.
         * @var ApiMap
         */
        private $map;

        /**
         * The BaseAction constructor function takes only the map which it will
         * be parsing the action specification for.
         * @param ApiMap $map
         */
        final function __construct(ApiMap $map) {
            $this->map = $map;
        }

        /**
         * Returns the ApiMap specified for the current API request
         * @return ApiMap
         */
        protected function getMap() : ApiMap {
            return $this->map;
        }

        /**
         * Performs parsing of the action specification object and creates an
         * action string. Additionally, if parameters are used in the action
         * specification, it adds them to the action parameters object passed by
         * reference to this method.
         * @param stdClass $actionSpecification The action specification object
         * @param ActionParameters $actionParameters The action parameters
         * @return string The part of the SQL query made for this specification
         */
        protected function parseActionSpecification(stdClass &$actionSpecification, ActionParameters &$actionParameters) {
            $string = '';

            $actionKeys = get_object_vars($actionSpecification);

            if (count($actionKeys) != 1) {
                new ApiResponse(ApiResponse::STATUS_ERROR, 'Invalid API call. Action specification object must contain only one key.');
            }

            $actionKey = array_keys($actionKeys)[0];

            if ($actionKey === '_and') {
                if (!is_array($actionSpecification->_and)) {
                    new ApiResponse(ApiResponse::STATUS_ERROR, 'Invalid API call. Action key _and must be an array.');
                }

                $string .= $this->parseAndAction($actionSpecification->_and, $actionParameters);
            } else if ($actionKey === '_or') {
                if (!is_array($actionSpecification->_or)) {
                    new ApiResponse(ApiResponse::STATUS_ERROR, 'Invalid API call. Action key _or must be an array.');
                }

                $string .= $this->parseOrAction($actionSpecification->_or, $actionParameters);
            } else {
                $string .= $this->parseParameterValue($actionSpecification, $actionKey, $actionParameters);
            }

            return $string;
        }

        /**
         * The action specification object, at its deepest level contains a
         * parameter value specification object. This method parses the final
         * level of the action specification object and returns the part of the
         * SQL query which specifies the operation between parameters and values
         * and adds the generically created parameter placeholder name to the
         * action parameter list for this particular parameter.
         * @param stdClass $actionSpecification
         * @param string $actionKey
         * @param ActionParameters $actionParameters
         * @return string
         */
        private function parseParameterValue(stdClass &$actionSpecification, string $actionKey, ActionParameters &$actionParameters) {
            $keyNameValue = $this->getParameterKeyNameAndValue($actionSpecification, $actionKey);
            $this->checkParameterValidity($keyNameValue);
            list($parameterKey, $parameterName, $parameterValue) = $keyNameValue;

            $operator = $this->getOperator($parameterKey);

            if ($operator === '') {
                new ApiResponse(ApiResponse::STATUS_ERROR, 'Invalid API call. Invalid parameter:' . $parameterName . ' value action:' . htmlspecialchars($parameterKey)) . ' given.';
            }

            $string = ' ( ' . $actionKey . ' ' . $operator;

            if ($operator === ' LIKE ' and !$this->modifyParameterValueForLikeOperators($parameterKey, $parameterValue)) {
                new ApiResponse(ApiResponse::STATUS_ERROR, 'Invalid API call. Invalid parameter:' . $parameterName . ' value action:' . htmlspecialchars($parameterKey)) . ' given.';
            }

            $actionParameterName = $actionParameters->getNextGenericParameterName();
            $actionParameters->addParameter($actionParameterName, $parameterValue->$parameterKey);

            return $string . $actionParameterName . ' ) ';
        }

        /**
         * Extracts the parameter key, name and value for the specified action
         * key from the current action specification object (at any depth).
         * It returns these three values as three elements of an array.
         * @param stdClass $actionSpecification
         * @param string $actionKey
         * @return array
         */
        private function getParameterKeyNameAndValue(stdClass &$actionSpecification, string $actionKey) {
            if (!is_object($actionSpecification->$actionKey)) {
                new ApiResponse(ApiResponse::STATUS_ERROR, 'Invalid API call. Parameter value key must be an object.');
            }

            $parameterValue = $actionSpecification->$actionKey;
            $parameterName = $actionKey;
            $parameterValueKeys = get_object_vars($parameterValue);

            if (count($parameterValueKeys) != 1) {
                new ApiResponse(ApiResponse::STATUS_ERROR, 'Invalid API call. The parameter:' . $parameterName . ' value object must contain only one key.');
            }

            $parameterKey = array_keys($parameterValueKeys)[0];

            return [$parameterKey, $parameterName, $parameterValue];
        }

        /**
         * Takes the array returned by the getParameterKeyNameAndValue method
         * and checks if the parameter name exists in the list of fields for the
         * currently used API map and if it does, if it is valid.
         * The method does not return a value, but instead sends out an error
         * response form the API.
         * @param array $parameterKeyNameValue
         */
        private function checkParameterValidity(array &$parameterKeyNameValue) {
            list($parameterKey, $parameterName, $parameterValue) = $parameterKeyNameValue;

            $field = $this->getMap()->getField($parameterName);

            if ($field === NULL) {
                new ApiResponse(ApiResponse::STATUS_ERROR, 'Invalid API call. The requested parameter:' . $parameterName . ' does not exist in this map.');
            }

            if (!$field->isValid($parameterValue->$parameterKey)) {
                new ApiResponse(ApiResponse::STATUS_ERROR, 'Invalid API call. The parameter:' . $parameterName . ' value is invalid.');
            }
        }

        /**
         * Converts the parameter key which could represent a certain operation
         * to an SQL equivalent. If the parameter key is not an operation name
         * if may be the name of the field (parameter), so it returns an empty
         * string to indicate that this parameter key was not an operation name.
         * @param string $parameterKey
         * @return string
         */
        private function getOperator(string $parameterKey) : string {
            switch ($parameterKey) {
                case '_eq' : return ' = ';
                case '_lt' : return ' < ';
                case '_lte' : return ' <= ';
                case '_gt' : return ' > ';
                case '_gte' : return ' >= ';
                case '_ne' : return ' != ';
                case '_begins' :
                case '_ends' :
                case '_contains' : return ' LIKE ';
                default: return '';
            }
        }

        /**
         * Of all SQL operator, the LIKE operator is a result of three possible
         * parameter keys with operation names mapped to a single operator.
         * This method modifies the parameter value so that it provides a way to
         * check if the value appears at the beginning, the end or in the middle
         * of the field value.
         * @param string $parameterKey
         * @param stdClass $parameterValue
         * @return boolean
         */
        private function modifyParameterValueForLikeOperators(string $parameterKey, stdClass &$parameterValue) {
            if ($parameterKey === '_begins') {
                $parameterValue->$parameterKey .= '%';
            } else if ($parameterKey === '_ends') {
                $parameterValue->$parameterKey = '%' . $parameterValue->$parameterKey;
            } else if ($parameterKey === '_contains') {
                $parameterValue->$parameterKey = '%' . $parameterValue->$parameterKey . '%';
            } else {
                return false;
            }

            return true;
        }

        /**
         * If the action specification's key was the _and operation, this method
         * parses all elements of its value (an array) and parses them each as
         * a separate action specification option and joins the resulting SQL
         * strings with an AND keyword. It also passes the list of action
         * parameters in order to properly append with new named parameters.
         * @param array $andList
         * @param ActionParameters $actionParameters
         * @return string
         */
        private function parseAndAction(array $andList, ActionParameters &$actionParameters) {
            $items = [];

            foreach ($andList as $actionSpecification) {
                $items[] = $this->parseActionSpecification($actionSpecification, $actionParameters);
            }

            return ' ( ' . implode(' AND ', $items) . ' ) ';
        }

        /**
         * If the action specification's key was the _or operation, this method
         * parses all elements of its value (an array) and parses them each as
         * a separate action specification option and joins the resulting SQL
         * strings with an OR keyword. It also passes the list of action
         * parameters in order to properly append with new named parameters.
         */
        private function parseOrAction(array $orList, ActionParameters &$actionParameters) {
            $items = [];

            foreach ($orList as $actionSpecification) {
                $items[] = $this->parseActionSpecification($actionSpecification, $actionParameters);
            }

            return ' ( ' . implode(' OR ', $items) . ' ) ';
        }

        /**
         * This method will be used to handle the action specification object.
         * Each action class should implement its own version of this method.
         */
        public abstract function handle(stdClass $actionSpecification);
    }
