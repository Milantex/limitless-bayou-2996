<?php
    namespace Milantex\LimitlessBayou;

    /**
     * The ActionInterface specifies that all action classes must implement
     * a handle method which takes an action specification object as an argument
     */
    interface ActionInterface {
        /**
         * This method should handle action specifications for a specific action
         * @param \stdClass $actionSpecification
         */
        public function handle(\stdClass $actionSpecification);
    }
