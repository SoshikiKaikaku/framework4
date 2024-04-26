<?php

class formatter {

	private $outputfile;

	function __construct($outputfile = true) {
		$this->outputfile = $outputfile;
	}

	function format($filename, $increment_key, $decrement_key, $temp_decrement_key = null) {
		$txt = file_get_contents($filename);
		$lines = explode("\n", $txt);
		$tab_idx = 0;
		$out = "";
		$blankline_idx = 0;

		foreach ($lines as $line) {

			$line_tmp = trim($line);

			if ($line_tmp == "") {
				$blankline_idx++;
				if ($blankline_idx > 1) {
					$blankline_idx = 0;
					continue;
				}
			}

			if ($temp_decrement_key != null && $this->has_key($line_tmp, $temp_decrement_key)) {
				$tab_idx--;
				$out .= $this->make_tab($tab_idx) . $line_tmp . "\n";
				$tab_idx++;
			} else if ($this->has_key($line_tmp, $increment_key)) {
				// Found increment key
				if ($this->has_key($line_tmp, $decrement_key)) {
					// Found decrement key in this line
					$tab_idx--;
				}

				$out .= $this->make_tab($tab_idx) . $line_tmp . "\n";
				$tab_idx++;
			} else if ($this->has_key($line_tmp, $decrement_key)) {
				// Found decrement key
				$tab_idx--;
				$out .= $this->make_tab($tab_idx) . $line_tmp . "\n";
			} else {
				$out .= $this->make_tab($tab_idx) . $line_tmp . "\n";
			}
			if ($tab_idx < 0) {
				$tab_idx = 0;
			}
		}

		if ($this->outputfile) {
			file_put_contents($filename, $out);
		} else {
			return $out;
			// echo $out;
		}
	}

	function has_key($line, $keys) {
		$arr = array();
		if (!is_array($keys)) {
			$arr[] = $keys;
		} else {
			$arr = $keys;
		}

		foreach ($arr as $k) {
			if (strpos($line, $k) !== false) {
				return true;
			}
		}

		return false;
	}

	function make_tab($tab_idx) {
		$tab = "";
		for ($i = 0; $i < $tab_idx; $i++) {
			$tab .= "\t";
		}
		return $tab;
	}
}
