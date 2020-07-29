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

//1 - Get variables
$complete = FALSE;

//3 - Delete domain
if(isset($_POST['delete'])){
    deleteDomain($path);
    $complete = TRUE;
} elseif(isset($_POST['cancel'])){
    header("Location: domainSettings.php?Path=$path");
}

//2 - Prompt user for confirmation
if($complete == FALSE){
    echo "
        <b>Are you sure you want to delete the domain $path? This cannot be undone</b>
      <form method='POST'>
      <div class='form-group'><button class='btn btn-secondary' name='delete' type='submit'>Delete</button></div>
      <div class='form-group'><button class='btn btn-secondary' name='cancel' type='submit'>Cancel</button></div>
    </form>
      ";
} elseif($complete == TRUE){
    echo "
      <b>Domain has been deleted</b>
      <div><a href='index.php'><button type='button' class='btn btn-outline-success'>Back</button></a><br></div>
    ";
}


include('../Resource/footer.php');
?>