<?php
    final class ApiMap {
        private $name;
        private $description;
        private $identifier = NULL;
        private $fields;

        const MAP_NAME_PATTERN = '|^[a-z]{2,}\.[a-z][a-z0-9_]*[a-z0-9]$|';

        public function __construct(string $name, string $description) {
            if (!preg_match(ApiMap::MAP_NAME_PATTERN, $name)) {
                throw new Exception("The name of the map does not match the required pattern: " . ApiMap::MAP_NAME_PATTERN . ".");
            }

            $this->name = $name;
            $this->description = $description;
            $this->fields = [];
        }

        public function getName() : string {
            return $this->name;
        }

        public function getDescription() : string {
            return $this->description;
        }

        public function setIdentifier(string $identifier) {
            $this->identifier = $identifier;
        }

        public function getIdentifier() : string {
            return $this->identifier;
        }

        public function addField($name, ApiMapField $field) {
            $this->fields[$name] = $field;
        }

        public function getField($name) : ApiMapField {
            return $this->fields[$name] ?? null;
        }

        public function getFields() : array {
            return $this->fields;
        }

        public function handle(stdClass $json) {
            $handler = new RequestHandler($json);

            if (!$handler->isValid()) {
                new ApiResponse(ApiResponse::STATUS_ERROR, 'The request is not in the valid format.');
            }

            $handler->run();
        }

        public function describe() : string {
            $documentation = '';
            $documentation .= '<table class="map">';
            $documentation .= '<tr>';
            $documentation .= '<th colspan="2">' . htmlspecialchars($this->description) . '</th>';
            $documentation .= '</tr>';
            $documentation .= '<tr>';
            $documentation .= '<td class="api-link" colspan="2"><a href="?map=' . htmlspecialchars($this->name) . '">?map=' . htmlspecialchars($this->name) . '</a></td>';
            $documentation .= '</tr>';
            $documentation .= '<tr>';
            $documentation .= '<th colspan="2">Fields:</th>';
            $documentation .= '</tr>';
            foreach ($this->fields as $field) {
                $documentation .= '<tr>';
                $documentation .= '<th>' . htmlspecialchars($field->getName()) . '</th>';
                $documentation .= '<td>' . htmlspecialchars($field->describe()) . '</td>';
                $documentation .= '</tr>';
            }
            $documentation .= '</table>';
            return $documentation;
        }
    }
