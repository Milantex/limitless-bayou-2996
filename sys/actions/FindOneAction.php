<?php
    class FindOneAction extends BaseAction {
        public function handle(stdClass $actionSpecification) {
            $actionParameters = new ActionParameters();
            $clause = $this->parseActionSpecification($actionSpecification, $actionParameters);
            $sql = 'SELECT * FROM `' . $this->getMap()->getTableName() . '` WHERE 1 AND ' . $clause . ' LIMIT 0, 1;';
            $result = DataBase::selectOne($sql, $actionParameters->getParameters());
            if ($result !== NULL) {
                new ApiResponse(ApiResponse::STATUS_OK, $result);
            } else {
                new ApiResponse(ApiResponse::STATUS_ERROR, 'API request execution error.');
            }
        }
    }
