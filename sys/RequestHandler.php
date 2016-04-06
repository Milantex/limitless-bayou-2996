<?php
    final class RequestHandler {
        const ACTION_FIND_ONE  = 'findOne';
        const ACTION_FIND_MANY = 'findMany';
        const ACTION_ADD       = 'add';
        const ACTION_EDIT      = 'edit';
        const ACTION_REMOVE    = 'remove';

        const ACTIONS = [
            RequestHandler::ACTION_FIND_ONE   => 'FindOneAction',
            RequestHandler::ACTION_FIND_MANY  => 'FindManyAction',
            RequestHandler::ACTION_ADD        => 'AddAction',
            RequestHandler::ACTION_EDIT       => 'EditAction',
            RequestHandler::ACTION_REMOVE     => 'RemoveAction'
        ];

        private $request;
        private $action = NULL;
        private $actionHandlerClass = NULL;

        public function __construct(stdClass $request) {
            $this->request = $request;
            $this->parse();
        }

        private function parse() {
            foreach (RequestHandler::ACTIONS as $action => $actionHandlerClass) {
                if (property_exists($this->request, $action)) {
                    $this->action = $action;
                    $this->actionHandlerClass = $actionHandlerClass;
                }
            }
        }

        public function isValid() : bool {
            if ($this->action === NULL) {
                return FALSE;
            }

            return TRUE;
        }

        public function run() {
            if ($this->isValid()) {
                $action = $this->action;
                $request = $this->request;
                $actionHandler = new $this->actionHandlerClass;
                $actionHandler->handle($request->$action);
            }
        }
    }
