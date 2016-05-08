<?php
    abstract class BaseAction implements ActionInterface {
        private $map;

        final function __construct(ApiMap $map) {
            $this->map = $map;
        }

        protected function getMap() : ApiMap {
            return $this->map;
        }

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

        private function parseAndAction(array $andList, ActionParameters &$actionParameters) {
            $items = [];

            foreach ($andList as $actionSpecification) {
                $items[] = $this->parseActionSpecification($actionSpecification, $actionParameters);
            }

            return ' ( ' . implode(' AND ', $items) . ' ) ';
        }

        private function parseOrAction(array $orList, ActionParameters &$actionParameters) {
            $items = [];

            foreach ($orList as $actionSpecification) {
                $items[] = $this->parseActionSpecification($actionSpecification, $actionParameters);
            }

            return ' ( ' . implode(' OR ', $items) . ' ) ';
        }

        public abstract function handle(stdClass $actionSpecification);
    }
