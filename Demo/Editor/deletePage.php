<?php
$directory = "../";
include('../Resource/header.php');

//Access check
$path = $_REQUEST['Path'];
runLoggedInCheck('../index.php');
$userID = $_SESSION['userid'];
if(!hasAccess($userID, $path, accessLevel::editor)){
    header("Location: ../index.php");
}

//PAGE DELETE 
//1 - Get page data
$complete = FALSE;

$pageID = getPathPageID($path);
$pageData = returnPageData($pageID);
$name = $pageData[0];

//3 - Delete page
if(isset($_POST['delete'])){
    deletePage($path);
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