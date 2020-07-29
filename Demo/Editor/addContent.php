<?php
$directory = "../";
include('../Resource/header.php');

/*Make it so that 
    -An element of the domain should not share a name with another element in that domain level
    
*/

//Access checks
$path = $_REQUEST['Path'];

runLoggedInCheck('../index.php');
$userID = $_SESSION['userid'];
if(!hasAccess($userID, $path, accessLevel::editor)){
    header("Location: ../index.php");
}

//Check form content
$locationError = "";
$typeError = "";
$nameError = "";
if(isset($_POST['request'])){
  $check = TRUE;
  $location = $_POST['location'];
  $type = $_POST['itemType'];
  $name = $_POST['itemName'];

  //Check all data has been entered
  if(!$location){
    $check = FALSE;
    $locationError = "Please select a valid path";
  }

  if(!$type){
    $check = FALSE;
    $typeError = "Please select a valid item type to add";
  }

  if(!$name){
    $check = FALSE;
    $nameError = "Please enter a valid name for your insert";
  }

  //If all correct add new record
  if($check == TRUE){
    if($type == "Folder"){
      //Add container
      addContainer($name, $location);
      //relocate
    } elseif($type == "Page"){
      //Add page
      addPage($name, $location);
      //relocate
    }
  }
}

//Generate form data
$paths = getAllDomainContainerPaths($path);

echo "<form method='POST'>
  <div class='form-group'>
    <label for='location'>Select Path</label>
    <select class='form-control' name='location' placeholder='$path'>";

    foreach($paths as $item){
        echo "<option>$item</option>";
    }
    
echo "
    </select>
  </div>

  <div class='form-group'>
    <label for='itemType'>Add</label>
    <select class='form-control' name='itemType'>
      <option>Folder</option>
      <option>Page</option>
    </select>
  </div>

  <div class='form-group'>
    <label for='itemName'>Name</label>";

  if($nameError != ""){
    echo "
    <div class='alert alert-danger' role='alert'>
      $nameError
    </div>
    ";
  }

echo   "<input type='text' class='form-control' name='itemName' placeholder=''>
  </div>
  <div class='form-group'>
    <button class='btn btn-secondary' name='request' type='submit'>Create</button>
  </div>  
</form>";

include('../Resource/footer.php');
?>