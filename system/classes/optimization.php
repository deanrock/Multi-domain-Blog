<?php
class Optimization {
	function Optimization() {}
	
	function GetTime() {
		$starttime = explode(' ', microtime());
		$starttime = $starttime[1] + $starttime[0];
		
		return $starttime;
	}
	
	function Difference($start, $end) {
		return $end - $start;
	}
}
?>