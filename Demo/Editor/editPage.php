<?php
$directory = "../";
include('../Resource/header.php');

//Access checks
$path = $_REQUEST['Path'];

runLoggedInCheck('../index.php');
$userID = $_SESSION['userid'];
if(!hasAccess($userID, $path, accessLevel::editor)){
    header("Location: ../index.php");
}

//PAGE EDIT OPTIONS 
//1 - Get page data (Name & markdown)
$complete = FALSE;
$pageID = getPathPageID($path);
$pageData = returnPageData($pageID);
$name = $pageData[0];
$markdown = $pageData[1];
$markdownError = "";
$nameError = "";

//3 - Once form is submitted make changes and relocate
if(isset($_POST['request'])){
  $check = TRUE;
  $nMarkdown = $_POST['newMarkdown'];
  $nName = $_POST['newName'];
  
  //Check all data has been entered
  if(!$nMarkdown){
    $check = FALSE;
    $markdownError = "Please enter markdown";
  }

  if(!$nName){
    $check = FALSE;
    $nameError = "Please enter a valid name";
  }

  //Check if the user has included any urls
  $url1 = "page.php?Path=";
  $url2 = "http://eoconnor16.lampt.eeecs.qub.ac.uk/Demo/view.php?Path=";
  $nMarkdown = str_replace("#PAGE#",$url1,$nMarkdown);
  $nMarkdown = str_replace("#CONTAINER#",$url2,$nMarkdown);

  //If passed the checks add save changes to db
  if($check == TRUE){
      changePageName($path, $nName);
      changePageMarkdown($path, $nMarkdown);
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
    </div>";

    if($markdownError != ""){
      echo "
      <div class='alert alert-danger' role='alert'>
        $markdownError
      </div>
      ";
    }

  echo  "<div class='form-group'>
      <label for='exampleFormControlTextarea1'>Markdown</label>
      <textarea class='form-control' id='exampleFormControlTextarea1' name='newMarkdown' rows='7' >$markdown</textarea>
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