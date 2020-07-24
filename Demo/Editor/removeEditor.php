<?php
$directory = "../";
include('../Resource/header.php');

//1 - Get Editor Data
$path = $_REQUEST['Path'];
$editor = $_REQUEST['Editor'];
$userData = getUserDataByUsername($editor);
$userID = $userData['UserID'];
$complete = FALSE;

//3 - Remove editor
if(isset($_POST['delete'])){
    removeEditor($userID, $path);
    $complete = TRUE;
} elseif(isset($_POST['cancel'])){
    header("Location: viewEditors.php?Path=$path");
}

//2 - Prompt user for confirmation
if($complete == FALSE){
    echo "
        <b>Are you sure you want to delete $editor as an editor of $path ?</b>
      <form method='POST'>
      <div class='form-group'><button class='btn btn-secondary' name='delete' type='submit'>Delete</button></div>
      <div class='form-group'><button class='btn btn-secondary' name='cancel' type='submit'>Cancel</button></div>
    </form>
      ";
  } elseif($complete == TRUE){
    echo "
      <b>Process Complete</b>
    ";
  }




include('../Resource/footer.php');
?>