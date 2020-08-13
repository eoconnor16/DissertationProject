<?php
include('Resource/header.php');

$name = "Sports & Other things";//$domains[$i]['Name'];
echo urlencode($name);

echo "<div>
  <a style='display:inline' href='view.php?Path=". urlencode($name) ."'>$name</a>
 </div>";

include('Resource/footer.php');
?>