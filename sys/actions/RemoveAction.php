<?php
    namespace Milantex\LimitlessBayou\Sys\Actions;

    use Milantex\LimitlessBayou\Sys\DataBase as DataBase;
    use Milantex\LimitlessBayou\Sys\ActionParameters as ActionParameters;
    use Milantex\LimitlessBayou\Sys\ApiResponse as ApiResponse;
    
    /**
     * The RemoveAction corresponds to the remove API action. It extends the
     * BaseAction class and inherits its ability to parse the action
     * specification object before handling it in its own specific manner.
     */
    class RemoveAction extends BaseAction {   
        public function handle(\stdClass $actionSpecification) {
            $this->checkActionSpecificationValidity($actionSpecification);

            $actionParameters = new ActionParameters();
            $clause = $this->parseActionSpecification($actionSpecification->find, $actionParameters);
            $data = $actionParameters->getParameters();

            $sql = 'DELETE FROM `' . $this->getMap()->getTableName() . '` WHERE 1 AND ' . $clause . ';';
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
        private function checkActionSpecificationValidity(\stdClass $actionSpecification) {
            $vars = get_object_vars($actionSpecification);
            if (count($vars) != 1) {
                new ApiResponse(ApiResponse::STATUS_ERROR, "This action's action specification object must have exactly one property, it being named 'find'.");
            }

            if (!property_exists($actionSpecification, 'find')) {
                new ApiResponse(ApiResponse::STATUS_ERROR, "This action's action specification object must have a single property named 'find'.");
            }

            if (!is_object($actionSpecification->find)) {
                new ApiResponse(ApiResponse::STATUS_ERROR, "The 'find' property of the action specification object must be an object.");
            }
        }
    }
