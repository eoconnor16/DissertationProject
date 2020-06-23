<?php
    session_start();
    include('conn.php');
    include('Parsedown.php');

#### FUNCTIONS FOR LOADING DOMAIN PROCESS
### included in index.php, view.php, page.php
##
#

/* Function to return the parent path of our current path
*/
function getParentPath($path){
    $parts = explode("/",$path);

    if(sizeof($parts) <= 1){
        exit("Error: invalid path");
    } else {
        $newPathArray = array();
        for($i = 0; $i < sizeof($parts)-1; $i++){
            array_push($newPathArray, $parts[$i]);
        }
        $newPath = implode("/",$newPathArray);
        return $newPath;
    }
}

/* Function to return the length of a path
*/
function getPathLength($path){
    $parts = explode("/",$path);
    $length = sizeof($parts);
    return $length;
}

/* Function to read a path and return the corresponding containerID
*/
function getPathContainerID($path){
    global $conn;
    $parts = explode("/",$path);
    
    if (sizeof($parts)<1 || $parts[0] == "") {
        exit("Error: invalid path");

    } elseif (sizeof($parts)==1){
        //echo "<b>Option One</b>";
        $domain = $parts[0];
        $query = "SELECT * FROM `Domain` WHERE Name='".$conn->real_escape_string($domain)."';";
        $queryReturn = $conn->query($query);
        $row = $queryReturn->fetch_assoc();
        $containerid = $row['ContainerID'];
        return $containerid;

    } elseif (sizeof($parts)==2){
        //echo "<b>Option Two</b>";
        $domain = $parts[0];
        $container = $parts[1];

        //Get Domains's ContainerID
        $query = "SELECT * FROM `Domain` WHERE Name='".$conn->real_escape_string($domain)."';";
        $queryReturn = $conn->query($query);
        $row = $queryReturn->fetch_assoc();
        $masterid = $row['ContainerID'];

        //Find Container
        $query2 = "SELECT * FROM `Container` WHERE MasterID='".$conn->real_escape_string($masterid)."' AND Name='".$conn->real_escape_string($container)."';";
        $queryReturn2 = $conn->query($query2);
        $row2 = $queryReturn2->fetch_assoc();
        $containerid = $row2['ContainerID'];
        return $containerid;

    } elseif(sizeof($parts)>2){
        //echo "<b>Option Three</b>";
        $domain = $parts[0];
        $parent = $parts[sizeof($parts)-2];
        $container = $parts[sizeof($parts)-1];

        //Get Domain's ContaierID
        $query = "SELECT * FROM `Domain` WHERE Name='".$conn->real_escape_string($domain)."';";
        $queryReturn = $conn->query($query);
        $row = $queryReturn->fetch_assoc();
        $masterid = $row['ContainerID'];

        //Get Parent's ContainerID
        $query2 = "SELECT * FROM `Container` WHERE MasterID='".$conn->real_escape_string($masterid)."' AND Name='".$conn->real_escape_string($parent)."';";
        $queryReturn2 = $conn->query($query2);
        $row2 = $queryReturn2->fetch_assoc();
        $parentid = $row2['ContainerID'];

        //Get Container
        $query3 = "SELECT * FROM `Container` WHERE MasterID='".$conn->real_escape_string($masterid)."' AND ParentID='".$conn->real_escape_string($parentid)."' AND Name='".$conn->real_escape_string($container)."';";
        $queryReturn3 = $conn->query($query3);
        $row3 = $queryReturn3->fetch_assoc();
        $containerid = $row3['ContainerID'];
        return $containerid;
    }
}

/* Function to return all records in Domains 
   returns a 2D array with DomainID, Name and ContainerID per each record
*/
function returnAllDomains() {
    global $conn;
    $domains = array();
    $query = "SELECT * FROM `Domain`;";
    $queryReturn = $conn->query($query);

    while($row = $queryReturn->fetch_assoc()){
        $id = $row['DomainID'];
        $name = $row['Name'];
        $containerid = $row['ContainerID'];
        array_push($domains, array($id, $name, $containerid));
    }

    return $domains;
}

/* Function to return all containers in container 
   returns a 2D array with ContainerID, Parent, Name per each record
*/
function returnContainers($containerid){
    global $conn;
    $containers = array();
    $query = "SELECT * FROM `Container` WHERE ParentID=".$conn->real_escape_string($containerid);
    $queryReturn = $conn->query($query);

    while($row = $queryReturn->fetch_assoc()){
        $id = $row['ContainerID'];
        $parentid = $row['ParentID'];
        $name = $row['Name'];
        array_push($containers, array($id, $parentid, $name));
    }

    return $containers;
}

/* Function to return all pages in Container 
   returns a 2D array with PageID and Name per each record
*/
function returnPages($containerid){
    global $conn;
    $pages = array();
    $query = "SELECT * FROM `Container_Pages` WHERE ContainerID=".$conn->real_escape_string($containerid);
    $queryReturn = $conn->query($query);

    while($row = $queryReturn->fetch_assoc()){
        $pageid = $row['PageID'];
        $query2 = "SELECT * FROM `Page` WHERE PageID='$pageid';";
        $queryReturn2 = $conn->query($query2);
        $row2 = $queryReturn2->fetch_assoc();
        $name = $row2['Name'];
        array_push($pages, array($pageid, $name));
    }

    return $pages;
}

/* Function to read a path and return the corresponding pageID
*/
function getPathPageID($path){
    global $conn;
    $parts = explode("/",$path);
    
    if (sizeof($parts)<2) {
        exit("Error: invalid path"); 

    } elseif (sizeof($parts)==2) { 
        $pagename = $parts[sizeof($parts)-1];
        $domainname = $parts[0];

        //Get domain's containerID
        $query = "SELECT * FROM `Domain` WHERE Name='".$conn->real_escape_string($domainname)."';";
        $queryReturn = $conn->query($query);
        $row = $queryReturn->fetch_assoc();
        $masterid = $row['ContainerID'];

        //Find related page
        $query3 = "SELECT Container_Pages.ContainerID, Container_Pages.PageID, Page.Name FROM Container_Pages 
                    INNER JOIN Page ON Container_Pages.PageID = Page.PageID 
                    WHERE Container_Pages.ContainerID ='".$conn->real_escape_string($masterid)."' && Page.Name ='".$conn->real_escape_string($pagename)."';";

        $queryReturn3 = $conn->query($query3);
        $row3 = $queryReturn3->fetch_assoc();
        $pageid = $row3['PageID'];
        return $pageid;

    } elseif (sizeof($parts)>2){
        $pagename = $parts[sizeof($parts)-1];
        $containername = $parts[sizeof($parts)-2];
        $domainname = $parts[0];

        //Get domain's containerID
        $query = "SELECT * FROM `Domain` WHERE Name='".$conn->real_escape_string($domainname)."';";
        //echo "$query !!!";
        $queryReturn = $conn->query($query);
        $row = $queryReturn->fetch_assoc();
        $masterid = $row['ContainerID'];

        //Find container that the page is in
        $query2 = "SELECT * FROM `Container` WHERE MasterID='".$conn->real_escape_string($masterid)."' AND Name='".$conn->real_escape_string($containername)."';";
        //echo "$query2 !!!";
        $queryReturn2 = $conn->query($query2);
        $row2 = $queryReturn2->fetch_assoc();
        $containerid = $row2['ContainerID'];

        //Find related page
        $query3 = "SELECT Container_Pages.ContainerID, Container_Pages.PageID, Page.Name FROM Container_Pages 
                    INNER JOIN Page ON Container_Pages.PageID = Page.PageID 
                    WHERE Container_Pages.ContainerID ='".$conn->real_escape_string($containerid)."' && Page.Name ='".$conn->real_escape_string($pagename)."';";
        //echo "$query3 !!!";
        $queryReturn3 = $conn->query($query3);
        $row3 = $queryReturn3->fetch_assoc();
        $pageid = $row3['PageID'];
        return $pageid;
    }
}

/* Function to read in a page's markdwon, convert it to html using parsedown.php  
   and return the result
*/
function ConvertPageMarkdown($pageid){
    global $conn;
    $Parsedown = new Parsedown();

    $query = "SELECT * FROM `Page` WHERE PageID='".$conn->real_escape_string($pageid)."';";
    $queryReturn = $conn->query($query);
    $row = $queryReturn->fetch_assoc();

    $markDown = $row['Markdown'];
    $html = $Parsedown->text($markDown);

    return $html;
}

#### FUNCTION FOR LOGIN PROCESS
### included in login.php, logout.php, header.php
##
#

/* Function to check if there is a matching db record base on login input  
   and return T/F
*/
function checkLoginDetails($username, $password){
    global $conn;
    //Check if there is a record for that user
    $query ="SELECT UserID FROM User WHERE Username='".$conn->real_escape_string($username)."' AND password=SHA1('".$conn->real_escape_string($password)."');";
    $queryReturn = $conn->query($query);
    
    if(!$queryReturn){
        echo $conn->error;
    }   
    
    if (mysqli_num_rows($queryReturn)==0) { 
        return FALSE;
    } elseif (mysqli_num_rows($queryReturn)==1) { 
        return TRUE;
    } elseif (mysqli_num_rows($queryReturn)==2) { 
        return 'ERROR: Multiple records';
    }
                
}

/* Fuction to return a userID base long login input
*/
function getUserIDFromLogin($username, $password){
    global $conn;
    $query ="SELECT UserID FROM User WHERE Username='".$conn->real_escape_string($username)."' AND password=SHA1('".$conn->real_escape_string($password)."');";
    $queryReturn = $conn->query($query);
    $row = $queryReturn->fetch_assoc();
    $userid = $row['UserID'];
    return $userid;
}

/* Fuction to return the usertype base on a userid
*/
function getUserType($userid){
    global $conn;
    $query ="SELECT User_Type.UserTypeID, User_Type.Name 
                FROM User INNER JOIN User_Type ON User.UserTypeID = User_Type.UserTypeID 
                WHERE User.UserID='".$conn->real_escape_string($userid)."';";
    $queryReturn = $conn->query($query);
    $row = $queryReturn->fetch_assoc();
    $usertype = $row['UserTypeID'];
    return $usertype;
}

/* Fuction to unset log in session variable
*/
function logout(){
    unset($_SESSION['userid']);
}

/* Fuction to check if a login session variable has been created
*/
function isLoggedIn(){
    if(isset($_SESSION['userid'])){
        return TRUE;
    } else {
        return FALSE;
    }
}

#### FUNCTIONS FOR REGISTRATION PROCESS
### included in register.php
##
#

/* Fuction to compare two password, returning true if they match are are of appropriate length
*/
function validatePasswords($pass1, $pass2){
    $result = FALSE; 
    if($pass1 == $pass2){
        if(strlen($pass1) > 5){
            $result = TRUE;
        }
    }
    return $result;
}

/* Fuction to check for an existing user by username, returning T/F
*/
function checkForUsername($username){
    global $conn;
    $query ="SELECT Username FROM User WHERE Username='".$conn->real_escape_string($username)."';";
    $queryReturn = $conn->query($query);
    
    if(!$queryReturn){
        echo $conn->error;
    }   
    
    if (mysqli_num_rows($queryReturn)==0) { 
        return FALSE;
    } elseif (mysqli_num_rows($queryReturn)==1) { 
        return TRUE;
    } elseif (mysqli_num_rows($queryReturn)==2) { 
        return 'ERROR: Multiple records';
    }
}

/* Fuction to check for an existing user by email, returning T/F
*/
function checkForEmail($email){
    global $conn;
    $query ="SELECT Email FROM User WHERE Email='".$conn->real_escape_string($email)."';";
    $queryReturn = $conn->query($query);
    
    if(!$queryReturn){
        echo $conn->error;
    }   
    
    if (mysqli_num_rows($queryReturn)==0) { 
        return FALSE;
    } elseif (mysqli_num_rows($queryReturn)==1) { 
        return TRUE;
    } elseif (mysqli_num_rows($queryReturn)==2) { 
        return 'ERROR: Multiple records';
    }
}

/* Fuction to create a new user record in db
*/
function createNewPublicUser($email, $username, $password, $firstname, $lastname){
    global $conn;
    $query = "INSERT INTO `User` (`UserID`, `UserTypeID`, `FirstName`, `LastName`, `Username`, `Email`, `Password`) VALUES (NULL, '1', '".$conn->real_escape_string($firstname)."', '".$conn->real_escape_string($lastname)."', '".$conn->real_escape_string($username)."', '".$conn->real_escape_string($email)."', SHA1('".$conn->real_escape_string($password)."'));";
    $queryReturn = $conn->query($query);
}

#### FUNCTION FOR SECURITY/ACCESS
### included in createDomain.php
##
#

function runLoggedInCheck(){
    if(!isset($_SESSION['userid'])){
        header("Location: index.php");
    } 
}

#### FUNCTION FOR CREATING DOMAIN
### included in  createDomain.php
##
#

/* Fuction to check for an existing domain by name, returning T if existing and F otherwise
*/
function checkDomainName($domainName){
    global $conn;
    $query ="SELECT Name FROM Domain WHERE Name='".$conn->real_escape_string($domainName)."';";
    $queryReturn = $conn->query($query);
    
    if(!$queryReturn){
        echo $conn->error;
    } 
    
    if (mysqli_num_rows($queryReturn)==0) { 
        return FALSE;
    } elseif (mysqli_num_rows($queryReturn)==1) { 
        return TRUE;
    } elseif (mysqli_num_rows($queryReturn)==2) { 
        return 'ERROR: Multiple records';
    }
}
?>