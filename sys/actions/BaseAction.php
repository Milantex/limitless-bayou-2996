<?php
    abstract class BaseAction implements ActionInterface {
        private $map;

        final function __construct(ApiMap $map) {
            $this->map = $map;
        }

        protected function getMap() : ApiMap {
            return $this->map;
        }

        protected function parseActionSpecification(stdClass &$actionSpecification) {
            $string = '';

            $actionKeys = get_object_vars($actionSpecification);

            if (count($actionKeys) != 1) {
                new ApiResponse(ApiResponse::STATUS_ERROR, 'Invalid API call. Action specification object must contain only one key.');
            }

            $actionNames = array_keys($actionKeys);
            $actionKey = $actionNames[0];

            if ($actionKey === '_and') {
                if (!is_array($actionSpecification->_and)) {
                    new ApiResponse(ApiResponse::STATUS_ERROR, 'Invalid API call. Action key _and must be an array.');
                }

                $string .= $this->parseAndAction($actionSpecification->_and);
            } else if ($actionKey === '_or') {
                if (!is_array($actionSpecification->_or)) {
                    new ApiResponse(ApiResponse::STATUS_ERROR, 'Invalid API call. Action key _or must be an array.');
                }

                $string .= $this->parseOrAction($actionSpecification->_or);
            } else {
                if (!is_object($actionSpecification->$actionKey)) {
                    new ApiResponse(ApiResponse::STATUS_ERROR, 'Invalid API call. Parameter value key must be an object.');
                }

                $string .= ' ( ' . $actionKey . ' ' . $this->parseParameterValue($actionSpecification->$actionKey, $actionKey) . ' ) ';
            }

            return $string;
        }

        protected function parseParameterValue(stdClass $parameterValue, string $parameterName) {
            $string = '';

            $parameterValueKeys = get_object_vars($parameterValue);

            if (count($parameterValueKeys) != 1) {
                new ApiResponse(ApiResponse::STATUS_ERROR, 'Invalid API call. The parameter:' . $parameterName . ' value object must contain only one key.');
            }

            $parameterValueNames = array_keys($parameterValueKeys);
            $parameterKey = $parameterValueNames[0];

            $field = $this->getMap()->getField($parameterName);

            if ($field === NULL) {
                new ApiResponse(ApiResponse::STATUS_ERROR, 'Invalid API call. The requested parameter:' . $parameterName . ' does not exist in this map.');
            }

            if (!$field->isValid($parameterValue->$parameterKey)) {
                new ApiResponse(ApiResponse::STATUS_ERROR, 'Invalid API call. The parameter:' . $parameterName . ' value is invalid.');
            }            

            if ($parameterKey === '_eq') {
                $string .= ' = \'' . $parameterValue->$parameterKey . '\'';
            } else if ($parameterKey === '_lt') {
                $string .= ' < \'' . $parameterValue->$parameterKey . '\'';
            } else if ($parameterKey === '_lte') {
                $string .= ' <= \'' . $parameterValue->$parameterKey . '\'';
            } else if ($parameterKey === '_gt') {
                $string .= ' > \'' . $parameterValue->$parameterKey . '\'';
            } else if ($parameterKey === '_gte') {
                $string .= ' >= \'' . $parameterValue->$parameterKey . '\'';
            } else if ($parameterKey === '_ne') {
                $string .= ' != \'' . $parameterValue->$parameterKey . '\'';
            } else if ($parameterKey === '_begins') {
                $string .= ' LIKE \'' . $parameterValue->$parameterKey . '%\'';
            } else if ($parameterKey === '_ends') {
                $string .= ' LIKE \'%' . $parameterValue->$parameterKey . '\'';
            } else if ($parameterKey === '_contains') {
                $string .= ' LIKE \'%' . $parameterValue->$parameterKey . '%\'';
            } else {
                new ApiResponse(ApiResponse::STATUS_ERROR, 'Invalid API call. Invalid parameter:' . $parameterName . ' value action:' . htmlspecialchars($parameterKey)) . ' given.';
            }

            return $string;
        }

        protected function parseAndAction(array $andList) {
            $items = [];

            foreach ($andList as $actionSpecification) {
                $items[] = $this->parseActionSpecification($actionSpecification);
            }

            return ' ( ' . implode(' AND ', $items) . ' ) ';
        }

        protected function parseOrAction(array $orList) {
            $items = [];

            foreach ($orList as $actionSpecification) {
                $items[] = $this->parseActionSpecification($actionSpecification);
            }

            return ' ( ' . implode(' OR ', $items) . ' ) ';
        }

        public abstract function handle(stdClass $actionSpecification);
    }
