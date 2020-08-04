<?php
$title = "Home";
include('Resource/header.php');

//Display all Public Domains
echo "
<div>
  <h1>All Domains</h1>
</div><br>";

//Check if logged in
$domains;

if(isset($_SESSION['userid'])){
  $userid = $_SESSION['userid'];
  $domains = returnAllDomains($userid);
} else {
  $domains = returnAllPublicDomains();
}

//Print all domains
for ($i = 0; $i < count($domains); $i++)  {
 $name = $domains[$i]['Name'];

 echo "<div>
   <a style='display:inline' href='view.php?Path=$name'>$name</a>
  </div>";
}

include('Resource/footer.php');

?>