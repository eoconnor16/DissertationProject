<?php
    include('Resource/function.php');

    if(isset($_POST['regRequest'])){
        $string = "";
        $vaild = TRUE;
        $email = $_POST['email']; 
        $username = $_POST['username'];
        $pass1 = $_POST['pass1'];
        $pass2 = $_POST['pass2'];
        $fname = $_POST['firstname']; 
        $lname = $_POST['lastname'];
        
        //Check all data is entered
        if(!$email || !$username || !$pass1 || !$pass2 || !$fname || !$lname){
            //DATA MISSED
            $vaild = FALSE;
        } else {
            //DATA FILLED CORRECTLY
            if(validatePasswords($pass1, $pass2) == FALSE){
            //PASSWORDS DONT MATCH
            $vaild = FALSE;
            $string .= "Passwords invalid, ";
            } 
    
            if(checkForUsername($username) == TRUE){
                //USERNAME NOT FREE
                $vaild = FALSE;
            } 
    
            if(checkForEmail($email) == TRUE){
                //EMAIL NOT FREE
                $vaild = FALSE;
            }
        }
    
        if($vaild == TRUE){
            //Create user
            createNewPublicUser($email, $username, $pass1, $fname, $lname);
            echo "<p>User added<p>";
            exit();
        } else {
            //Reload page
            //echo "<p>Please try again<p>";
        }   
    }

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <!--
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    -->
</head>

<body>
    <?php
        if(isset($string)){
            echo "<b>Error message: $string<b>";
        }
    ?>

    <form method="POST">
          <div class="col">
            <div class="form-group"><!-- Email -->
                  <label for="email">Email :</label>
                  <input name="email" type="email" class="form-control" id="email" placeholder="">
              </div>
          </div>
          <div class="col">
            <div class="form-group"><!-- Username -->
                  <label for="username">Username :</label>
                  <input name="username" type="text" class="form-control" id="username" placeholder="">
              </div>
          </div>
          <div class="col">
            <div class="form-group"><!-- Password -->
                  <label for="pass1">Password :</label>
                  <input name="pass1" type="password" class="form-control" id="pass1" placeholder="">
              </div>
          </div>
          <div class="col">
            <div class="form-group"><!-- Re-Password -->
                  <label for="pass2">Re-Enter Password :</label>
                  <input name="pass2" type="password" class="form-control" id="pass2" placeholder="">
              </div>
          </div>
      </div>
          <div class="row">
            <div class="col">
              <div class="form-group"><!-- First Name -->
                    <label for="firstname">First Name :</label>
                    <input name="firstname" type="text" class="form-control" id="firstname" placeholder="">
                </div>
            </div>
            <div class="col">
              <div class="form-group"><!-- Last Name -->
                    <label for="lastname">Last Name :</label>
                    <input name="lastname" type="text" class="form-control" id="lastname" placeholder="">
                </div>
            </div>
          </div>
          <div class="row">
            <div class="form-group"> <!-- Submit button -->
                <button class="btn btn-secondary" name="regRequest" type="submit">Register</button>
            </div>
          </div>  
    </form>    
</body>
<?php



?>
