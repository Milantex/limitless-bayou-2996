<?php
    namespace Milantex\LimitlessBayou\Actions;

    use Milantex\LimitlessBayou\ActionParameters as ActionParameters;
    
    /**
     * The FindManyAction corresponds to the findMany API action. It extends the
     * BaseAction class and inherits its ability to parse the action
     * specification object before handling it in its own specific manner.
     */
    class FindManyAction extends BaseAction {   
        /**
         * This method has the action specification object parsed into an SQL
         * array, creates a prepared statement and executes it with all of the
         * named parameters returned by the action parsing method. The SQL query
         * is written so that it can return multiple values and the selectMany
         * version of the select method of the DataBase class is used to return
         * an array of results and send them in an API response.
         * @param stdClass $actionSpecification
         */
        public function handle(\stdClass $actionSpecification) {
            $actionParameters = new ActionParameters();
            $clause = $this->parseActionSpecification($actionSpecification, $actionParameters);
            $sql = 'SELECT * FROM `' . $this->getMap()->getTableName() . '` WHERE 1 AND ' . $clause . ';';
            $result = $this->getDatabase()->selectMany($sql, $actionParameters->getParameters());
            if ($result !== NULL) {
                $this->getApp()->respondWithOk($result);
            } else {
                $this->getApp()->respondWithError('API request execution error.');
            }
        }
    }
