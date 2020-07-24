<?php
$directory = "../";
include('../Resource/header.php');

//Get path and check current privacy state
$path = $_REQUEST['Path'];

//If Public - prompt user if they want to change to private
if(isPublic($path)){
    //Review form 
    if(isset($_POST['delete'])){
        //SET TO PRIVATE
        changeToPrivate($path);
        header("Location: privacySettings.php?Path=$path");
    } elseif(isset($_POST['cancel'])){
        //RETURN
        header("Location: domainSettings.php?Path=$path");
    }

    //Print page content
    echo "<div><p>The domain $path is currently set to <b>public</b></p></div>
    <form method='POST'>
      <div class='form-group'><button class='btn btn-secondary' name='delete' type='submit'>Set to private</button></div>
      <div class='form-group'><button class='btn btn-secondary' name='cancel' type='submit'>Cancel</button></div>
    </form>";
}

//If Private - prompt user if they want to change to public
//           - allow user to edit user group of who can view domain
if(isPrivate($path)){
    //Review form 
    if(isset($_POST['delete'])){
        //SET TO PUBLIC
        changeToPublic($path);
        header("Location: privacySettings.php?Path=$path");
    } elseif(isset($_POST['access'])){
        //SET ACCESS
        header("Location: editAccess.php?Path=$path");
    } elseif(isset($_POST['cancel'])){
        //RETURN
        header("Location: domainSettings.php?Path=$path");
    }

    //Print page content
    echo "<div><p>The domain $path is currently set to <b>private</b></p></div>
    <form method='POST'>
      <div class='form-group'><button class='btn btn-secondary' name='delete' type='submit'>Set to public</button></div>
      <div class='form-group'><button class='btn btn-secondary' name='access' type='submit'>Edit Access</button></div>
      <div class='form-group'><button class='btn btn-secondary' name='cancel' type='submit'>Cancel</button></div>
    </form>";
}

include('../Resource/footer.php');
?>