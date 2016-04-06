<?php
    abstract class BaseAction implements ActionInterface {
        public function handle(stdClass $actionSpecification);
    }
