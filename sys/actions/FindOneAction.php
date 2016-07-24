<?php
    namespace Milantex\LimitlessBayou\Actions;

    use Milantex\LimitlessBayou\ActionParameters as ActionParameters;

    /**
     * The FindOneAction corresponds to the findOne API action. It extends the
     * BaseAction class and inherits its ability to parse the action
     * specification object before handling it in its own specific manner.
     */
    class FindOneAction extends BaseAction {
        /**
         * This method has the action specification object parsed into an SQL
         * array, creates a prepared statement and executes it with all of the
         * named parameters returned by the action parsing method. The SQL query
         * is written so that it can return a single value and the selectOne
         * version of the select method of the DataBase class is used to return
         * an object and send it in an API response.
         * @param stdClass $actionSpecification
         */
        public function handle(\stdClass $actionSpecification) {
            $actionParameters = new ActionParameters();
            $clause = $this->parseActionSpecification($actionSpecification, $actionParameters);
            $sql = 'SELECT * FROM `' . $this->getMap()->getTableName() . '` WHERE 1 AND ' . $clause . ' LIMIT 0, 1;';
            $result = $this->getDatabase()->selectOne($sql, $actionParameters->getParameters());

            if ($result !== NULL) {
                $this->getApp()->respondWithOk($result);
                return;
            }

            $this->getApp()->respondWithError('API request execution error.');
        }
    }
