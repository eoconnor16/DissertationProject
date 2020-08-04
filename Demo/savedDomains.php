<?php
include('Resource/header.php');

//Access check

//Get user data
$userid = $_SESSION['userid'];
$savedDomains = getSavedDomains($userid);

//Display all saved domains
echo "<div class='container'>
            <div class='row'>
              <h1>Saved Domains</h1>
            </div>
          ";
if(sizeof($savedDomains) > 0){
    for ($i = 0; $i < count($savedDomains); $i++)  {
        $id = $savedDomains[$i]['DomainID'];
        $name = $savedDomains[$i]['Name'];
        $containerid = $savedDomains[$i]['ContainerID'];
       
        echo "<div>
          <a style='display:inline' href='view.php?Path=$name'>$name</a>
         </div>";
       }
} else {
    echo "You have not saved any domains";
}

include('Resource/footer.php');
?>