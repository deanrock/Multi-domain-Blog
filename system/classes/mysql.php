<?php
class MySql {
	var $count;
	var $time;
	var $query;
	var $result;
	function MySql(){
		$this->count = 0;
	}
	
	function Query($query, $array = "", $save = 1) {
		global $string;
		
		if(is_array($array)) {
			foreach($array as $key => $item) {
				$query = str_replace("@".$key, "'".String::Protect($item)."'", $query);
				$query = str_replace("#".$key, String::Protect($item), $query);
			}
		}
		
		$this->count++;
		
		$this->query = $query;
		
		$s_t = Optimization::GetTime();
		$return = mysql_query($query);
		if($save) $this->result = $return;
		$e_t = Optimization::GetTime();
		
		$this->time += Optimization::Difference($s_t, $e_t);
		
		return $return;
	}
	
	function Insert($table, $array) {
		global $string;
		
		$query = "INSERT INTO ".$table." (";
		$keys = "";
		$items = "";
		
		if(is_array($array)) {
			$i = 0;
			foreach($array as $key => $item) {
				if($i > 0) {
					$keys .= ",";
					$items .= ",";
				}
				
				$keys .= $key;
				
				$items .= "'".String::Protect($item)."'";
				
				$i++;
			}
			
			$query .= $keys.") VALUES (".$items.")";
			
			$this->Query($query);
		}
	}
	
	function InsertId() {
		return mysql_insert_id();
	}
	
	function NumRows($query) {
		return mysql_num_rows($query);
	}
	
	function FetchArray($query="") {
		if($query == "") {
			return mysql_fetch_array($this->result);
		}
		return mysql_fetch_array($query);
	}
	
	function Count() {
		return $this->count;
	}
	
	function LastQuery() {
		return $this->query;
	}
}
?>