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

//Validate form
$complete = FALSE;
$nameError = "";
$userError = "";
$adminError = "";

if(isset($_POST['cancel'])){
    header("Location: viewEditors.php?Path=$path");
}

if(isset($_POST['request'])){
    $check = TRUE;
    $name = $_POST['username'];

    //Check if a name exists
    if(!$name || !checkForUsername($name)){
        $check = FALSE;
        $nameError = "Error: No such username '$name'";
    }

    //Check if user is already an editor
    if(isEditor($name, $path)){
        $check = FALSE;
        $userError = "Error: '$name' is already assigned as an editor";
    }

    //Check if user is an admin
    if(isAdmin($name, $path)){
        $check = FALSE;
        $adminError = "Error: '$name' is already assigned as an admin";
    }

    //If all correct add new record
    if($check == TRUE){
        addEditor($name, $path);
        $complete = TRUE;
    }
}

//Print form
if($complete == FALSE){
    echo "
    <form method='POST'>
        <div class='col'>";

        if($nameError != ""){
          echo "
          <div class='alert alert-danger' role='alert'>
            $nameError
          </div>
          ";
        }

        if($userError != ""){
            echo "
            <div class='alert alert-danger' role='alert'>
              $userError
            </div>
            ";
        }

        if($adminError != ""){
            echo "
            <div class='alert alert-danger' role='alert'>
              $adminError
            </div>
            ";
        }
      
      echo
            "<div class='form-group'><!-- Username -->
                <label for='username'>Username :</label>
                <input name='username' type='text' class='form-control' id='username' placeholder=''>
            </div>
        </div>
        <div class='form-group'>
            <button class='btn btn-secondary' name='request' type='submit'>Add</button>
            <button class='btn btn-secondary' name='cancel' type='submit'>Cancel</button>
        </div>
    </form> 
";
} elseif($complete == TRUE){
    echo "<div>
      <p><b>$name</b> has been added as an editor to <b>$path</b></p> 
      </div>
      <div><a href='viewEditors.php?Path=$path'><button type='button' class='btn btn-outline-success'>Back</button></a><br></div>
    ";
}

include('../Resource/footer.php');
?>