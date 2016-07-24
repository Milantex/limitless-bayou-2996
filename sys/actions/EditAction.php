<?php
    namespace Milantex\LimitlessBayou\Actions;

    use Milantex\LimitlessBayou\ActionParameters as ActionParameters;

    /**
     * The EditAction corresponds to the edit API action. It extends the
     * BaseAction class and inherits its ability to parse the action
     * specification object before handling it in its own specific manner.
     */
    class EditAction extends BaseAction {
        /**
         * Handles the action specification to edit records in the mapped table
         * @param \stdClass $actionSpecification
         */
        public function handle(\stdClass $actionSpecification) {
            $this->checkActionSpecificationValidity($actionSpecification);

            $actionParameters = new ActionParameters();
            $clause = $this->parseActionSpecification($actionSpecification->find, $actionParameters);
            $data = $actionParameters->getParameters();

            $fields = [];

            $uniqueIndex = 1;
            foreach (get_object_vars($actionSpecification->values) as $key => $value) {
                if (!$this->getMap()->fieldExists($key)) {
                    $this->getApp()->respondWithError('The specified field:' . addslashes($key) . ' does not exist in this map.');
                    continue;
                }
                
                if (!$this->getMap()->getField($key)->isValid($value)) {
                    $this->getApp()->respondWithError('The value for the field:' . addslashes($key) . ' is not valid.');
                    continue;
                }

                $fields[] = '`' . $key . '` = ' . ':update_' . $key . $uniqueIndex;
                $data[':update_' . $key . $uniqueIndex] = $value;
                $uniqueIndex++;
            }

            $sql = 'UPDATE `' . $this->getMap()->getTableName() . '` SET ' . implode(', ', $fields) . ' WHERE 1 AND ' . $clause . ';';
            $res = $this->getDatabase()->execute($sql, $data);

            if ($res) {
                $rowCount = $this->getDatabase()->getLastExecutionAffectedRownCount();
                $this->getApp()->respondWithOk($rowCount);
                return;
            }

            $this->getApp()->respondWithError($this->getDatabase()->getLastExecutionError());
        }

        /**
         * Checks if the action specification object is of valid structure
         * @param stdClass $actionSpecification
         */
        private function checkActionSpecificationValidity(\stdClass $actionSpecification) {
            $vars = get_object_vars($actionSpecification);
            if (count($vars) != 2) {
                $this->getApp()->respondWithError("This action's action specification object must have exactly two properties.");
            }

            if (!property_exists($actionSpecification, 'values')) {
                $this->getApp()->respondWithError("This action's action specification object must have the 'values' property.");
            }

            if (!is_object($actionSpecification->values)) {
                $this->getApp()->respondWithError("The 'values' property of the action specification object must be an object.");
            }

            if (!property_exists($actionSpecification, 'find')) {
                $this->getApp()->respondWithError("This action's action specification object must have the 'find' property.");
            }
        }
    }
