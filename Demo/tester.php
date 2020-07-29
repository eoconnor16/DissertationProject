<?php
include('Resource/header.php');

//Set Access

//Get data
$path = "Sports";//$_REQUEST['Path'];

//Validate form
$complete = FALSE;
$nameError = "";

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
    ";
}

include('Resource/footer.php');
?>