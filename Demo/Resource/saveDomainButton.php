<?php
//Print Form
if(isLoggedIn()){
  $userID = $_SESSION['userid'];

  //Check form 
  if(isset($_POST['unsave'])){
    //UNSAVE DOMAIN
    removeSavedDomain($userID, $path);
    header("Refresh:0");
  } elseif(isset($_POST['save'])){
    //SAVE DOMAIN
    saveDomain($userID, $path);
    header("Refresh:0");
  }

  //Print form
  if(isSystemAdmin($userID) == FALSE){
    //Check if domain is already saved
    if(isDomainSaved($userID, $path)){
      //DOMAIN SAVED
      echo "
      <form method='POST'>
        <div class='form-group'><button class='btn btn-warning' name='unsave' type='submit'>Unsave Domain</button></div>
      </form>";
      } else {
      //DOMAIN NOT SAVED
      echo "
      <form method='POST'>
        <div class='form-group'><button class='btn btn-success' name='save' type='submit'>Save Domain</button></div>
      </form>";
      }
    } 
} 
?>