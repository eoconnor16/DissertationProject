<?php
include('Resource/header.php');

//Get all admins of this domain, print list and allow remove option
$path = "Sports";//$_REQUEST['Path'];
$admins = getAdmins($path);

if(sizeof($admins) == 0){
    echo "No Admins For $path";
} elseif(sizeof($admins) > 0){
    echo "<div><h2>Admins For $path</h2></div>
        <div>";
    foreach ($admins as &$admin){
        $data = getUserData($admin);
        $username = $data['Username'];
        echo "<div>
        <p>$username</p>
        <a href=''><button type='button' class='btn btn-danger'>Remove</button></a>
        </div>";
    }
    echo "</div>";
}

//Option to assign new editor
echo "<div>
<a href=''><button type='button' class='btn btn-success'>Assign Admin</button></a>
</div>";

include('Resource/footer.php');
?>