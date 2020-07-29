<?php
$directory = "../";
include('../Resource/header.php');

//Access check
$path = $_REQUEST['Path'];
runLoggedInCheck('../index.php');
$userID = $_SESSION['userid'];
if(!hasAccess($userID, $path, accessLevel::systemAdmin)){
    header("Location: ../index.php");
}


//Validate form
$complete = FALSE;
$nameError = "";
$userError = "";
$editorError = "";

if(isset($_POST['cancel'])){
    header("Location: viewAdmins.php?Path=$path");
}

if(isset($_POST['request'])){
    $check = TRUE;
    $name = $_POST['username'];

    //Check if a name exists
    if(!$name || !checkForUsername($name)){
        $check = FALSE;
        $nameError = "Error: No such username '$name'";
    }

    //Check if user is already an admin
    if(isAdmin($name, $path)){
        $check = FALSE;
        $userError = "Error: '$name' is already assigned as an admin";
    }

    //Check if user is an editor
    if(isEditor($name, $path)){
        $check = FALSE;
        $editorError = "Error: '$name' is already assigned as an editor";
    }

    //If all correct add new record
    if($check == TRUE){
        addAdmin($name, $path);
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

        if($editorError != ""){
            echo "
            <div class='alert alert-danger' role='alert'>
              $editorError
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
      <p><b>$name</b> has been added as an admin to <b>$path</b></p> 
      </div>
      <div><a href='viewAdmins.php?Path=$path'><button type='button' class='btn btn-outline-success'>Back</button></a><br></div>
    ";
}

include('../Resource/footer.php');
?>