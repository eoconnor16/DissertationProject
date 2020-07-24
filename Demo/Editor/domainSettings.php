<?php
$directory = "../";
include('../Resource/header.php');

//Access check
runLoggedInCheck('../index.php');

//Get user and domain information
$userID = $_SESSION['userid'];
$path = $_REQUEST['Path'];
$domainID = getPathDomainID($path);

//Check users relation to domain and display relevent settings
if(isDomainEditor($domainID, $userID) == TRUE || isDomainAdmin($domainID, $userID) == TRUE || isSystemAdmin($userID) == TRUE){
    //Acess to domain editor settings
    echo "<div><div><a href='addContent.php?Path=$path'><button type='button' class='btn btn-outline-success'>Add Content</button></a><br></div></div>
    <div><a href='editContent.php?Path=$path'><button type='button' class='btn btn-outline-success'>Edit Content</button></a><br></div>
    ";
}
if(isDomainAdmin($domainID, $userID) == TRUE || isSystemAdmin($userID) == TRUE){
    //Access to domain admin settings
    //echo "ADMIN ACCESS<br>";
    echo "
    <div><a href='viewEditors.php?Path=$path'><button type='button' class='btn btn-outline-success'>Assign/Remove Editor</button></a><br></div>
    <div><a href=''><button type='button' class='btn btn-outline-success'>Privacy Settings</button></a><br></div>
    <div><a href=''><button type='button' class='btn btn-outline-success'>Delete Domain</button></a><br></div>
    ";
} 
if(isSystemAdmin($userID) == TRUE){
    //Access to all domain settings
    //echo "SYS ADMIN ACCESS";
    echo "
    <div><a href=''><button type='button' class='btn btn-outline-success'>Assign/Remove Admin</button></a><br></div>
    ";
} 

include('../Resource/footer.php');
?>