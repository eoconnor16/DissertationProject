<?php
include('Resource/header.php');

//Security check
runLoggedInCheck();

//Check form content
if(isset($_POST['request'])){
    $domainName = $_POST['domainName'];
    echo "<b>$domainName</b>";

    //Validate daat
    
    if(checkDomainName($domainName) == TRUE){
        echo "<b>DOMAIN TAKEN</b>";
    }
}

//Web page content
echo "
<form method='POST'>
<div class='form-group'>
  <label for='exampleFormControlInput1'>Domain Name:</label>
  <input name='domainName' type='text' class='form-control' id='exampleFormControlInput1' placeholder=''>
</div>
<div class='form-group'>
    <button class='btn btn-secondary' name='request' type='submit'>Create</button>
</div>  
</form>

";

include('Resource/footer.php');
?>