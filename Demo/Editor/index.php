<?php
$directory = "../";
include('../Resource/header.php');

//Security check
runLoggedInCheck('../index.php');

//We want to get a list of all the domains that a user is associated with (Admin/Editor)
$userID = $_SESSION['userid'];
$domains = getAssocDomains($userID);

//If the user has no domains we want to display a message stating so
if(empty($domains)){

} else {
//If the user has domains we want to display them as a list 
    for ($i = 0; $i < count($domains); $i++)  {
        $domainID = $domains[$i][0];
        $roleID = $domains[$i][1];
        $roleName = $domains[$i][2];
        $name = getDomainNameFromID($domainID);
   
        echo "<div>
            <a style='display:inline' href='domainSettings.php?Path=$name'>$name</a>
        </div>";
   }
}


include('../Resource/footer.php');
?>