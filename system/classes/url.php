<?php
class Url {
	var $full;
	var $array;
	
	function Url() {
		$this->full = substr(strstr($_SERVER["REQUEST_URI"],'/'),1);
		$this->array = explode("/",$this->full);
	}
	
	function GetFull() {
		return $this->full;
	}
	
	function Get($n) {
		if(isset($this->array[$n])) {
			return $this->array[$n];
		}
		
		return '';
	}
	
	function Count() {
		return count($this->array);
	}
	
	function More($n) {
		if($this->Count() > $n && $this->Get($n) == "") {
			//OK
			return 0;
		}else{
			return 1;
		}
	}
	
	function GetAll() {
		return $this->array;
	}
	
	function Error($x) {
		global $language;
		
		if($x) {
			header('Location: /'.$language->Get().'/'.$x.'/');
		}else{
			header('Location: /');
		}
	}
}
?>