<?php
	class TextualApiMapField extends ApiMapField {
		private $pattern;

		public function __construct($name, $pattern = '/^.*$/') {
			parent::__construct($name);
			$this->pattern = $pattern;
		}

		public function isValid($value) {
			if (!is_string($value)) {
				return FALSE;
			}

			return preg_match($this->pattern, $value);
		}

		public function describe() {
			return 'This field stores a text value which must match the following regular expression: ' . $this->pattern . '.';
		}
	}
