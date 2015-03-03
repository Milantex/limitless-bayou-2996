<?php
    final class ApiMap {
        private $name;
        private $description;
        private $identifier = NULL;
        private $fields;

        public function __construct($name, $description) {
            $this->name = $name;
            $this->description = $description;
            $this->fields = array();
        }

        public function getName() {
            return $this->name;
        }

        public function getDescription() {
            return $this->description;
        }

        public function setIdentifier($identifier) {
            $this->identifier = $identifier;
        }

        public function getIdentifier() {
            return $this->identifier;
        }

        public function addField($name, $field) {
            $this->fields[$name] = $field;
        }

        public function getField($name) {
            if (isset($this->fields[$name])) {
                return $this->fields[$name];
            }
        }

        public function getFields() {
            return $this->fields;
        }

        public function describe() {
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
            foreach ($this->fields as $name => $field) {
                $documentation .= '<tr>';
                $documentation .= '<th>' . htmlspecialchars($field->getName()) . '</th>';
                $documentation .= '<td>' . htmlspecialchars($field->describe()) . '</td>';
                $documentation .= '</tr>';
            }
            $documentation .= '</table>';
            return $documentation;
        }
    }
