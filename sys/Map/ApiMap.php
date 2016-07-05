<?php
    namespace Milantex\LimitlessBayou\Map;

    use \Milantex\LimitlessBayou\LimitlessBayou as LimitlessBayou;
    use \Milantex\LimitlessBayou\RequestHandler as RequestHandler;
    use \Milantex\LimitlessBayou\ApiResponse as ApiResponse;

    /**
     * The ApiMap class represents a structured description of a single table in
     * the database with the list of its fields.
     */
    final class ApiMap {
        /**
         * The reference to the main LimitlessBayou application API instance
         * @var LimitlessBayou
         */
        private $app;

        /**
         * The name of the map, as it appears in the API request.
         * The name of the map must match the regular expression defined in this
         * class' constant named MAP_NAME_PATTERN.
         * @var string
         */
        private $name;

        /**
         * The name of the table which this map describes.
         * @var string
         */
        private $tableName;

        /**
         * A human readable name or a short description of this map.
         * @var string
         */
        private $description;

        /**
         * The name of the primary key (single) field of this map's table.
         * @var string
         */
        private $identifier = NULL;

        /**
         * The array which stores the list of table fields mapped in this map.
         * @var array
         */
        private $fields;

        /**
         * The regular expression value which is used to see if the name for the
         * map, passed to the constructor, is valid.
         */
        const MAP_NAME_PATTERN = '|^[a-z]{2,}\.[a-z][a-z0-9_]*[a-z0-9]$|';

        /**
         * The ApiMap constructor function creates an instance of a map which
         * contains the definition of the structure of the table it represents.
         * @param string $name The map name, which must match a defined pattern.
         * @param string $tableName The name of the table in the database.
         * @param string $description A human readable description of the map.
         * @throws Exception Throws an exception if the map name is not valid.
         */
        public function __construct(string $name, string $tableName, string $description) {
            if (!preg_match(ApiMap::MAP_NAME_PATTERN, $name)) {
                throw new \Exception("The name of the map does not match the required pattern: " . ApiMap::MAP_NAME_PATTERN . ".");
            }

            $this->name = $name;
            $this->tableName = $tableName;
            $this->description = $description;
            $this->fields = [];
        }

        /**
         * Returns the name of the map
         * @return string
         */
        public function getName() : string {
            return $this->name;
        }

        /**
         * Returns the name of the table which this map describes
         * @return string
         */
        public function getTableName() : string {
            return $this->tableName;
        }

        /**
         * Returns the human readable description or the short name of this map
         * @return string
         */
        public function getDescription() : string {
            return $this->description;
        }

        /**
         * Used to set the name of the primary key identifier of the map's table
         * @param string $identifier
         */
        public function setIdentifier(string $identifier) {
            $this->identifier = $identifier;
        }

        /**
         * Returns the name of the primary key identifier of this map's table
         * @return string
         */
        public function getIdentifier() : string {
            return $this->identifier;
        }

        /**
         * Used to add a new map field object to the list of this map's fields
         * @param string $name
         * @param ApiMapField $field
         */
        public function addField(string $name, ApiMapField $field) {
            $this->fields[$name] = $field;
        }

        /**
         * Checks if a field with the given name exists in this map.
         * @param string $name
         * @return bool
         */
        public function fieldExists(string $name): bool {
            return isset($this->fields[$name]);
        }

        /**
         * Returns the ApiMapField object containing information about the field
         * with the specified name. If no such field exists in the map's field
         * list, this method returns NULL.
         * @param string $name
         * @return ApiMapField|NULL
         */
        public function getField(string $name) {
            return $this->fields[$name] ?? null;
        }

        /**
         * Returns the list of this map's fields.
         * The list elements are of type ApiMapField.
         * @return array
         */
        public function getFields() : array {
            return $this->fields;
        }

        /**
         * Returns the reference to the LimitlessBayou API application instance.
         * @return LimitlessBayou
         */
        function getApp(): LimitlessBayou {
            return $this->app;
        }

        /**
         * This method checks if the action specification fed to the request
         * handler is valid and if it is, executes the request handler.
         * @param stdClass $json
         */
        public function handle(\stdClass $json, LimitlessBayou &$app) {
            $this->app = $app;

            $handler = new RequestHandler($json, $this);

            if (!$handler->isValid()) {
                $this->getApp()->respondWithError('The request is not in the valid format.');
            }

            $handler->run();
        }

        /**
         * The purpose of this method is to generate a renderable HTML code with
         * the full specification of the map's fields with their descriptions.
         * @return string
         */
        public function describe() : \stdClass {
            $descriptionObject = (object) [
                'map_name' => $this->getName(),
                'table_name' => $this->getTableName(),
                'description' => $this->getDescription(),
                'api_url' => '?map=' . $this->name,
                'fields' => [ ]
            ];

            foreach ($this->fields as $key => $field) {
                $descriptionObject->fields[] = (object) [
                    'key' => $key,
                    'name' => $field->getName(),
                    'type' => $field->getDatabaseTypeEquivalent(),
                    'description' => $field->describe()
                ];
            }

            return $descriptionObject;
        }
    }
