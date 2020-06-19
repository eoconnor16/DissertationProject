<?php
    include('Resource/function.php');
    $path = $_REQUEST['Path'];
    $containerid = getPathContainerID($path);
    
    echo "<div>
            <h2>Path: $path</h2>
          </div><br>";

    //Return Link
    if (getPathLength($path) <= 1){
      echo "<div>
              <a style='display:inline' href='index.php'>Back</a>
            </div>";
    } else {
      $oldPath = getParentPath($path);
      echo "<div>
              <a style='display:inline' href='view.php?Path=$oldPath'>Back</a>
            </div>";
    }

    
    $containers = returnContainers($containerid);
    $pages = returnPages($containerid);

    for ($i = 0; $i < count($containers); $i++)  {
        $id = $containers[$i][0];
        $parentid = $containers[$i][1];
        $name = $containers[$i][2];
        $newPath = "$path/$name";

        echo "<div>
          <p style='display:inline'>Container: </p><a style='display:inline' href='view.php?Path=$newPath'>$name</a>
         </div>";
    }

    for ($i = 0; $i < count($pages); $i++)  {
        $id = $pages[$i][0];
        $pagename = $pages[$i][1];
        $newPath = "$path/$pagename";
       
        echo "<div>
          <p style='display:inline'>Page: </p><a style='display:inline' href='page.php?Path=$newPath'>$name</a>
         </div>";
    }

?>