<?php

include('Resource/header.php');

$path = $_REQUEST['Path'];
$oldPath = getParentPath($path);
      echo "<div>
              <a style='display:inline' href='view.php?Path=$oldPath'>Back</a>
            </div><br>";
$pageid = getPathPageID($path);
echo ConvertPageMarkdown($pageid);

include('Resource/footer.php');

?>