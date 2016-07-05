<?php
    namespace Milantex\LimitlessBayou;

    use Milantex\LimitlessBayou\Map\ApiMap as ApiMap;

    /**
     * The RequestHandler class initiates parsing of the action specification
     * sent in the API request. It identifies the requested action and delegates
     * further parsing and execution of the requested action to one of five
     * predefined action handler classes.
     */
    final class RequestHandler {
        /**
         * Stores the API map specified in the current API request
         * @var ApiMap
         */
        private $map;

        /**
         * Action to select a single record from the SQL query result set
         */
        const ACTION_FIND_ONE  = 'findOne';

        /**
         * Action to select all records from the SQL query result set
         */
        const ACTION_FIND_MANY = 'findMany';

        /**
         * Action to insert a single record to the table
         */
        const ACTION_ADD       = 'add';

        /**
         * Action to update values of records that match the filtering clause
         */
        const ACTION_EDIT      = 'edit';

        /**
         * Action to delete records that match the filtering clause
         */
        const ACTION_REMOVE    = 'remove';

        /**
         * An array of all available actions
         */
        const ACTIONS = [
            RequestHandler::ACTION_FIND_ONE   => '\\Milantex\\LimitlessBayou\\Actions\\FindOneAction',
            RequestHandler::ACTION_FIND_MANY  => '\\Milantex\\LimitlessBayou\\Actions\\FindManyAction',
            RequestHandler::ACTION_ADD        => '\\Milantex\\LimitlessBayou\\Actions\\AddAction',
            RequestHandler::ACTION_EDIT       => '\\Milantex\\LimitlessBayou\\Actions\\EditAction',
            RequestHandler::ACTION_REMOVE     => '\\Milantex\\LimitlessBayou\\Actions\\RemoveAction'
        ];

        /**
         * Stores the content of the API request action specification object
         * @var stdClass 
         */
        private $request;

        /**
         * The name of the action specified in the action specification object
         * @var string
         */
        private $action = NULL;

        /**
         * The name of the class that handles actions specified in the request
         * @var string
         */
        private $actionHandlerClass = NULL;

        /**
         * RequestHandler class constructor method stores the API request action
         * specification object and the API map specified in the API request URL
         * and executes the action specification object parsing method.
         * @param stdClass $request
         * @param ApiMap $map
         */
        public function __construct(\stdClass $request, ApiMap $map) {
            $this->request = $request;
            $this->map = $map;
            $this->parse();
        }

        /**
         * The parsing method identifies the requested action and sets the
         * action name and the action class handler name to appropriate values.
         */
        private function parse() {
            foreach (RequestHandler::ACTIONS as $action => $actionHandlerClass) {
                if (property_exists($this->request, $action)) {
                    $this->action = $action;
                    $this->actionHandlerClass = $actionHandlerClass;
                }
            }
        }

        /**
         * Returns TRUE if the parser found the adequate action or FALSE if the
         * parsing did not succeed in identifying the appropriate action handler
         * @return bool
         */
        public function isValid() : bool {
            if ($this->action === NULL) {
                return FALSE;
            }

            return TRUE;
        }

        /**
         * If the request was valid and the appropriate action handler class was
         * identified, this method creates an instance of that action handler
         * class and delegates further execution of the request to it.
         */
        public function run() {
            if ($this->isValid()) {
                $action = $this->action;
                $request = $this->request;
                $className = $this->actionHandlerClass;
                $actionHandler = new $className($this->map);
                $actionHandler->handle($request->$action);
            }
        }
    }
