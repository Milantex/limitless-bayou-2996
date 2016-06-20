<?php
    namespace Milantex\LimitlessBayou\Sys\Actions;

    use Milantex\LimitlessBayou\Sys\DataBase as DataBase;
    use Milantex\LimitlessBayou\Sys\ApiResponse as ApiResponse;

    /**
     * The AddAction corresponds to the add API action. It extends the
     * BaseAction class and inherits its ability to parse the action
     * specification object before handling it in its own specific manner.
     */
    class AddAction extends BaseAction {   
        public function handle(\stdClass $actionSpecification) {
            $data = [];
            $fields = [];

            foreach (get_object_vars($actionSpecification) as $key => $value) {
                if (!$this->getMap()->fieldExists($key)) {
                    new ApiResponse(ApiResponse::STATUS_ERROR, 'The specified field:' . addslashes($key) . ' does not exist in this map.');
                } elseif (!$this->getMap()->getField($key)->isValid($value)) {
                    new ApiResponse(ApiResponse::STATUS_ERROR, 'The value for the field:' . addslashes($key) . ' is not valid.');
                } else {
                    $fields[] = '`' . $key . '`';
                    $data[':' . $key] = $value;
                }
            }

            $sql = 'INSERT INTO `' . $this->getMap()->getTableName() . '` (' . implode(', ', $fields) . ') VALUES (' . implode(', ', array_keys($data)) . ');';
            $res = DataBase::execute($sql, $data);

            if ($res) {
                $recordId = DataBase::getInstance()->lastInsertId();
                new ApiResponse(ApiResponse::STATUS_OK, $recordId);
            } else {
                new ApiResponse(ApiResponse::STATUS_ERROR, DataBase::getLastExecutionError());
            }
        }
    }
