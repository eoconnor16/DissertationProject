<?php
$directory = "../";
include('../Resource/header.php');

//1 - Get admin data
$path = $_REQUEST['Path'];
$admin = $_REQUEST['Admin'];
$userData = getUserDataByUsername($admin);
$userID = $userData['UserID'];
$complete = FALSE;

//3 - Remove admin
if(isset($_POST['delete'])){
    removeAdmin($userID, $path);
    $complete = TRUE;
} elseif(isset($_POST['cancel'])){
    header("Location: viewAdmins.php?Path=$path");
}

//2 - Prompt user for confirmation
if($complete == FALSE){
    echo "
        <b>Are you sure you want to delete $admin as an admin of $path?</b>
      <form method='POST'>
      <div class='form-group'><button class='btn btn-secondary' name='delete' type='submit'>Delete</button></div>
      <div class='form-group'><button class='btn btn-secondary' name='cancel' type='submit'>Cancel</button></div>
    </form>
      ";
  } elseif($complete == TRUE){
    echo "
      <b>Process Complete</b>
      <div><a href='viewAdmins.php?Path=$path'><button type='button' class='btn btn-outline-success'>Back</button></a><br></div>
    ";
  }


include('../Resource/footer.php');
?>