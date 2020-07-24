<?php
$directory = "../";
include('../Resource/header.php');

//PAGE DELETE 
//1 - Get container data
$complete = FALSE;
$path = $_REQUEST['Path'];
$containerID = getPathContainerID($path);
$contianerData = getContainerData($containerID);
$name = $contianerData[2];

//3 - Delete page
if(isset($_POST['delete'])){
    deleteContainer($path);
    $complete = TRUE;
} elseif(isset($_POST['cancel'])){
    header("Location: editContent.php?Path=$path");
}

//2 - Prompt user for conformation
if($complete == FALSE){
    echo "
        <b>Are you sure you want to delete $name from $path ?</b>
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