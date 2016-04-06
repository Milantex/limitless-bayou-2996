<?php
    class FindOneAction extends BaseAction {   
        public function handle(stdClass $actionSpecification) {
            // TODO: Parse predefined findOne action specification
            echo json_encode($actionSpecification);
            exit;
        }
    }
