<?php

$path = $_REQUEST['Path'];
include('Resource/function.php');
$oldPath = getParentPath($path);
      echo "<div>
              <a style='display:inline' href='view.php?Path=$oldPath'>Back</a>
            </div><br>";
$pageid = getPathPageID($path);
echo ConvertPageMarkdown($pageid);

?>