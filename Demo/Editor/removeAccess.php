<?php
include('Resource/header.php');

//1 - Get Accessor Data
$path = $_REQUEST['Path'];
$user = $_REQUEST['User'];
$userData = getUserDataByUsername($user);
$userID = $userData['UserID'];
$complete = FALSE;

//2 - Check for usergroup
$userGroupID;
if(hasUserGroup($path)){
    //Get UserGroupID
    $userGroupID = getUserGroup($path);
} elseif(!hasUserGroup($path)){
    //Go back a page
    header("Location: editAccess.php?Path=$path");
}

//4 - Remove user
if(isset($_POST['delete'])){
    removeUserGroupMember($userGroupID, $userID);
    $complete = TRUE;
} elseif(isset($_POST['cancel'])){
    header("Location: editAccess.php?Path=$path");
}

//3 - Prompt user for confirmation
if($complete == FALSE){
    echo "
        <p>Are you sure you want to remove <b>$user</b> from having access too <b>$path</b> ?</p>
      <form method='POST'>
      <div class='form-group'><button class='btn btn-secondary' name='delete' type='submit'>Remove</button></div>
      <div class='form-group'><button class='btn btn-secondary' name='cancel' type='submit'>Cancel</button></div>
    </form>
      ";
  } elseif($complete == TRUE){
    echo "
      <b>Process Complete</b>
    ";
    header("Location: editAccess.php?Path=$path");
  }



include('Resource/footer.php');
?>