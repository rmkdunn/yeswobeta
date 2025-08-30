<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <link rel="icon" href="/favicon.ico" type="image/x-icon" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
    <style>
        /* Shared styles */
        .work-order-photo {
            max-width: 150px;
            max-height: 150px;
            cursor: pointer;
        }

        /* Light theme is default */
        body {
            transition: background-color 0.3s, color 0.3s;
        }

        /* Dark Theme Styles */
        body.dark-mode {
            background-color: #343a40;
            color: white;
        }
        .dark-mode .table {
            background-color: #454d55;
            color: white;
        }
        .dark-mode .modal-content, .dark-mode .card, .dark-mode .card-body {
            background-color: #343a40;
            color: white;
        }
       .dark-mode .dropdown-menu {
            background-color: #343a40;
        }
       .dark-mode .dropdown-item {
            color: white;
        }
       .dark-mode .dropdown-item:hover {
            background-color: #495057;
        }
        .dark-mode .form-control {
            background-color: #495057;
            color: white;
            border-color: #6c757d;
        }
        .dark-mode .form-control::placeholder {
            color: #adb5bd;
        }
        .dark-mode .list-group-item {
             background-color: #454d55;
             border-color: #6c757d;
        }

        /* Theme switcher styles */
        .theme-switcher {
            cursor: pointer;
            margin-left: 15px;
        }
    </style>
    <title>Engineering Task</title>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg sticky-top navbar-dark bg-dark">
        <a class="navbar-brand" href="#">DFWAM</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/index.php">Home</a>
                </li>
				<li class="nav-item">
                	<a class="nav-link" href="/add.php">Add Task</a>
                 </li>
                  <li class="nav-item">
                        <a class="nav-link" href="/search.php">Search Orders</a>
                   
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/print.php">Print Daily Report</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
          
            </ul>
            <form class="form-inline my-2 my-lg-0" method="GET" action="search.php">
                <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search" name="search">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
            </form>
            
            <div class="theme-switcher text-white">
                <span id="theme-icon">🌙</span> Dark Mode
            </div>
            </div>
        </div>
    </nav>