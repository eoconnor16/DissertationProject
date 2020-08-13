<?php
    include('Resource/header.php');
    $passedPath = $_REQUEST['Path'];
    
    //Check if passed path is valid
    $containerid = getPathContainerID($passedPath);
    $path = getPath($containerid);
    if(isset($path)){
      echo "<div class='container'>
            <div class='row'>
              <h2>Path: $path</h2>
            </div>
          ";
    } else {
      //Redirect to error page
      header("Location: Error/404.php");
    }
    
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
                <a style='display:inline' href='view.php?Path=". urlencode($oldPath) ."'>Back</a>
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
          <p style='display:inline'>Container: </p><a style='display:inline' href='view.php?Path=". urlencode($newPath) ."'>$name</a>
         </div>";
    }

    for ($i = 0; $i < count($pages); $i++)  {
        $pagename = $pages[$i][1];
        $newPath = "$path/$pagename";
       
        echo "<div>
          <p style='display:inline'>Page: </p><a style='display:inline' href='page.php?Path=". urlencode($newPath) ."''>$pagename</a>
         </div>";
    }

    include('Resource/footer.php');

?>