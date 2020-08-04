<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php if(isset($title)){
                    echo $title;
                  }?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
</head>

<body class>
  <?php
    include('function.php');
    include('settings.php');

    if(!isset($directory)){
      $directory = "";
    }

    if(isLoggedIn() == TRUE){
      $userID = $_SESSION['userid'];
      if(isSystemAdmin($userID)){
        echo "
      <nav class='navbar navbar-expand-lg navbar-dark bg-dark'>
        <a class='navbar-brand' href='".$directory."index.php'>Wiki</a>

        <div class='navbar-nav-scroll' id='navbarNav'>
          <ul class='navbar-nav'>
            <li class='nav-item active'>
              <a class='nav-link' href='".$directory."logout.php'>Logout</a>
            </li>
            <li class='nav-item active'>
              <a class='nav-link' href='".$directory."createDomain.php'>Create Domain</a>
            </li>
            <li class='nav-item active'>
              <a class='nav-link' href='".$directory."Editor/index.php'>Editor</a>
            </li>
            <li class='nav-item active'>
              <a class='nav-link' href='".$directory."requestedDomains.php'>Requests</a>
            </li>
          </ul>
        </div>

        <form class='form-inline my-2'>
          <input class='form-control mr-sm-2' type='search' placeholder='Search' aria-label='Search'>
          <button class='btn btn-outline-success my-2 my-sm-0' type='submit'>Search</button>
        </form>
      </nav>
      ";
      } else {
        echo "
      <nav class='navbar navbar-expand-lg navbar-dark bg-dark'>
        <a class='navbar-brand' href='".$directory."index.php'>Wiki</a>

        <div class='navbar-nav-scroll' id='navbarNav'>
          <ul class='navbar-nav'>
            <li class='nav-item active'>
              <a class='nav-link' href='".$directory."logout.php'>Logout</a>
            </li>
            <li class='nav-item active'>
              <a class='nav-link' href='".$directory."createDomain.php'>Create Domain</a>
            </li>
            <li class='nav-item active'>
              <a class='nav-link' href='".$directory."Editor/index.php'>Editor</a>
            </li>
            <li class='nav-item active'>
              <a class='nav-link' href='".$directory."savedDomains.php'>Saved Domains</a>
            </li>
          </ul>
        </div>

        <form class='form-inline my-2'>
          <input class='form-control mr-sm-2' type='search' placeholder='Search' aria-label='Search'>
          <button class='btn btn-outline-success my-2 my-sm-0' type='submit'>Search</button>
        </form>
      </nav>
      ";
      }
      
    } else {
      echo "
      <nav class='navbar navbar-expand-lg navbar-dark bg-dark'>
        <a class='navbar-brand' href='index.php'>Wiki</a>

        <div class='navbar-nav-scroll' id='navbarNav'>
          <ul class='navbar-nav'>
            <li class='nav-item active'>
              <a class='nav-link' href='".$directory."login.php'>Login</a>
            </li>
            <li class='nav-item'>
              <a class='nav-link' href='".$directory."register.php'>Register</a>
            </li>
          </ul>
        </div>

        <form class='form-inline my-2'>
          <input class='form-control mr-sm-2' type='search' placeholder='Search' aria-label='Search'>
          <button class='btn btn-outline-success my-2 my-sm-0' type='submit'>Search</button>
        </form>
      </nav>
      ";
    }
  ?>

<div class="container">
