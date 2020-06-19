<?php
    $host = "eoconnor16.lampt.eeecs.qub.ac.uk";
    $user = "eoconnor16";
    $pw = "HLmhF5ysgLp3VfxJ";
    $db = "eoconnor16";

    $conn = new mysqli($host, $user, $pw, $db);

    if($conn->connect_error){
        echo $conn->connect_error;
    }
    
?>