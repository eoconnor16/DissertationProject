<?php
//Display all Domains

echo "
<div>
  <h1>All Domains</h1>
</div><br>";

echo "<div>
   <a style='display:inline' href='login.php'>LOGIN</a>
  </div>";

  echo "<div>
   <a style='display:inline' href='register.php'>REGISTER</a>
  </div>";

include('Resource/function.php');
$domains = returnAllDomains();
//print_r($domains);

for ($i = 0; $i < count($domains); $i++)  {
 $id = $domains[$i][0];
 $name = $domains[$i][1];
 $containerid = $domains[$i][2];

 echo "<div>
   <a style='display:inline' href='view.php?Path=$name'>$name</a>
  </div>";
}

?>