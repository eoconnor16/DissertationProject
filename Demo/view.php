<?php
    include('Resource/header.php');
    $path = $_REQUEST['Path'];
    $containerid = getPathContainerID($path);
    
    echo "<div class='container'>
            <div class='row'>
              <h2>Path: $path</h2>
            </div>
          ";

    //Return Link
    if (getPathLength($path) <= 1){
      echo "<div class='row'>
              <div class='col'>
                <a style='display:inline' href='index.php'>Back</a>
              </div>";
    } else {
      $oldPath = getParentPath($path);
      echo "<div class='row'>
              <div class='col'>
                <a style='display:inline' href='view.php?Path=$oldPath'>Back</a>
              </div>";
    }

    //Include save domain button
    echo "<div class='col'>";
    include('Resource/saveDomainButton.php');
    echo "</div>
        </div>
        </div><br>";

    $containers = returnContainers($containerid);
    $pages = returnPages($containerid);

    for ($i = 0; $i < count($containers); $i++)  {
        $name = $containers[$i][2];
        $newPath = "$path/$name";

        echo "<div>
          <p style='display:inline'>Container: </p><a style='display:inline' href='view.php?Path=$newPath'>$name</a>
         </div>";
    }

    for ($i = 0; $i < count($pages); $i++)  {
        $pagename = $pages[$i][1];
        $newPath = "$path/$pagename";
       
        echo "<div>
          <p style='display:inline'>Page: </p><a style='display:inline' href='page.php?Path=$newPath'>$pagename</a>
         </div>";
    }

    include('Resource/footer.php');

?>