<?php
    /**
     * The ActionInterface specifies that all action classes must implement
     * a handle method which takes an action specification object as an argument
     */
    interface ActionInterface {
        public function handle(stdClass $actionSpecification);
    }
