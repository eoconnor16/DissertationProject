<?php
$directory = "../";
include('../Resource/header.php');

//Get path and check current privacy state
$path = $_REQUEST['Path'];

/* Check if a path has a linked usergroup, if not make one
*/
$userGroupID;
if(hasUserGroup($path)){
    //Get UserGroupID
    $userGroupID = getUserGroup($path);
} elseif(!hasUserGroup($path)){
    //Make UserGroup and refresh page
    makeUserGroup($path);
    header("Location: tester.php");
}

/* Display all users who currently have access with a remove option
*/
$users = getUserGroupUsers($userGroupID);
for ($i = 0; $i < sizeof($users); $i++){
    $username = $users[$i]['Username'];
    echo "<div>
    <p>$username</p>
    <a href='removeAccess??Path=$path&User=$username'><button type='button' class='btn btn-danger'>Remove</button></a>
    </div>";
}

/* Add users to that usergroup based on username on a form
*/
echo "<div>
<a href=''><button type='button' class='btn btn-success'>Add User</button></a>
</div>";

include('../Resource/footer.php');
?>