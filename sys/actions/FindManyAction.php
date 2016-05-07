<?php
    class FindManyAction extends BaseAction {   
        public function handle(stdClass $actionSpecification) {
            $clause = $this->parseActionSpecification($actionSpecification);
            $sql = 'SELECT * FROM `' . $this->getMap()->getTableName() . '` WHERE 1 AND ' . $clause . ';';
            $result = DataBase::selectMany($sql);
            if ($result !== NULL) {
                new ApiResponse(ApiResponse::STATUS_OK, $result);
            } else {
                new ApiResponse(ApiResponse::STATUS_ERROR, 'API request execution error.');
            }
        }
    }
