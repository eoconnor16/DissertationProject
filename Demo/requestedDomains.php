<?php
include('Resource/header.php');

//Set Access
runLoggedInCheck('index.php');
$userID = $_SESSION['userid'];
if(!hasAccess($userID, $path, accessLevel::systemAdmin)){
    header("Location: index.php");
}

//Get all requested domains
$requests = getAllDomainRequest();
print_r($requests);

//Validate form
for($i = 0; $i < sizeof($requests); $i++){
    if(isset($_POST['accept'.$i])){
        $domainID = $requests[$i]['DomainID'];
        acceptDomainRequest($domainID);
        header("Location: requestedDomains.php");
    } elseif(isset($_POST['delete'.$i])){
        $domainID = $requests[$i]['DomainID'];
        deleteDomainRequest($domainID);
        header("Location: requestedDomains.php");
    }
}

//Print request with option to accept or reject
//  - State the requested domain's name and then admin/requester
if(sizeof($requests) == 0){
    echo "<div><h2>No Requests</h2></div>
        <div>";
} elseif(sizeof($requests) > 0){
    echo "<div><h2>Requests</h2></div>
        <div>
        <form method='POST'>";

    for($i = 0; $i < sizeof($requests); $i++){
        //get admin
        $domainName = $requests[$i]['Name'];
        $admins = getAdmins($domainName);
        //print_r($admins);

        if(sizeof($admins) == 1){
            $data = getUserData($admins[0]);
            $username = $data['Username'];

            echo "<div class='alert alert-dark'>
                <p>Domain: <b>$domainName</b><br>Admin: <b>$username</b></p>
                <div class='form-group'><button type='submit' class='btn btn-success' name='accept$i'>Accept</button></div>
                <div class='form-group'><button type='submit' class='btn btn-danger' name='delete$i'>Reject</button></div>
            </div>";
        } 
    }
    echo "</form>
    </div>";
}

include('Resource/footer.php');
?>