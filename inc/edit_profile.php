<?php

function edit_profile($form_edit_profile) {
  $conn = new db_connection();

  $name = !empty($_POST['name']) ? $_POST['name'] : '';
  $surname = !empty($_POST['name']) ? $_POST['name'] : '';
  $email = !empty($_POST['email']) ? $_POST['email'] : '';
  $gender = !empty($_POST['gender']) ? $_POST['gender'] : '';

  $user = $_SESSION['user'];
  $user_id = $user['user_id'];

  $sql_data = [
    'name' => $name,
    'surname' => $surname,
    'email' => $email,
    'gender' => $gender
  ];

  if(!empty($_POST['password']) || !empty($_POST['password_rpt'])) {
    if($_POST['password'] == $_POST['password_rpt'])
      $sql_data['password'] = sha1($_POST['password']);
    else
      $form_edit_profile->set_error('edit_profile_btn', 'Password and repeated password are not the same.');
  }

  $affected_rows = $conn->update_data('user', $sql_data, $user_id);

  $data = array("name" => $name, "surname" => $surname, "email" => $email, "password" => $password, "gender" => $gender);
  $conn->insert_data('user', $data);

  if($affected_rows == 1) {
  	$sql = "SELECT * FROM user WHERE user_id = '$user_id'";
  	$row = $conn->query($sql)[0];
  	$_SESSION['user'] = $row;
  }
  else {
  	$form_edit_profile->set_error('edit_profile_btn', 'Update failed.');
  }

  if(!$form_edit_profile->check_errors()) {
    $form_edit_profile->set_success_msg('Successfully updated.');
    header("Location: my_account.php");
  }
}
