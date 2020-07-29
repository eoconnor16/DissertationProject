<?php
$directory = "../";
include('../Resource/header.php');

//Access check
$path = $_REQUEST['Path'];
runLoggedInCheck('../index.php');
$userID = $_SESSION['userid'];
if(!hasAccess($userID, $path, accessLevel::admin)){
    header("Location: ../index.php");
}

//1 - Get Editor Data

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
        <b>Are you sure you want to delete $editor as an editor of $path?</b>
      <form method='POST'>
      <div class='form-group'><button class='btn btn-secondary' name='delete' type='submit'>Delete</button></div>
      <div class='form-group'><button class='btn btn-secondary' name='cancel' type='submit'>Cancel</button></div>
    </form>
      ";
} elseif($complete == TRUE){
    echo "
      <b>Editor removed</b>
      <div><a href='viewEditors.php?Path=$path'><button type='button' class='btn btn-outline-success'>Back</button></a><br></div>
    ";
}


include('../Resource/footer.php');
?>