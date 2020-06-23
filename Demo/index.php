<?php
include('Resource/header.php');

//Display all Domains
echo "
<div>
  <h1>All Domains</h1>
</div><br>";

$domains = returnAllDomains();

for ($i = 0; $i < count($domains); $i++)  {
 $id = $domains[$i][0];
 $name = $domains[$i][1];
 $containerid = $domains[$i][2];

 echo "<div>
   <a style='display:inline' href='view.php?Path=$name'>$name</a>
  </div>";
}

include('Resource/footer.php');

?>