<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assests/css/Navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
    .header-page {
        height: 90px;
        width: 160px;
        overflow: hidden;
        /* This will prevent the image from overflowing if it doesn't fit */
        position: relative;
        /* If you need positioning for the image */
    }

    .header-page img {
        object-fit: contain;
        /* Ensures the image covers the container without distortion */
        width: 100%;
        /* Makes the image fill the container width */
        height: 100%;
        /* Makes the image fill the container height */
    }
    </style>
</head>

<body>
    <div class="navbar">
        <div class="header-page">
            <img src="./assests/image/arbeit technology logo.png" alt="">
        </div>
        <ul>
            <li><a href="index.php">

                    Home
                </a></li>
            <li><a href="index.php#about">

                    About
                </a></li>

        </ul>
        <?php
        session_start();
        if (isset($_SESSION['id'])) {

            echo ' <a class="hire-btn" href="./Profile/index.php" >
            Profile
        </a>';
        } else {

            echo ' <a href="join.php" class="hire-btn">Join/Sign In</a> ';
        }
        ?>

    </div>
</body>

</html>