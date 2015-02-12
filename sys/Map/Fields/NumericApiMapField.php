<?php
	class NumericApiMapField extends ApiMapField {
		private $unsigned;
		private $minimum;
		private $maximum;

		public function __construct($name, $unsigned = FALSE, $minimum = NULL, $maximum = NULL) {
			parent::__construct($name);
			$this->unsigned = $unsigned;
			$this->minimum = $minimum;
			$this->maximum = $maximum;
		}

		public function isValid($value) {
			if (!is_numeric($value)) {
				return FALSE;
			}

			if ($this->minimum !== NULL and $value < $this->minimum) {
				return FALSE;
			}

			if ($this->maximum !== NULL and $this->maximum < $value) {
				return FALSE;
			}

			if ($this->unsigned === TRUE and $value < 0) {
				return FALSE;
			}

			return TRUE;
		}

		public function describe() {
			return 'This field stores ' . (($this->unsigned !== FALSE)?'an unsigned':'a signed') .
				   ' numeric value' . (($this->minimum) !== NULL?' which cannot be smaller than ' . $this->minimum : '') .
				   (($this->maximum !== NULL)?(($this->minimum !== NULL)?' and':' which') . ' cannot be larger than ' . $this->maximum : '') . '.';
		}
	}
