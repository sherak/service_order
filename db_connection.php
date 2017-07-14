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

	function insert_data($table, $columns, $data) {
	    $columns = implode(',', $columns);
	    $data = implode("','", $data);
	    $sql = "INSERT INTO " . $table . " (" . $columns . ") VALUES ('" . $data . "')";
	    $conn = $this->connect();
	    $result = mysqli_query($conn, $sql);
	    if($result === FALSE) { 
    		die(mysqli_error($conn));
		}
		mysqli_close ($conn);
  	}

  	function update_data($table, $columns, $values, $column_id) {
  		$column_value = '';
  		foreach(array_combine($columns, $values) as $column => $value) {
  			$column_value .= $column . "=" . "'" . $value;
  			if($column === end($columns))
       			$column_value .= "'";
       		else
       			$column_value .= "',";
       	}
       	// TODO: change code structure
	    $sql = "UPDATE" . " " . $table . " SET " . $column_value . " WHERE " . $table . "." . $table . "_id=" . $column_id;
	    $conn = $this->connect();
	    $result = mysqli_query($conn, $sql);
	    if($result === FALSE) { 
    		die(mysqli_error($conn));
		}
		mysqli_close ($conn);
  	}

  	function select_data($table, $columns, $column_id) {
	    $columns = implode(',', $columns);
	    $sql = "SELECT " . $columns . " FROM " . $table . " WHERE " . $table . "." . $table . "_id=" . $column_id;
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

$c = new db_connection();
$columns = array("name", "surname", "email", "password", "gender");

$data = array("Simone", "Herak", "herak4@gmail.com", "sherak", "M");
$c->insert_data('user', $columns, $data);

$value = array("Mario", "Marić", "mario.maric@gmail.com", "mmaric", "M");
$user_id = 7;
$c->update_data('user', $columns, $value, $user_id);

$table_data = $c->select_data('user', $columns, $user_id);
foreach ($table_data as $key => $val) {
	echo $key . " = " . $val ."<br>";
}