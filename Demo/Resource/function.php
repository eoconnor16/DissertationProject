<?php
    session_start();
    include('conn.php');
    include('Parsedown.php');
    include('accessLevel.php');

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

/* Function to read a path and return the corresponding domainID
*/
function getPathDomainID($path){
    global $conn;
    $parts = explode("/",$path);
    
    if (sizeof($parts)<1 || $parts[0] == "") {
        exit("Error: invalid path");
    } elseif(sizeof($parts)>=1){
        $domainName = $parts[0];
        $query = "SELECT * FROM `Domain` WHERE Name='".$conn->real_escape_string($domainName)."';";
        $queryReturn = $conn->query($query);
        $row = $queryReturn->fetch_assoc();
        $domainID = $row['DomainID'];
        return $domainID;
    }
   
}

/* Function to return all Domain records linked to a user
   returns a 2D array with DomainID, Name, ContainerID and StatusID per each record
*/
function returnAllDomains($userid) {
    global $conn;
    $domains = array();
    //Get all public domains
    $query = "SELECT * FROM `Domain` WHERE StatusID='1' AND PrivacyID='1';";
    $queryReturn = $conn->query($query);

    while($row = $queryReturn->fetch_assoc()){
        $id = $row['DomainID'];
        $domainData = getDomainData($id);
        array_push($domains, $domainData);
    }


    //Get all private domains that the user is part of
    $query2 = "SELECT UserGroup_Members.UserID, UserGroup_Members.UserGroupID, Domain_Access.DomainID FROM  UserGroup_Members 
    INNER JOIN Domain_Access ON  UserGroup_Members.UserGroupID = Domain_Access.UserGroupID 
    WHERE  UserGroup_Members.UserID ='".$conn->real_escape_string($userid)."';";
    $queryReturn2 = $conn->query($query2);

    if(mysqli_num_rows($queryReturn2)>0){
        while($row2 = $queryReturn2->fetch_assoc()){
            $id = $row2['DomainID'];
            $domainData = getDomainData($id);
            if(!array_search($domains, $domainData)){
                array_push($domains, $domainData);
            }
        }
    }

    return $domains;
}

/* Function to return all active Domain records
   returns a 2D array with DomainID, Name, ContainerID and StatusID per each record
*/
function returnAllPublicDomains(){
    global $conn;
    $domains = array();
    $query = "SELECT * FROM `Domain` WHERE StatusID='1' AND PrivacyID='1';";
    $queryReturn = $conn->query($query);

    while($row = $queryReturn->fetch_assoc()){
        $id = $row['DomainID'];
        $domainData = getDomainData($id);
        array_push($domains, $domainData);
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

/**
 * Function to return a pages Name and markdown content based on its pageID
 */
function returnPageData($pageID){
    global $conn;
    $pages = array();
    $query2 = "SELECT * FROM `Page` WHERE PageID='$pageID';";
    $queryReturn2 = $conn->query($query2);
    $row2 = $queryReturn2->fetch_assoc();
    $name = $row2['Name'];
    $markdown = $row2['Markdown'];
    array_push($pages, $name);
    array_push($pages, $markdown);
    return $pages;
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
    //Check if there is a record for that domain
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

/* Fuction to check for an existing username, returning T/F
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

/* Fuction to check for an existing email , returning T/F
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

/* Fuction to create a new domain record in db
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

function runLoggedInCheck($location){
    if(!isset($_SESSION['userid'])){
        header("Location: $location");
    } 
}



/* Function to check if a user has the appropriate access to a path, returning T/F
*/
function hasAccess($userID, $path, $accessLevel){
    //Vars used
    $ans = FALSE;
    $domainID = getPathDomainID($path);

    $readOnly = accessLevel::readOnly;
    $editor = accessLevel::editor;
    $admin = accessLevel::admin;
    $systemAdmin = accessLevel::systemAdmin;

    //Check asscess level
    switch ($accessLevel){
        case $readOnly:
            //We want to check the domain to see if it is public
            if(isPublicAndActive($path)){
                $ans = TRUE;
            } elseif(isPrivateAndActive($path)){
                //If private we need to check if user has access
                if(isUserGroupMember($path, $userID)){
                    $ans = TRUE;
                }
            }
            
            break;
        case $editor: // User needs to be Editor, Admin or system admin
            if(isDomainEditor($domainID, $userID) == TRUE || isDomainAdmin($domainID, $userID) == TRUE || isSystemAdmin($userID) == TRUE){
                $ans = TRUE;
            }
          break;

        case $admin: // User needs to be Admin or system admin
            if(isDomainAdmin($domainID, $userID) == TRUE || isSystemAdmin($userID) == TRUE){
                $ans = TRUE;
            }
          break;

        case $systemAdmin: // User needs to be system admin
            if(isSystemAdmin($userID) == TRUE){
                $ans = TRUE;
            }
          break;
      }

      return $ans;

}

#### FUNCTION FOR CREATING DOMAIN
### included in  createDomain.php
##
#

/* Fuction to check for an existing domain by name, returning T if existing and F otherwise
*/
function isDomainNameTaken($domainName){
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

/* Fuction to validate a domain name before entered into db
*/
function valdateDomainName($domainName){
}

/* Fuction to create a new container record in db and return it's containerID
*/
function createContainer($containerName){
    global $conn;
    $query ="INSERT INTO `Container` (`ContainerID`, `ParentID`, `MasterID`, `Name`) VALUES (NULL, NULL, NULL, '".$conn->real_escape_string($containerName)."');";
    $conn->query($query);

    $query2 = "SELECT last_insert_id();";
    $queryReturn = $conn->query($query2);
    $row = $queryReturn->fetch_assoc();
    $containerid = $row['last_insert_id()'];
    return $containerid;
}

/* Fuction to create a new domain record in db and return it's domainID
*/
function createDomain($domainName, $containerID, $statusID){
    global $conn;
    $query ="INSERT INTO `Domain` (`DomainID`, `Name`, `ContainerID`, `StatusID`) VALUES (NULL, '".$conn->real_escape_string($domainName)."', '".$conn->real_escape_string($containerID)."', '".$conn->real_escape_string($statusID)."');";
    $conn->query($query);

    $query2 = "SELECT last_insert_id();";
    $queryReturn = $conn->query($query2);
    $row = $queryReturn->fetch_assoc();
    $domainID = $row['last_insert_id()'];
    return $domainID;
}

/* Fuction to assign a new domain admin record in db
*/
function assignAdminToDomain($userID, $domainID){
    global $conn;
    $query ="INSERT INTO `Domain_Roles` (`DomainID`, `UserID`, `RoleID`) VALUES ('".$conn->real_escape_string($domainID)."', '".$conn->real_escape_string($userID)."', '1');";
    $conn->query($query);
}

/* Fuction to carry out the process of creating a domian via request to a system admin
*/
function createDomain_RequestProcess($domainName){
    //Create new container record
    $containerID = createContainer($domainName);

    //Set status of new domain
    $statusID = 3; //requested

    //Create new domain
    $domainID = createDomain($domainName, $containerID, $statusID);

    //Get domain id and assign them as admin
    $userID = $_SESSION['userid'];
    assignAdminToDomain($userID, $domainID);
}

/* Fuction to carry out the process of creating a domain 
*/
function createDomain_OpenProcess($domainName){
    //Create new container record
    $containerID = createContainer($domainName);

    //Set status of new domain
    $statusID = 1;

    //Create new domain
    $domainID = createDomain($domainName, $containerID, $statusID);

    //Get domain id and assign them as admin
    $userID = $_SESSION['userid'];
    assignAdminToDomain($userID, $domainID);
}

#### FUNCTIONS FOR EDITING DOMAIN PROCESS
### included in Editor/index.php, Editor/domainSettings.php
##
#

/* Function to get all of the domains that a domain is associated with 
    either as an admin or as an editor
    returns array with DomainID, RoleID and RoleName per each record
*/
function getAssocDomains($userID){
    global $conn;
    $query ="SELECT Domain_Roles.DomainID, Domain_Roles.RoleID, Role.Name 
                FROM Domain_Roles INNER JOIN Role ON Domain_Roles.RoleID = Role.RoleID
                WHERE Domain_Roles.UserID='".$conn->real_escape_string($userID)."';";
    $queryReturn = $conn->query($query);
    
    $domains = array();
    while($row = $queryReturn->fetch_assoc()){
        $domainID = $row['DomainID'];
        $roleID = $row['RoleID'];
        $roleName = $row['Name'];
        array_push($domains, array($domainID, $roleID, $roleName));
    }

    return $domains;
}

/* Function that returns a domains name from a domainID
*/
function getDomainNameFromID($domainID){
    global $conn;
    $query ="SELECT * FROM Domain WHERE DomainID='".$conn->real_escape_string($domainID)."'";
    $queryReturn = $conn->query($query);
    $row = $queryReturn->fetch_assoc();
    $name = $row['Name'];
    return $name;
}

/* Function to check if a domain is a system admin based on their
    userID, returning T/F
*/
function isSystemAdmin($userID){
    global $conn;
    $query ="SELECT User.UserTypeID, User_Type.Name
    FROM User INNER JOIN User_Type ON User.UserTypeID = User_Type.UserTypeID
    WHERE User.UserID='".$conn->real_escape_string($userID)."';";
    $queryReturn = $conn->query($query);
    $row = $queryReturn->fetch_assoc();
    $type = $row['Name'];
    
    if($type == "System Admin"){
        return TRUE;
    } else {
        return FALSE;
    }
}

/* Function to check if a domain is a domain admin based on their
    userID, return T/F
*/
function isDomainAdmin($domainID, $userID){
    global $conn;
    $query ="SELECT * FROM Domain_Roles WHERE DomainID='".$conn->real_escape_string($domainID)."' AND UserID='".$conn->real_escape_string($userID)."' AND RoleID='1';";
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

/* Function to check if a domain is a domain editor based on their
    userID, return T/F
*/
function isDomainEditor($domainID, $userID){
    global $conn;
    $query ="SELECT * FROM Domain_Roles WHERE DomainID='".$conn->real_escape_string($domainID)."' AND UserID='".$conn->real_escape_string($userID)."' AND RoleID='2';";
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

#### FUNCTIONS FOR ADDING DOMAIN CONTENT PROCESS
### included in Editor/addContent.php
##
#

/* Function to return the full path name of a containerID
*/
function getPath($containerID){
    global $conn;
    $hasParent = TRUE;
    $path = "";
    $count = 0;

    do {
        //Get container's name
        $query ="SELECT * FROM Container WHERE ContainerID='".$conn->real_escape_string($containerID)."';";
        $queryReturn = $conn->query($query);
        $row = $queryReturn->fetch_assoc();
        $name = $row['Name'];

        //Add it to a string
        if($count == 0){
            $path = $name;
        } else {
            $path = $name."/".$path;
        }

        //Check if it has a parent and repeat if so
        $parentID = $row['ParentID'];

        if(!$parentID){
            $hasParent = FALSE;
        } else {
            $containerID = $parentID;  
        }

        $count++;
    } while($hasParent == TRUE);

    return $path;
}

/* Function to return a domain's containerID (root)
*/
function getDomainContainerID($domainID){
    global $conn;
    $query ="SELECT * FROM Domain WHERE DomainID='".$conn->real_escape_string($domainID)."';";
    $queryReturn = $conn->query($query);
    $row = $queryReturn->fetch_assoc();
    $containerID = $row['ContainerID'];
    return $containerID;
}

/* Function to return all cointainer paths that a domain has, intented 
    to be returned to the addContent.php form
*/
function getAllDomainContainerPaths($path){
    global $conn;

    //Get DomainID's containerID
    $domainID = getPathDomainID($path);
    $masterID = getDomainContainerID($domainID);
    $containers = array();
    array_push($containers, $masterID);

    //Get all containerIDis within that master
    $query ="SELECT * FROM Container WHERE MasterID='".$conn->real_escape_string($masterID)."';";
    $queryReturn = $conn->query($query);

    if(!$queryReturn){
        echo $conn->error;
    }

    if (mysqli_num_rows($queryReturn)>0) { 
        while($row = $queryReturn->fetch_assoc()){
            $id = $row['ContainerID'];
            array_push($containers, $id);
        }
    } 

    //Get ContainerID's Path name
    $paths = array();
    foreach($containers as $item) {
        $path = getPath($item);
        array_push($paths, $path);
    }

    //Return
    sort($paths, SORT_STRING);
    return $paths;
   

}

/* Function to return the MasterID/Root containerID of a path
*/
function getPathMasterID($path){
    $domainID = getPathDomainID($path);
    $masterID = getDomainContainerID($domainID);
    return $masterID;
}

/* Function to add a new container record in db and returning it's containerID
*/
function addContainer($name, $path){
    global $conn;
    $parentID = getPathContainerID($path);
    $masterID = getPathMasterID($path);
    $query ="INSERT INTO `Container` (`ContainerID`, `ParentID`, `MasterID`, `Name`) VALUES (NULL, '".$conn->real_escape_string($parentID)."', '".$conn->real_escape_string($masterID)."', '".$conn->real_escape_string($name)."');";
    $conn->query($query);

    $query2 = "SELECT last_insert_id();";
    $queryReturn = $conn->query($query2);
    $row = $queryReturn->fetch_assoc();
    $containerID = $row['last_insert_id()'];
    return $containerID;
}

/* Function to add a new page record in db and returning it's containerID
*/
function addPage($name, $path){
    global $conn;
    $containerID = getPathContainerID($path);
    $query ="INSERT INTO `Page` (`PageID`, `Name`, `Markdown`) VALUES (NULL, '".$conn->real_escape_string($name)."', '');";
    $conn->query($query);

    $query2 = "SELECT last_insert_id();";
    $queryReturn = $conn->query($query2);
    $row = $queryReturn->fetch_assoc();
    $pageID = $row['last_insert_id()'];

    $query3 = "INSERT INTO `Container_Pages` (`ContainerID`, `PageID`) VALUES ('".$conn->real_escape_string($containerID)."', '".$conn->real_escape_string($pageID)."');";
    $conn->query($query3);
}

#### FUNCTIONS FOR EDITIMG DOMAIN CONTENT PROCESS
### included in Editor/editContent.php
##
#

/* Function returning all paths in a domian
*/
function getAllDomainPaths($path){
    global $conn;

    //Get DomainID's containerID
    $domainID = getPathDomainID($path);
    $masterID = getDomainContainerID($domainID);
    $containers = array();
    array_push($containers, $masterID);

    //Get all containerIDis within that master
    $query ="SELECT * FROM Container WHERE MasterID='".$conn->real_escape_string($masterID)."';";
    $queryReturn = $conn->query($query);

    if(!$queryReturn){
        echo $conn->error;
    }

    if (mysqli_num_rows($queryReturn)>0) { 
        while($row = $queryReturn->fetch_assoc()){
            $id = $row['ContainerID'];
            array_push($containers, $id);
        }
    } 

    //Get ContainerID's Path name
    $paths = array();
    foreach($containers as $item) {
        $path = getPath($item);
        array_push($paths, $path);
    }

    //Get all pages in each container
    foreach($containers as $item) {
        $query2 = "SELECT Container_Pages.ContainerID, Container_Pages.PageID, Page.Name FROM Container_Pages 
        INNER JOIN Page ON Container_Pages.PageID = Page.PageID 
        WHERE Container_Pages.ContainerID ='".$conn->real_escape_string($item)."';";

        $queryReturn2 = $conn->query($query2);

        //Get the full path name
        if (mysqli_num_rows($queryReturn2)>0) { 
            while($row = $queryReturn2->fetch_assoc()){
                $name = $row['Name'];
                $path = getPath($item)."/".$name;
                array_push($paths, $path);
            }
        } 
    }

    //Return
    sort($paths, SORT_STRING);
    return $paths;
}

/* Function to check if a path ends with a page, returning T/F
*/
function doesPathEndWithPage($path){
    global $conn;

    //Split path into parts
    $parts = explode("/",$path);
        //print_r($parts);

    if (sizeof($parts)==1){
        return FALSE;
    } elseif (sizeof($parts) > 1){
            //Get parent path & last element
        $parent = getParentPath($path);
        $lastElement = $parts[sizeof($parts)-1];
            //echo "Parent : $parent && Last Element : $lastElement <br>";

        //Get parent containerID and query if theyre is a page within it called $lastElement
        $parentContainerID = getPathContainerID($parent);
        $query ="SELECT Container_Pages.ContainerID, Container_Pages.PageID, Page.Name FROM Container_Pages 
        INNER JOIN Page ON Container_Pages.PageID = Page.PageID 
        WHERE Container_Pages.ContainerID ='".$conn->real_escape_string($parentContainerID)."' AND Page.Name='".$conn->real_escape_string($lastElement)."';";
        $queryReturn = $conn->query($query);
            //echo "$query";

        if (mysqli_num_rows($queryReturn)==0) { 
            return FALSE;
        } elseif (mysqli_num_rows($queryReturn)==1) { 
            return TRUE;
        } elseif (mysqli_num_rows($queryReturn)==2) { 
            return 'ERROR: Multiple records';
        }
    }
}

/* Function to check if a path ends with a container, retuning T/F
*/
function doesPathEndWithContainer($path){
    global $conn;

    //Split path into parts
    $parts = explode("/",$path);
        //print_r($parts);

    //Check path length
    if (sizeof($parts)<1 || $parts[0] == "") {
        exit("Error: invalid path");

    } elseif (sizeof($parts)==1){
        //Find domains name containerID
        $domain = $parts[0];
        $query = "SELECT * FROM `Domain` WHERE Name='".$conn->real_escape_string($domain)."';";
        $queryReturn = $conn->query($query);
        
        //Cehck return
        if (mysqli_num_rows($queryReturn)==0) { 
            return FALSE;
        } elseif (mysqli_num_rows($queryReturn)==1) { 
            return TRUE;
        } elseif (mysqli_num_rows($queryReturn)==2) { 
            return 'ERROR: Multiple records';
        }

    } elseif (sizeof($parts) >= 2){
        //Get parent path & last element
        $parent = getParentPath($path);
        $lastElement = $parts[sizeof($parts)-1];

        //Get parent containerID and query if theyre is a container with that parentID and is called $lastElement
        $parentContainerID = getPathContainerID($parent);
        $query ="SELECT * FROM Container WHERE ParentID='".$conn->real_escape_string($parentContainerID)."' AND Name='".$conn->real_escape_string($lastElement)."';";
        $queryReturn = $conn->query($query);

            //Check return
        if (mysqli_num_rows($queryReturn)==0) { 
            return FALSE;
        } elseif (mysqli_num_rows($queryReturn)==1) { 
            return TRUE;
        } elseif (mysqli_num_rows($queryReturn)==2) { 
            return 'ERROR: Multiple records';
        }
    }

}

#### FUNCTIONS FOR EDITIMG PAGE CONTENT PROCESS
### included in Editor/editPage.php, editContainer.php
##
#

/* Function to change a page's name record in the db if different from existing
*/
function changePageName($path, $newName){
    global $conn;

    //Get current name
    $pageID = getPathPageID($path);
    $pageData = returnPageData($pageID);
    $currentName = $pageData[0];
    
    //if different from new name add to db
    if($currentName != $newName){
        $query = "UPDATE `Page` SET `Name` = '".$conn->real_escape_string($newName)."' WHERE `Page`.`PageID` = '".$conn->real_escape_string($pageID)."';";
        $conn->query($query);
    }

}

/* Function to change a page's markdown record in the db if different from existing
*/
function changePageMarkdown($path, $newMarkdown){
    global $conn;

    //Get current name
    $pageID = getPathPageID($path);
    $pageData = returnPageData($pageID);
    $currentMarkdown = $pageData[1];
    
    //if different from new name add to db
    if($currentMarkdown != $newMarkdown){
        $query = "UPDATE `Page` SET `Markdown` = '".$conn->real_escape_string($newMarkdown)."' WHERE `Page`.`PageID` = '".$conn->real_escape_string($pageID)."';";
        $conn->query($query);
    }
}

/* Function to return all Container data i.e. ParentID, MasterID and Name
*/
function getContainerData($containerID){
    global $conn;
    $container = array();
    $query2 = "SELECT * FROM `Container` WHERE ContainerID='".$conn->real_escape_string($containerID)."';";
    $queryReturn2 = $conn->query($query2);
    $row2 = $queryReturn2->fetch_assoc();
    $parentID = $row2['ParentID'];
    $masterID = $row2['MasterID'];
    $name = $row2['Name'];
    array_push($container, $parentID);
    array_push($container, $masterID);
    array_push($container, $name);
    return $container;
}

/* Function to change a container's name record in the db if different from existing
*/
function changeContainerName($path, $newName){
    global $conn;

    //Get current name
    $containerID = getPathContainerID($path);
    $containerData = getContainerData($containerID);
    $currentName = $containerData[2];
    
    //if different from new name add to db
    if($currentName != $newName){
        $query = "UPDATE `Container` SET `Name` = '".$conn->real_escape_string($newName)."' WHERE `Container`.`ContainerID` = '".$conn->real_escape_string($containerID)."';";
        $conn->query($query);
    }
}

#### FUNCTIONS FOR DELETING A PAGE
### included in Editor/deletePage.php
##
#

/* Function to delete a page record from the db 
*/
function deletePage($path){
    global $conn;

    //Get page data
    $pageID = getPathPageID($path);
    $parentPath = getParentPath($path);
    $containerID =  getPathContainerID($parentPath);
    
    //Need to delete relation to container
    $query = "DELETE FROM `Container_Pages` WHERE ContainerID='".$conn->real_escape_string($containerID)."' AND PageID='".$conn->real_escape_string($pageID)."';";
    $conn->query($query);

    //Need to delete page
    $query2 = "DELETE FROM `Page` WHERE PageID='".$conn->real_escape_string($pageID)."';";
    $conn->query($query2);
}

/* Function checking if a container has any sub-containers, returning T/F
*/
function hasSubcontainer($containerID){
    global $conn;
    $query ="SELECT * FROM `Container` WHERE ParentID='".$conn->real_escape_string($containerID)."';";
    $queryReturn = $conn->query($query);
    
    if(!$queryReturn){
        echo $conn->error;
    }   
    
    if (mysqli_num_rows($queryReturn)==0) { 
        return FALSE;
    } elseif (mysqli_num_rows($queryReturn) >= 1) { 
        return TRUE;
    } 
}

/* Function returning a list of subcontainers(ID's) related to an entered containerID
*/
function getSubcontainer($containerID){
    global $conn;
    $containers = array();
    $query ="SELECT * FROM `Container` WHERE ParentID='".$conn->real_escape_string($containerID)."';";
    $queryReturn = $conn->query($query);
    
    if(!$queryReturn){
        echo $conn->error;
    }   
    
    if (mysqli_num_rows($queryReturn) >= 1) { 
        while($row = $queryReturn->fetch_assoc()){
            $id = $row['ContainerID'];
            array_push($containers, $id);
        }
    } 

    return $containers;
}

/* Function to delete a container record from the db 
*/
function deleteContainer($path){
    global $conn;

    //Get container data
    $containerID =  getPathContainerID($path);
    
    //Need to get all sub containers
    $query = "DELETE FROM `Container` WHERE ContainerID='".$conn->real_escape_string($containerID)."';";
    $conn->query($query);
}

#### FUNCTIONS FOR Assigning/Deleteing EDITORS
### included in Editor/viewEditors.php, removeEditor.php, addEditor.php
##
#

/* Function to get all the editors of a domain and return an array of userID's 
*/
function getEditors($path){
    $domainID = getPathDomainID($path);
    $editors = array();

    //Get all editors of this domain and add to array
    global $conn;
    $containers = array();
    $query ="SELECT * FROM `Domain_Roles` WHERE DomainID='".$conn->real_escape_string($domainID)."' AND RoleID='2';";
    $queryReturn = $conn->query($query);
    
    if(!$queryReturn){
        echo $conn->error;
    }   
    
    if (mysqli_num_rows($queryReturn) >= 1) { 
        while($row = $queryReturn->fetch_assoc()){
            $id = $row['UserID'];
            array_push($editors, $id);
        }
    }
    
    return $editors;
}

/* Function returning domain data base on userID, returning array with firstname, lastname, containerid and statusid
*/
function getUserData($userID){
    global $conn;
    $domain;
    $query2 = "SELECT * FROM `User` WHERE UserID='".$conn->real_escape_string($userID)."';";
    $queryReturn = $conn->query($query2);

    if(!$queryReturn){
        echo $conn->error;
    }   
    
    if (mysqli_num_rows($queryReturn) == 1) { 
        $row = $queryReturn->fetch_assoc();
        $userid = $row['UserID'];
        $fname = $row['FirstName'];
        $lname = $row['LastName'];
        $username = $row['Username'];
        $email = $row['Email'];
        $domain=array("UserID"=>$userid,"Firstname"=>$fname,"Lastname"=>$lname,"Username"=>$username,"Email"=>$email);
        //array_push($domain, $fname, $lname, $containerid, $statusid);
    } else {
        $domain = array();
    }

    return $domain;
   
}

/* Function returning all user data from user table
*/
function getUserDataByUsername($username){
    global $conn;
    $domain;
    $query2 = "SELECT * FROM `User` WHERE Username='".$conn->real_escape_string($username)."';";
    $queryReturn = $conn->query($query2);

    if(!$queryReturn){
        echo $conn->error;
    }   
    
    if (mysqli_num_rows($queryReturn) == 1) { 
        $row = $queryReturn->fetch_assoc();
        $userid = $row['UserID'];
        $fname = $row['FirstName'];
        $lname = $row['LastName'];
        $username = $row['Username'];
        $email = $row['Email'];
        $domain=array("UserID"=>$userid,"Firstname"=>$fname,"Lastname"=>$lname,"Username"=>$username,"Email"=>$email);
        //array_push($domain, $fname, $lname, $containerid, $statusid);
    } else {
        $domain = array();
    }

    return $domain;
}

/* Function to remove an editor form a domain
*/
function removeEditor($userID, $path){
    global $conn;

    //Get domainID
    $domainID =  getPathDomainID($path);
    
    //Need to get all sub containers
    $query = "DELETE FROM `Domain_Roles` WHERE DomainID='".$conn->real_escape_string($domainID)."' AND UserID='".$conn->real_escape_string($userID)."' AND RoleID='2';";
    $conn->query($query);
}

/* Function to add a new editor to a domain
*/
function addEditor($username, $path){
    global $conn;
    //Get data needed
    $domainID =  getPathDomainID($path);
    $userData = getUserDataByUsername($username);
    $userID = $userData['UserID'];

    //Add new editor
    $query ="INSERT INTO `Domain_Roles` (`DomainID`, `UserID`, `RoleID`) VALUES ('".$conn->real_escape_string($domainID)."', '".$conn->real_escape_string($userID)."', '2');";
    $conn->query($query);
}

/* Function to check if a user is a domain editor, returning T/F
*/
function isEditor($username, $path){
    global $conn;
    //Get data needed
    $domainID =  getPathDomainID($path);
    $userData = getUserDataByUsername($username);
    $userID = $userData['UserID'];

    $ans = isDomainEditor($domainID, $userID);
    return $ans;
}

### viewAdmin.php, addAdmin.php, removeAdmin.php
##
#

/* Function to get all the admins of a domain and return an array of userID's 
*/
function getAdmins($path){
    $domainID = getPathDomainID($path);
    $admins = array();

    //Get all editors of this domain and add to array
    global $conn;
    $containers = array();
    $query ="SELECT * FROM `Domain_Roles` WHERE DomainID='".$conn->real_escape_string($domainID)."' AND RoleID='1';";
    $queryReturn = $conn->query($query);
    
    if(!$queryReturn){
        echo $conn->error;
    }   
    
    if (mysqli_num_rows($queryReturn) >= 1) { 
        while($row = $queryReturn->fetch_assoc()){
            $id = $row['UserID'];
            array_push($admins, $id);
        }
    }
    
    return $admins;
}

/* Function to add a new admin to a domain
*/
function addAdmin($username, $path){
    global $conn;
    //Get data needed
    $domainID =  getPathDomainID($path);
    $userData = getUserDataByUsername($username);
    $userID = $userData['UserID'];

    //Add new editor
    $query ="INSERT INTO `Domain_Roles` (`DomainID`, `UserID`, `RoleID`) VALUES ('".$conn->real_escape_string($domainID)."', '".$conn->real_escape_string($userID)."', '1');";
    $conn->query($query);
}

/* Function to check if a user is an admin to a domain returning T/F
*/
function isAdmin($username, $path){
    global $conn;
    //Get data needed
    $domainID =  getPathDomainID($path);
    $userData = getUserDataByUsername($username);
    $userID = $userData['UserID'];

    $ans = isDomainAdmin($domainID, $userID);
    return $ans;
}

function removeAdmin($userID, $path){
    global $conn;

    //Get domainID
    $domainID =  getPathDomainID($path);
    
    //Need to get all sub containers
    $query = "DELETE FROM `Domain_Roles` WHERE DomainID='".$conn->real_escape_string($domainID)."' AND UserID='".$conn->real_escape_string($userID)."' AND RoleID='1';";
    $conn->query($query);
}

#### FUNCTIONS FOR PRIVACY SETTINGS
### included in Editor/privacySettings.php
##
#

/* Function to check if a domain is public, returning T/F
*/
function isPublic($path){
    //Vars used
    global $conn;
    $domainID = getPathDomainID($path);

    //Check if it is public
    $query ="SELECT * FROM `Domain` WHERE DomainID='".$conn->real_escape_string($domainID)."' AND PrivacyID='1';";
    $queryReturn = $conn->query($query);
    
    if(!$queryReturn){
        echo $conn->error;
    }   
    
    if (mysqli_num_rows($queryReturn)==0) { 
        return FALSE;
    } elseif (mysqli_num_rows($queryReturn) >= 1) { 
        return TRUE;
    } 
}

/* Function to check if a domain is public and active, returning T/F
*/
function isPublicAndActive($path){
    //Vars used
    global $conn;
    $domainID = getPathDomainID($path);

    //Check if it is public
    $query ="SELECT * FROM `Domain` WHERE DomainID='".$conn->real_escape_string($domainID)."' AND PrivacyID='1' AND StstusID='1';";
    $queryReturn = $conn->query($query);
    
    if(!$queryReturn){
        echo $conn->error;
    }   
    
    if (mysqli_num_rows($queryReturn)==0) { 
        return FALSE;
    } elseif (mysqli_num_rows($queryReturn) >= 1) { 
        return TRUE;
    } 
}

/* Function to check if a domain is private, returning T/F
*/
function isPrivate($path){
    //Vars used
    global $conn;
    $domainID = getPathDomainID($path);

    //Check if it is public
    $query ="SELECT * FROM `Domain` WHERE DomainID='".$conn->real_escape_string($domainID)."' AND PrivacyID='2';";
    $queryReturn = $conn->query($query);
    
    if(!$queryReturn){
        echo $conn->error;
    }   
    
    if (mysqli_num_rows($queryReturn)==0) { 
        return FALSE;
    } elseif (mysqli_num_rows($queryReturn) >= 1) { 
        return TRUE;
    } 
}

/* Function to check if a domain is private and active, returning T/F
*/
function isPrivateAndActive($path){
     //Vars used
     global $conn;
     $domainID = getPathDomainID($path);
 
     //Check if it is public
     $query ="SELECT * FROM `Domain` WHERE DomainID='".$conn->real_escape_string($domainID)."' AND PrivacyID='2' AND StatusID='1';";
     $queryReturn = $conn->query($query);
     
     if(!$queryReturn){
         echo $conn->error;
     }   
     
     if (mysqli_num_rows($queryReturn)==0) { 
         return FALSE;
     } elseif (mysqli_num_rows($queryReturn) >= 1) { 
         return TRUE;
     } 
}
/* Function to change a domains privacy state to public
*/
function changeToPublic($path){
    //Vars used
    global $conn;
    $domainID = getPathDomainID($path);

    $query = "UPDATE Domain
    SET PrivacyID='1'
    WHERE DomainID='".$conn->real_escape_string($domainID)."';";
    $conn->query($query);

    //Remove usergroup
    removeUserGroup($path);
}

/* Function to change a domains privacy state to private
*/
function changeToPrivate($path){
    //Vars used
    global $conn;
    $domainID = getPathDomainID($path);

    $query = "UPDATE Domain
    SET PrivacyID='2'
    WHERE DomainID='".$conn->real_escape_string($domainID)."';";
    $conn->query($query);

    //Add usergroup
    makeUserGroup($path);
}

/* Function to chechk if a user is a member of a private domains user group
*/
function isUserGroupMember($path, $userID){
    //Vars used
    global $conn;
    $userGroupID = getUserGroup($path);

    //Check if the user is in the userGroup
    $query ="SELECT * FROM `UserGroup_Members` WHERE UserID='".$conn->real_escape_string($userID)."';";
    $queryReturn = $conn->query($query);
    
    if(!$queryReturn){
        echo $conn->error;
    }   
    
    if (mysqli_num_rows($queryReturn)==0) { 
        return FALSE;
    } elseif (mysqli_num_rows($queryReturn) >= 1) { 
        return TRUE;
    } 

}

### Included in editAccess.php

/* Function to check if a domain has a related usergroup, returning T/F
*/
function hasUserGroup($path){
    //Vars used
    global $conn;
    $domainID = getPathDomainID($path);

    //Check if it is public
    $query ="SELECT * FROM `Domain_Access` WHERE DomainID='".$conn->real_escape_string($domainID)."';";
    $queryReturn = $conn->query($query);
    
    if(!$queryReturn){
        echo $conn->error;
    }   
    
    if (mysqli_num_rows($queryReturn)==0) { 
        return FALSE;
    } elseif (mysqli_num_rows($queryReturn) >= 1) { 
        return TRUE;
    } 
}

/* Function get return a paths UserGroupID
*/
function getUserGroup($path){
    //Vars used
    global $conn;
    $domainID = getPathDomainID($path);

    //Check if it is public
    $query ="SELECT * FROM `Domain_Access` WHERE DomainID='".$conn->real_escape_string($domainID)."';";
    $queryReturn = $conn->query($query);
    
    if(!$queryReturn){
        echo $conn->error;
    }   
    
    if (mysqli_num_rows($queryReturn)==0) { 
        return NULL;
    } elseif (mysqli_num_rows($queryReturn) >= 1) { 
        $row = $queryReturn->fetch_assoc();
        $ID = $row['UserGroupID'];
        return $ID;
    } 
}

/* Function to return all domain data in array
*/
function getDomainData($domainID){
    //Vars used
    global $conn;

    $doamin;
    $query2 = "SELECT * FROM `Domain` WHERE DomainID='".$conn->real_escape_string($domainID)."';";
    $queryReturn = $conn->query($query2);

    if(!$queryReturn){
        echo $conn->error;
    }   
    
    if (mysqli_num_rows($queryReturn) == 1) { 
        $row = $queryReturn->fetch_assoc();
        $domainid = $row['DomainID'];
        $name = $row['Name'];
        $containerid = $row['ContainerID'];
        $statusid = $row['StatusID'];
        $privacyid = $row['PrivacyID'];
        $domain=array("DomainID"=>$domainid,"Name"=>$name,"ContainerID"=>$containerid,"StatusID"=>$statusid,"PrivacyID"=>$privacyid);
    } else {
        $domain = array();
    }

    return $domain;
}

/* Function to make a usergroup for a domain
*/
function makeUserGroup($path){
    //Vars used
    global $conn;
    $domainID = getPathDomainID($path);
    $data = getDomainData($domainID);
    $name = $data['Name'];

    //Create user group
    $query ="INSERT INTO `UserGroup` (`UserGroupID`, `Name`) VALUES (NULL, '".$conn->real_escape_string($name)."');";
    $conn->query($query);

    //Get new UsergroupID
    $query2 = "SELECT last_insert_id();";
    $queryReturn = $conn->query($query2);
    $row = $queryReturn->fetch_assoc();
    $userGroupID = $row['last_insert_id()'];

    //Add link to domain_access
    $query3 ="INSERT INTO `Domain_Access` (`DomainID`, `UserGroupID`) VALUES ('".$conn->real_escape_string($domainID)."', '".$conn->real_escape_string($userGroupID)."');";
    $conn->query($query3);
}

/* Function to remove a usergroup from a domain
*/
function removeUserGroup($path){
    //Vars used
    global $conn;
    $userGroupID = getUserGroup($path);

    //Check if it is public
    $query ="DELETE FROM `UserGroup` WHERE USerGroupID='".$conn->real_escape_string($userGroupID)."';";
    $conn->query($query);
}

/* Function to return an array of users in a usergroup
    returning 
*/
function getUserGroupUsers($userGroupID){
    //Vars used
    global $conn;

    $users = array();
    $query2 = "SELECT * FROM `UserGroup_Members` WHERE UserGroupID='".$conn->real_escape_string($userGroupID)."';";
    $queryReturn = $conn->query($query2);

    if(!$queryReturn){
        echo $conn->error;
    }   
    
    if(mysqli_num_rows($queryReturn) >= 1){
        while($row = $queryReturn->fetch_assoc()){
            $userID = $row['UserID'];
            $userData = getUserData($userID);
            array_push($users, $userData);
        }
    }

    return $users;

    
}

/* Function to remove a user from a usergroup
*/
function removeUserGroupMember($usergroupid, $userid){
    global $conn;
    $query = "DELETE FROM `UserGroup_Members` WHERE UserGroupID='".$conn->real_escape_string($usergroupid)."' AND UserID='".$conn->real_escape_string($userid)."';";
    $conn->query($query);
}

/* Function to add a user to a usergroup
*/
function addUserGroupMember($usergroupid, $userid){
    global $conn;
    $query = "INSERT INTO `UserGroup_Members` (`UserGroupID`, `UserID`) VALUES ('".$conn->real_escape_string($usergroupid)."', '".$conn->real_escape_string($userid)."');";
    $conn->query($query);
}

#### FUNCTIONS FOR DELETING DOMAIN
### included in Editor/deleteDomain.php
##
#

function deleteDomain($path){
    //Vars used
    global $conn;
    $domainID = getPathDomainID($path);
    $masterID = getDomainContainerID($domainID);

    //Need to get all containers
    $query = "DELETE FROM `Container` WHERE ContainerID='".$conn->real_escape_string($masterID)."';";
    $conn->query($query);
}




?>