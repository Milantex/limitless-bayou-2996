<?php
    namespace Milantex\LimitlessBayou\Sys\Actions;

    /**
     * The AddAction corresponds to the add API action. It extends the
     * BaseAction class and inherits its ability to parse the action
     * specification object before handling it in its own specific manner.
     */
    class AddAction extends BaseAction {
        /**
         * Handles the action specification to add a record to the mapped table
         * @param \stdClass $actionSpecification
         */
        public function handle(\stdClass $actionSpecification) {
            $data = [];
            $fields = [];

            foreach (get_object_vars($actionSpecification) as $key => $value) {
                if (!$this->getMap()->fieldExists($key)) {
                    $this->getApp()->respondWithError('The specified field:' . addslashes($key) . ' does not exist in this map.');
                } elseif (!$this->getMap()->getField($key)->isValid($value)) {
                    $this->getApp()->respondWithError('The value for the field:' . addslashes($key) . ' is not valid.');
                } else {
                    $fields[] = '`' . $key . '`';
                    $data[':' . $key] = $value;
                }
            }

            $sql = 'INSERT INTO `' . $this->getMap()->getTableName() . '` (' . implode(', ', $fields) . ') VALUES (' . implode(', ', array_keys($data)) . ');';
            $res = $this->getDatabase()->execute($sql, $data);

            if ($res) {
                $recordId = $this->getDatabase()->getLastInsertId();
                $this->getApp()->respondWithOk($recordId);
            } else {
                $this->getApp()->respondWithError($this->getDatabase()->getLastExecutionError());
            }
        }
    }
