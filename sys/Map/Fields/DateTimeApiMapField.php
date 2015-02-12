<?php
	class DateTimeApiMapField extends ApiMapField {
		private $minimum = NULL;
		private $maximum = NULL;

		public function __construct($name, $minimum = NULL, $maximum = NULL) {
			parent::__construct($name);

			if ($minimum != NULL) {
				$this->minimum = strtotime($minimum);
			}

			if ($maximum != NULL) {
				$this->maximum = strtotime($maximum);
			}
		}

		public function isValid($value) {
			if (!is_string($value) or !preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/', $value)) {
				return FALSE;
			}

			$value = strtotime($value);

			if ($this->minimum !== NULL and $value < $this->minimum) {
				return FALSE;
			}

			if ($this->maximum !== NULL and $this->maximum < $value) {
				return FALSE;
			}

			return TRUE;
		}

		public function describe() {
			return 'This field stores a datetime value in the format YYYY-MM-DD HH:MM:SS' .
				   (($this->minimum) !== NULL?' which cannot be smaller than ' . date('Y-m-d H:i:s', $this->minimum) : '') .
				   (($this->maximum !== NULL)?(($this->minimum !== NULL)?' and':' which') . ' cannot be larger than ' . date('Y-m-d H:i:s', $this->maximum) : '') . '.';
		}
	}
