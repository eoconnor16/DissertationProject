<?php
$directory = "../";
include('../Resource/header.php');

//Access checks
$path = $_REQUEST['Path'];

runLoggedInCheck('../index.php');
$userID = $_SESSION['userid'];
if(!hasAccess($userID, $path, accessLevel::editor)){
    header("Location: ../index.php");
}

//Check form content
$locationError = "";
if(isset($_POST['edit']) || isset($_POST['delete'])){
    $dataCheck = TRUE;
    $location = $_POST['location'];

    if(!$location){
        $dataCheck = FALSE;
        $locationError = "Please select a valid path";
    }

    if($dataCheck == TRUE){
        $showForm = FALSE;
        //Check option selected
        if(isset($_POST['edit'])){
            //Check if location is a page or container
            if(doesPathEndWithPage($location)){
                //PAGE EDIT
                header("Location: editPage.php?Path=$location");
            } elseif(doesPathEndWithContainer($location)){
                //CONTAINER EDIT
                header("Location: editContainer.php?Path=$location");
            } else {
                //PATH ERROR REFRESH
                header("Location: editContent.php?Path=$path");
            }
           
            
        } elseif(isset($_POST['delete'])){
            if(doesPathEndWithPage($location)){
                //PAGE DELETE
                header("Location: deletePage.php?Path=$location");
            } elseif(doesPathEndWithContainer($location)){
                //CONTAINER DELETE
                header("Location: deleteContainer.php?Path=$location");
            } else {
                //PATH ERROR REFRESH
                header("Location: editContent.php?Path=$path");
            }
        } 
    }
}

//Generate form data
$paths = getAllDomainPaths($path);

echo "<form method='POST'>
    <div class='form-group'>
        <label for='location'>Select Path</label>
        <select class='form-control' name='location' placeholder='$path'>";
        foreach($paths as $item){
            echo "<option>$item</option>";
        }
echo "</select>
    </div>
    <div class='form-group'>
        <button class='btn btn-secondary' name='edit' type='submit'>Edit</button>
        <button class='btn btn-secondary' name='delete' type='submit'>Delete</button>
    </div>  
    </form>";


include('../Resource/footer.php');
?>