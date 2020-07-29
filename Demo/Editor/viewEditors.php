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

//Get all editors of this domain, print list and allow remove option

$editors = getEditors($path);

if(sizeof($editors) == 0){
    echo "No Editors For $path";
} elseif(sizeof($editors) > 0){
    echo "<div><h2>Editors For $path</h2></div>
        <div>";
    foreach ($editors as &$editor){
        $data = getUserData($editor);
        $username = $data['Username'];
        echo "<div>
        <p>$username</p>
        <a href='removeEditor.php?Path=$path&Editor=$username'><button type='button' class='btn btn-danger'>Remove</button></a>
        </div>";
    }
    echo "</div>";
}

//Option to assign new editor
echo "<div>
<a href='addEditor.php?Path=$path'><button type='button' class='btn btn-success'>Add Editor</button></a>
</div>";


include('../Resource/footer.php');
?>