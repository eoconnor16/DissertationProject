<?php
$directory = "../";
include('../Resource/header.php');

//Access checks
$path = $_REQUEST['Path'];

runLoggedInCheck('../index.php');
$userID = $_SESSION['userid'];
if(!hasAccess($userID, $path, 1)){
    header("Location: ../index.php");
}

//PAGE EDIT OPTIONS 
//1 - Get page data (Name & markdown)
$complete = FALSE;
$path = $_REQUEST['Path'];
$containerID = getPathContainerID($path);
$containerData = getContainerData($containerID);
$name = $containerData[2];
$nameError = "";

//3 - Once form is submitted make changes and relocate
if(isset($_POST['request'])){
  $check = TRUE;
  $nName = $_POST['newName'];
  
  //Check all data has been entered
  if(!$nName){
    $check = FALSE;
    $nameError = "Please enter a valid name";
  }

  //If passed the checks add save changes to db
  if($check == TRUE){
    changeContainerName($path, $nName);
    $complete = TRUE;
  }
}

//2 - Generate form
if($complete == FALSE){
  echo "
    <form method='POST'>";

    if($nameError != ""){
      echo "
      <div class='alert alert-danger' role='alert'>
        $nameError
      </div>
      ";
    }

  echo  "<div class='form-group'>
      <label for='exampleFormControlInput1'>Name</label>
      <input type='text' class='form-control' name='newName' id='exampleFormControlInput1' value='$name'>
    </div>
    <button class='btn btn-secondary' name='request' type='submit'>Save Changes</button>
  </form>
    ";
} elseif($complete == TRUE){
  echo "
    <b>Process Complete</b>
  ";
}

include('../Resource/footer.php');

?>