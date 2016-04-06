<?php
    abstract class BaseAction implements ActionInterface {
        public abstract function handle(stdClass $actionSpecification);
    }
