<?php
    /**
     * The EditAction corresponds to the edit API action. It extends the
     * BaseAction class and inherits its ability to parse the action
     * specification object before handling it in its own specific manner.
     */
    class EditAction extends BaseAction {   
        public function handle(stdClass $actionSpecification) {
            $this->checkActionSpecificationValidity($actionSpecification);

            $actionParameters = new ActionParameters();
            $clause = $this->parseActionSpecification($actionSpecification->find, $actionParameters);
            $data = $actionParameters->getParameters();

            $fields = [];

            $uniqueIndex = 1;
            foreach (get_object_vars($actionSpecification->values) as $key => $value) {
                if (!$this->getMap()->fieldExists($key)) {
                    new ApiResponse(ApiResponse::STATUS_ERROR, 'The specified field:' . addslashes($key) . ' does not exist in this map.');
                } elseif (!$this->getMap()->getField($key)->isValid($value)) {
                    new ApiResponse(ApiResponse::STATUS_ERROR, 'The value for the field:' . addslashes($key) . ' is not valid.');
                } else {
                    $fields[] = '`' . $key . '` = ' . ':update_' . $key . $uniqueIndex;
                    $data[':update_' . $key . $uniqueIndex] = $value;
                    $uniqueIndex++;
                }
            }

            $sql = 'UPDATE `' . $this->getMap()->getTableName() . '` SET ' . implode(', ', $fields) . ' WHERE 1 AND ' . $clause . ';';
            $res = DataBase::execute($sql, $data);

            if ($res) {
                $rowCount = DataBase::getLastExecutionAffectedRownCount();
                new ApiResponse(ApiResponse::STATUS_OK, $rowCount);
            } else {
                new ApiResponse(ApiResponse::STATUS_ERROR, DataBase::getLastExecutionError());
            }
        }

        /**
         * Checks if the action specification object is of valid structure
         * @param stdClass $actionSpecification
         */
        private function checkActionSpecificationValidity(stdClass $actionSpecification) {
            $vars = get_object_vars($actionSpecification);
            if (count($vars) != 2) {
                new ApiResponse(ApiResponse::STATUS_ERROR, "This action's action specification object must have exactly two properties.");
            }

            if (!property_exists($actionSpecification, 'values')) {
                new ApiResponse(ApiResponse::STATUS_ERROR, "This action's action specification object must have the 'values' property.");
            }

            if (!is_object($actionSpecification->values)) {
                new ApiResponse(ApiResponse::STATUS_ERROR, "The 'values' property of the action specification object must have be an object.");
            }

            if (!property_exists($actionSpecification, 'find')) {
                new ApiResponse(ApiResponse::STATUS_ERROR, "This action's action specification object must have the 'find' property.");
            }
        }
    }
