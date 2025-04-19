<?php
session_start();
include '../connect.php';

$uid = "";

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if (isset($_GET['uid'])) {
    $uid = $_GET['uid'];
    $query = "SELECT * FROM usertable WHERE id = '$uid'";
    $selected = mysqli_query($con, $query);

    if (mysqli_num_rows($selected) > 0) {
      $row = mysqli_fetch_assoc($selected);

      $name = $row['name'];
      $email = $row['email'];
      $phno = $row['phno'];
      $ophno = $row['ophno'];
      $imageFilename = $row['image'] ?? 'default.jpg';

      $imagePath = "../uploads/" . $imageFilename;
      if (!file_exists($imagePath) || empty($imageFilename)) {
        $imagePath = "../uploads/default.jpg";
      }

    } else {
      // No user found
      echo "<script>
                    Swal.fire('Error', 'No user found or session expired. Please log in again.', 'error')
                    .then(() => window.location = 'logout.php');
                  </script>";
      exit;
    }
  } else {
    echo "<script>
                Swal.fire('Error', 'Invalid request. UID not provided.', 'error')
                .then(() => window.location = 'logout.php');
              </script>";
    exit;
  }
} else {
  echo 'This is not a GET request.';
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>vCard -User Profile</title>

  <link rel="shortcut icon" href="../assests/image/arbeit technology logo.png" type="image/x-icon" />

  <!--
    - custom css link
  -->
  <link rel="stylesheet" href="./assets/css/profile-page.css" />
  <!--
    - custom image link
  -->
  <link rel="stylesheet" href="./assets/css/image.css">
  <!--
    - custom Social icons link
  -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<body>

  <main>
    <div class="sidebar">
      <div class="sidebars">
        <figure class="">
          <div class="upload">
            <img src="<?php echo htmlspecialchars($imagePath); ?>" width="125" height="125" class="my-3 rounded-circle">
          </div>

        </figure>
        <div class="info-content">
          <h3 class="name" title="<?php echo $name; ?>"><?php echo $name; ?></h3>
        </div>

        <div class="separator"></div>

        <ul class="contacts-list">
          <li class="contact-item">
            <div class="icon-box">
              <ion-icon name="mail-outline"></ion-icon>
            </div>
            <div class="contact-info">
              <p class="contact-title">Email</p>
              <a href="mailto:<?php echo $email; ?>" class="contact-link"><?php echo $email; ?></a>
            </div>
          </li>

          <li class="contact-item">
            <div class="icon-box">
              <ion-icon name="phone-portrait-outline"></ion-icon>
            </div>
            <div class="contact-info">
              <p class="contact-title">Personal</p>
              <a href="tel:<?php echo $phno; ?>" class="contact-link"><?php echo $phno; ?></a>
            </div>
          </li>
          <li class="contact-item">
            <div class="icon-box">
              <ion-icon name="call-outline"></ion-icon>

            </div>

            <div class="contact-info">
              <p class="contact-title">Office</p>

              <a href="tel:<?php echo $ophno; ?>" class="contact-link"><?php echo $ophno; ?></a>
            </div>
          </li>
          <div class="wrapper">
            <li class="contact-item">
              <?php
              $a = $_GET['uid'];
              $sql = "SELECT * FROM usertable WHERE id = '$a'";
              $result = mysqli_query($con, $sql);

              if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $retrievedFacebook = $row['facebook'];
                $retrievedGitHub = $row['github'];
                $retrievedLinkedIn = $row['linkedin'];
                $retrievedlink = $row['link'];
                // Check if any social link is available, if not set as empty strings
                if (empty($retrievedFacebook) && empty($retrievedGitHub) && empty($retrievedLinkedIn) && empty($retrievedlink)) {
                  $retrievedFacebook = "";
                  $retrievedGitHub = "";
                  $retrievedLinkedIn = "";
                  $retrievedlink = "";
                }
              } else {
                // Set as empty strings if no data found
                $retrievedFacebook = "";
                $retrievedGitHub = "";
                $retrievedLinkedIn = "";
                $retrievedlink = "";
              }
              $fetchAdditionalLinksSql = "SELECT * FROM additional_links WHERE user_id = '$a'";
              $fetchAdditionalLinksResult = mysqli_query($con, $fetchAdditionalLinksSql);

              $additionalLinks = [];
              if ($fetchAdditionalLinksResult && mysqli_num_rows($fetchAdditionalLinksResult) > 0) {
                while ($row = mysqli_fetch_assoc($fetchAdditionalLinksResult)) {
                  $additionalLinks[] = $row['link'];
                }
              }
              ?>

              <div class="social-icons">
                <?php if (!empty($retrievedFacebook)): ?>
                  <a href="<?php echo $retrievedFacebook; ?>" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                <?php endif; ?>
                <?php if (!empty($retrievedGitHub)): ?>
                  <a href="<?php echo $retrievedGitHub; ?>" class="icon"><i class="fa-brands fa-github"></i></a>
                <?php endif; ?>
                <?php if (!empty($retrievedLinkedIn)): ?>
                  <a href="<?php echo $retrievedLinkedIn; ?>" class="icon"><i class="fa-brands fa-linkedin-in"></i></a>
                <?php endif; ?>
                <?php if (!empty($retrievedlink)): ?>
                  <a href="<?php echo $retrievedlink; ?>" class="icon"><i class="fas fa-briefcase"></i>

                  </a>
                <?php endif; ?>
                <?php foreach ($additionalLinks as $additionalLink): ?>
                  <?php if (!empty($additionalLink)): ?>
                    <a href="<?php echo $additionalLink; ?>" class="icon"><i class="fa fa-link"></i></a>
                  <?php endif; ?>
                <?php endforeach; ?>
              </div>
            </li>
          </div>
        </ul>

        <div class="separator"></div>

      </div>
    </div>

  </main>

  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>


</body>

</html>