<?php

function edit_profile($form_edit_profile) {
  $conn = new db_connection();

  $img_uploaded = false;
  if(!empty($_FILES['profile_picture']['name'])) {
    move_uploaded_file($_FILES['profile_picture']['tmp_name'], "img/profile_pictures/" . $_FILES['profile_picture']['name']);
    $img_uploaded = true;
  }
  $name = !empty($_POST['name']) ? $_POST['name'] : '';
  $surname = !empty($_POST['surname']) ? $_POST['surname'] : '';
  $email = !empty($_POST['email']) ? $_POST['email'] : '';
  $gender = !empty($_POST['gender']) ? $_POST['gender'] : '';

  $user = $_SESSION['user'];
  $user_id = $user['user_id'];

  if($img_uploaded) {
    $sql = "SELECT filename FROM images WHERE fk_user_id = " . (int)$user_id . "";
    if(!empty($conn->query($sql))) {
      $filename = $conn->query($sql)[0]['filename'];
      unlink("img/profile_pictures/" . $filename);
      $sql = "DELETE FROM images WHERE fk_user_id = " . (int)$user_id . "";
    }
    $conn->query($sql);
    $sql_data = [
      'filename' => $_FILES['profile_picture']['name'],
      'fk_user_id' => $user_id
    ];
    $conn->insert_data('images', $sql_data);
  }

  $sql_data = [
    'name' => $name,
    'surname' => $surname,
    'email' => $email,
    'gender' => $gender
  ];

  $same_psw = true;
  if(!empty($_POST['password']) || !empty($_POST['password_rpt'])) {
    if($_POST['password'] == $_POST['password_rpt'])
      $sql_data['password'] = sha1($_POST['password']);
    else {
      $form_edit_profile->set_error('password_rpt', 'Password and repeated password are not the same.');
      $same_psw = false;
    }
  }

  $affected_rows = $conn->update_data('user', $sql_data, 'user_id', $user_id);
  if($affected_rows) {
  	$sql = "SELECT * FROM user WHERE user_id = '$user_id'";
  	$row = $conn->query($sql)[0];
  	$_SESSION['user'] = $row;
  }
  else if(!$same_psw) {
  	$form_edit_profile->set_error('edit_profile_btn', 'Update failed.');
  }
  else if(!$img_uploaded) {
    $form_edit_profile->set_error('edit_profile_btn', 'Update failed. You didn\'t change any field.'); 
  }

  if(!$form_edit_profile->check_errors()) {
    $form_edit_profile->set_success_msg('Successfully updated.');
  }
}
