<?php

function edit_profile($form_edit_profile) {
  $conn = new db_connection();

  $name = $form_edit_profile->get_value('name');
  $surname = $form_edit_profile->get_value('surname');
  $email = $form_edit_profile->get_value('email');
  $gender = $form_edit_profile->get_value('gender');

  $sql_data = [
    'name' => $name,
    'surname' => $surname,
    'email' => $email,
    'gender' => $gender
  ];

  if(!empty($_FILES['profile_picture']['name'])) {
    move_uploaded_file($_FILES['profile_picture']['tmp_name'], "img/profile_pictures/" . $_FILES['profile_picture']['name']);

    $sql = "SELECT filename FROM images WHERE fk_user_id = " . (int)$user_id;
    $res = $conn->query($sql);
    if(!empty($res)) {
      $filename = $res[0]['filename'];
      unlink("img/profile_pictures/" . $filename);
      $sql = "DELETE FROM images WHERE fk_user_id = " . (int)$user_id;
    }

    $conn->insert_data('images', [
      'filename' => $_FILES['profile_picture']['name'],
      'fk_user_id' => $user_id
    ]);
  }

  $user = $_SESSION['user'];
  $user_id = $user['user_id'];

  if(!empty($_POST['password']) || !empty($_POST['password_rpt'])) {
    if($_POST['password'] == $_POST['password_rpt'])
      $sql_data['password'] = sha1($_POST['password']);
    else {
      $form_edit_profile->set_error('password_rpt', 'Passwords are not same.');
    }
  }

  if($form_edit_profile->check_errors()) {
    $form_edit_profile->set_error('edit_profile_btn', 'Update failed.');
  } else {
    $affected_rows = $conn->update_data('user', $sql_data, 'user_id', $user_id);
    if($affected_rows) {
      $sql = "SELECT * FROM user WHERE user_id = '$user_id'";
      $_SESSION['user'] = $conn->query($sql)[0];
    }

    $form_edit_profile->set_success_msg('Successfully updated.');
    header("Location: my_account.php#edit_profile");
  }
}
