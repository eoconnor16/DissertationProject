<?php
include('Resource/header.php');

// !!! - Change so that a domain is default to public

//Security check
runLoggedInCheck("index.php");

//Check form content
$string = "";
if(isset($_POST['request'])){
    $domainName = $_POST['domainName'];
    echo "<p>$domainName : </p>";

    //Validate data
    if(!$domainName){
        $string .= "Missed input, "; 
    } else {
        if(isDomainNameTaken($domainName) == TRUE){
            echo "<b>DOMAIN TAKEN</b><br>";
        } else {
            echo "<b>DOMAIN FREE</b><br>";
            if  (!$settings["createDomainByRequest"]){
                //echo "<p>Open creation process</p><br>";
                createDomain_OpenProcess($domainName);
                header("Location: index.php");
            } else {
                //echo "<p>Request creation process</p><br>";
                createDomain_RequestProcess($domainName);
                header("Location: index.php");
        }
    }
    }
    
    
}

//Web page content
echo " <b>$string</b>
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