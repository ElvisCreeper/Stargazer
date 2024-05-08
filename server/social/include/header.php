<!DOCTYPE html>

<head>
    <title>Bootstrap 5 Example</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../style.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="d-flex flex-column min-vh-100">
    <div class='main'></div>
    <div class="p-5 bg-primary text-white text-center">
        <h1>My First Bootstrap 5 Page</h1>
        <p>Resize this responsive page to see the effect!</p>
    </div>

    <script>
        function showHint(str) {
            if (str.length == 0) {
                document.getElementById("txtHint").innerHTML = "";
                return;
            } else {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function () {
                    if (this.readyState == 4 && this.status == 200) {
                        document.getElementById("txtHint").innerHTML = this.responseText;
                    }
                };
                xmlhttp.open("GET", "../database/getHint.php?q=" + str, true);
                xmlhttp.send();
            }
        }
    </script>

    <nav class="navbar navbar-expand-sm navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="../userpages/homepage.php">Logo</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mynavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mynavbar">
                <ul class="navbar-nav me-auto" id="path">

                </ul>
                <form class="d-flex" id="searchbar">
                    <div class='dropdown'>
                        <div class="row">
                            <div class="input-group">
                                <input class="form-control dropdown-toggle border-end-0 border" type="text"
                                    placeholder="Search..." data-bs-toggle="dropdown" onkeyup="showHint(this.value)"
                                    aria-expanded='false'>
                                <span class="input-group-append">
                                    <button
                                        class="btn btn-outline-secondary bg-white border-start-0 border-bottom-0 border"
                                        type="button" style="margin-left: -40px;">
                                        <i class="bx bx-search"></i>
                                    </button>

                                    <ul class='dropdown-menu' aria-labelledby='dropdownMenuButton' id='txtHint'>
                                    </ul>
                            </div>
                        </div>
                    </div>
                </form>
                <div class='dropdown navbar-brand ms-3'>

                    <ul class='dropdown-menu dropdown-menu-end' aria-labelledby='dropdownMenuButton'>
                        <li class='m-0'><a class='dropdown-item d-flex gap-2 align-items-center'
                                href="../userpages/user.php"><i class='bx bxs-user'></i> View Profile</a></li>
                        <li class='m-0'><a class='dropdown-item d-flex gap-2 align-items-center text-danger'
                                href="../include/logout.php"><i class='bx bx-log-out'></i> Log out</a></li>
                    </ul>
                    <img src="<?php echo $_SESSION['pfp']; ?>" alt="Logo" style="width:40px;height:40px;"
                        class="rounded-pill" data-bs-toggle='dropdown' aria-expanded='false'>
                </div>


            </div>
        </div>
    </nav>