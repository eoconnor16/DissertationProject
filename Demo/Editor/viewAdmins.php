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

//Get all admins of this domain, print list and allow remove option

$admins = getAdmins($path);

if(sizeof($admins) == 0){
    echo "No Admins For $path";
} elseif(sizeof($adminss) > 0){
    echo "<div><h2>Editors For $path</h2></div>
        <div>";
    foreach ($admins as &$admin){
        $data = getUserData($admin);
        $username = $data['Username'];
        echo "<div>
        <p>$username</p>
        <a href='removeAdmin.php?Path=$path&Editor=$username'><button type='button' class='btn btn-danger'>Remove</button></a>
        </div>";
    }
    echo "</div>";
}

//Option to assign new editor
echo "<div>
<a href='addAdmin.php?Path=$path'><button type='button' class='btn btn-success'>Assign Editor</button></a>
</div>";


include('../Resource/footer.php');
?>