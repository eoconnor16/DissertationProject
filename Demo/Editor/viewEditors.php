<?php
$directory = "../";
include('../Resource/header.php');

//Get all editors of this domain, print list and allow remove option
$path = $_REQUEST['Path'];
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
<a href=''><button type='button' class='btn btn-success'>Assign Editor</button></a>
</div>";


include('../Resource/footer.php');
?>