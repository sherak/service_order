<?php

class db_connection {

	protected $conn = null;

	function __construct() {
		$this->conn = $this->connect();
	}


	protected function connect() {
		$config = parse_ini_file('config.ini');
		$conn = mysqli_connect('localhost', $config['username'], $config['password'], $config['dbname']);
		if(mysqli_connect_errno()) {
			printf("Connection failed: %s", mysqli_connect_error());
			exit();
		}
		mysqli_query($conn, "SET NAMES 'utf8'"); 
		mysqli_query($conn, "SET CHARACTER SET 'utf8'"); 
		return $conn;
	}

	function insert_data($table, $data) {
		if(in_array("", $data))
			return False;
		$columns = array_keys($data);
		$values = array_values($data);
    	$columns = implode(',', $columns);
    	array_walk($values, function(&$v){ $v = $this->escape($v); });
    	$values = implode("','", $values);
    	$sql = "INSERT INTO " . $table . " (" . $columns . ") VALUES ('" . $values . "')";
	    $conn = $this->connect();
	    $result = mysqli_query($conn, $sql);
	    if($result === FALSE) { 
    		die(mysqli_error($conn));
		}
		$last_id = mysqli_insert_id($conn);
		mysqli_close ($conn);
		return $last_id;
  	}

  	function update_data($table, $data, $column_id) {
  		$columns = array_keys($data);
		$values = array_values($data);
  		$column_value = '';
  		foreach(array_combine($columns, $values) as $column => $value) {
  			$column_value .= $column . "=" . "'" . $value;
  			if($column === end($columns))
       			$column_value .= "'";
       		else
       			$column_value .= "',";
       	}
	    $sql = "UPDATE" . " " . $table . " SET " . $column_value . " WHERE " . $table . "." . $table . "_id=" . $column_id;
	    $result = mysqli_query($this->conn, $sql);
	    if($result === FALSE) { 
    		die(mysqli_error($this->conn));
		}
		$affected_rows = mysqli_affected_rows($this->conn);
		return $affected_rows;
  	}

  	function query($sql) {
	    $result = mysqli_query($this->conn, $sql);
	    $delete_query = explode(' ', trim($sql))[0];
	    if($result === FALSE) { 
    		die(mysqli_error($this->conn));
		}
		else {
			if($delete_query == 'DELETE')
				return true;
			return mysqli_fetch_all($result, MYSQLI_ASSOC);
		}
  	}

	function escape($str) {
		return mysqli_escape_string($this->conn, $str);
	}
}
