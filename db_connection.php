<?php

class db_connection {

	function connect() {
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
    	$values = implode("','", $values);
    	$sql = "INSERT INTO " . $table . " (" . $columns . ") VALUES ('" . $values . "')";
	    $conn = $this->connect();
	    $result = mysqli_query($conn, $sql);
	    if($result === FALSE) { 
    		die(mysqli_error($conn));
		}
		mysqli_close ($conn);
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
	    $conn = $this->connect();
	    $result = mysqli_query($conn, $sql);
	    if($result === FALSE) { 
    		die(mysqli_error($conn));
		}
		$affected_rows = mysqli_affected_rows($conn);
		mysqli_close ($conn);
		return $affected_rows;
  	}

  	function query($sql) {
	    $conn = $this->connect();
	    $result = mysqli_query($conn, $sql);
	    if($result === FALSE) { 
    		die(mysqli_error($conn));
		}
		else {
			$table_data = array();
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$table_data += $row;
			}
			mysqli_close ($conn);
			return $table_data;
		}
  	}

}

/*$c = new db_connection();

$c->insert_data('user', [
  'name' => 'Simone',
  'surname' => 'Herak',
  'email' => 'herak4@gmail.com',
  'password' => 'sherak',
  'gender' => 'M'
]);

$user_id = 7;
$c->update_data('user', [
  'name' => 'Mario',
  'surname' => 'Marić',
  'email' => 'mario.maric@gmail.com',
  'password' => 'mmaric',
  'gender' => 'M'
], $user_id);*/
