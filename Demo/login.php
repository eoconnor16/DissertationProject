<?php
    session_start();
    include('Resource/function.php');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>

<body>

<form method='POST'>
    <div class="form-group">
        <label for="InputUsername">Username</label>
        <input name="loginUsername" type="text" class="form-control" id="InputUsername" aria-describedby="emailHelp">
    </div>
    <div class="form-group">
        <label for="InputPassword1">Password</label>
        <input name="loginPassword" type="password" class="form-control" id="InputPassword1">
    </div>
    <button name="loginRequest" type="submit" class="btn btn-primary">Log In</button>
</form>

<?php
    if(isset($_POST['loginRequest'])){
        $username = $_POST['loginUsername'];
        $password = $_POST['loginPassword'];

        //Check if user exists
        $exists = checkLoginDetails($username, $password);

        if ($exists == TRUE) {
            //Get userid
            $userid = getUserIDFromLogin($username, $password);
            
            //Create session variable
            $_SESSION['userid'] = $userid;

            //Check usertype and redirect
            $usertypeid = getUserType($userid);

            if($usertypeid == 1){
                echo "<b>Public user</b>";
            } elseif ($usertypeid == 2){
                echo "<b>Admin</b>";
            }

        } else {
            echo "<p>Please try again<p>";
        }

    }
    
?>
</body>