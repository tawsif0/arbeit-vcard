<?php
session_start();
include '../connect.php';

if (!isset($_SESSION['id']) && !isset($_COOKIE['id'])) {
  header("Location: logout.php");
  exit;
}

$id = $_SESSION['id'] ?? $_COOKIE['id'];

// Use prepared statement for security
$query = "SELECT name, email, phno, ophno, id, image FROM usertable WHERE id = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "s", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
  $row = mysqli_fetch_assoc($result);

  // Assign variables with null coalescing operator
  $name = $row['name'] ?? '';
  $email = $row['email'] ?? '';
  $phno = $row['phno'] ?? '';
  $ophno = $row['ophno'] ?? '';
  $id = $row['id'] ?? '';
  $imageFilename = $row['image'] ?? 'default.jpg';

  // Verify image exists
  $imagePath = "../uploads/" . $imageFilename;
  if (!file_exists($imagePath) || empty($imageFilename)) {
    $imagePath = "../uploads/default.jpg";
  }
} else {
  echo "<script>
            Swal.fire('Error', 'No user found or session expired. Please log in again.', 'error')
            .then(() => window.location = 'logout.php');
          </script>";
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>vCard - profile</title>
  <link rel="shortcut icon" href="../assests/image/arbeit technology logo.png" type="image/x-icon" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
  <!--
    - custom css link
  -->
  <link rel="stylesheet" href="./assets/css/dashboard.css" />
  <link rel="stylesheet" href="./assets/css/image.css">
  <link rel="stylesheet" href="./assets/css/social-icon.css">
  <link rel="stylesheet" href="./assets/css/qr.css">
  <!--
    -  links
  -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-eoGF3tkGTm3R7xy9cZXz5n3vgrO2wkMa3LCzz7spuqKQ6cVBK+7Rr9U8UwRfl5IS" crossorigin="anonymous">
    </script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<body>
  <!--
    - #MAIN
  -->
  <main>
    <!--
      - #SIDEBAR
    -->
    <aside class="sidebar" data-sidebar>
      <div class="sidebar-info">
        <figure class="">
          <div>

            <form class="form" id="form" action="upim.php" enctype="multipart/form-data" method="post">
              <div class="upload">
                <img src="<?php echo htmlspecialchars($imagePath); ?>" width="125" height="125"
                  class="my-3 rounded-circle" alt="Profile Picture">
                <div class="round">
                  <input type="file" name="image" id="image" accept=".jpg, .jpeg, .png">
                  <i class="fa fa-camera" style="color: #fff;"></i>
                </div>
              </div>
            </form>
          </div>
        </figure>

        <div class="info-content">
          <h3 class="name" title="<?php echo $name; ?>"><?php echo $name; ?></h3>

        </div>

        <button class="info_more-btn" data-sidebar-btn>
          <span>Show Contacts</span>

          <ion-icon name="chevron-down"></ion-icon>
        </button>
      </div>

      <div class="sidebar-info_more">
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
          <li class="contact-item">
            <?php
            $a = $_SESSION['id'];
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

        </ul><br>

        <div class="separator"></div>

      </div>
    </aside>

    <!--
      - #main-content
    -->

    <div class="main-content">
      <!--
        - #NAVBAR
      -->

      <nav class="navbar">
        <ul class="navbar-list">
          <li class="navbar-item">
            <button class="navbar-link" onclick="location.replace('../index.php')">Home</button>
          </li>
          <li class="navbar-item">
            <button class="navbar-link active" data-nav-link>About</button>
          </li>

          <li class="navbar-item">
            <button class="navbar-link" data-nav-link>Edit</button>
          </li>


          <li class="navbar-item">
            <button class="navbar-link" data-nav-link>QrCode</button>
          </li>


          <li class="navbar-item">
            <button class="navbar-link" data-nav-link>Social</button>
          </li>
          <li class="navbar-item">
            <button class="navbar-link" onclick="location.replace('logout.php')">Logout</button>
          </li>
        </ul>
      </nav>

      <!--
        - #ABOUT
      -->

      <article class="about active" data-page="about">
        <header>
          <h2 class="h2 article-title">About me</h2>
        </header>

        <?php


        if ($_SERVER["REQUEST_METHOD"] == "POST") {
          if (isset($_POST['about'])) {
            $about = $_POST['about'];
            $a = $_SESSION['id'];
            $checkQuery = "SELECT * FROM usertable WHERE id = '$a'";
            $checkResult = mysqli_query($con, $checkQuery);

            if (mysqli_num_rows($checkResult) > 0) {
              // Update the about text for the existing user
              $updateQuery = "UPDATE usertable SET about = '$about' WHERE id = '$a'";
              $updateResult = mysqli_query($con, $updateQuery);

              if ($updateResult) {
                // Update successful
                echo "<script>
                Swal.fire('Success', 'About text updated!', 'success')
                .then(() => window.location = '" . $_SERVER['PHP_SELF'] . "');
              </script>";
              } else {
                // Update failed
                echo "<script>
                Swal.fire('Error', 'Failed to update about text!', 'error');
              </script>";
              }
            } else {
              // Insert the about text for the user
              $insertQuery = "INSERT INTO usertable (id, about) VALUES ('$a', '$about')";
              $insertResult = mysqli_query($con, $insertQuery);

              if ($insertResult) {
                // Insert successful
                echo "<script>
                Swal.fire('Success', ' Saved!', 'success')
                .then(() => window.location = '" . $_SERVER['PHP_SELF'] . "');
              </script>";
              } else {
                // Insert failed
                echo "<script>
                Swal.fire('Error', 'Failed to save about text!', 'error');
              </script>";
              }
            }
          }
        }

        $a = $_SESSION['id'];
        $sql = "SELECT about FROM usertable WHERE id = '$a'";
        $result = mysqli_query($con, $sql);

        $about = "";
        if ($result && mysqli_num_rows($result) > 0) {
          $row = mysqli_fetch_assoc($result);
          $about = $row['about'];
        }
        ?>


        <section class="about-text">
          <?php if (empty($about)): ?>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
              <textarea name="about" class="form-input" placeholder="Write about yourself" rows="4" cols="50" required
                data-form-input></textarea><br>
              <input type="submit" class="form-btn" value="Save">
            </form>
          <?php else: ?>
            <div>
              <p><?php echo nl2br($about); ?></p>
              <a href="?edit=1" class="form-btn">
                <span>Edit</span>
              </a><br>
            </div>

            <?php if (isset($_GET['edit'])): ?>
              <!-- Show the text box for editing about text -->
              <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <textarea name="about" class="form-input" rows="4" cols="50" required
                  data-form-input><?php echo htmlspecialchars($about); ?></textarea><br>
                <input type="submit" class="form-btn" value="Update">
              </form>
            <?php endif; ?>
          <?php endif; ?>
        </section>

        <!--
          - testimonials modal
        -->
        <div class="modal-container" data-modal-container>
          <div class="overlay" data-overlay></div>
          <section class="testimonials-modal">
            <button class="modal-close-btn" data-modal-close-btn>
              <ion-icon name="close-outline"></ion-icon>
            </button>
            <div class="modal-img-wrapper">
            </div>
            <div class="modal-content">
              <h4 class="h3 modal-title" data-modal-title></h4>
              <div data-modal-text>

              </div>
            </div>
          </section>
        </div>
      </article>

      <!--
        - #edit
      -->

      <article class="edit" data-page="edit">
        <header>
          <h2 class="h2 article-title">Edit info</h2>
        </header>
        <section class="contact-form">
          <h3 class="h3 form-title">Update Information</h3>

          <form action="update.php" method="post" class="form" data-form>
            <div class="input-wrapper">

              <input type="email" name="email" id="email" class="form-input" placeholder="Input new email address"
                value="<?php echo $email; ?>" required data-form-input autocomplete="off" />
              <input type="text" name="name" id="name" class="form-input" placeholder="Input new username"
                value="<?php echo $name; ?>" required data-form-input autocomplete="off" />

            </div>
            <div class="input-wrapper">
              <input type="text" name="phno" id="phno" class="form-input" placeholder="Input new personal number"
                value="<?php echo $phno; ?>" required data-form-input autocomplete="off" />
              <input type="text" name="ophno" id="ophno" class="form-input" placeholder="Input new office number"
                value="<?php echo $ophno; ?>" required data-form-input autocomplete="off" />
            </div>
            <button class="form-btn" type="submit" disabled data-form-btn>
              <ion-icon name="paper-plane"></ion-icon>
              <span>Update info</span>
            </button><br>
          </form>


          <h3 class="h3 form-title">Update Password</h3>

          <form action="changePass.php" method="post" class="form" data-form>
            <div class="input-wrapper">
              <input id="password" name="password" type="password" class="form-input" placeholder="Input old password"
                required data-form-input />
              <input id="con_pass" name="con_pass" type="password" class="form-input" placeholder="Input new password"
                required data-form-input />
            </div>
            <button class="form-btn" type="submit">
              <ion-icon name="paper-plane"></ion-icon>
              <span>Update password</span>
            </button>
          </form>

        </section>
      </article>


      <!--
        - #Qr
      -->

      <article class="portfolio" data-page="qrcode">
        <header>
          <h2 class="h2 article-title">Qr Code</h2>
        </header>
        <h3 class="h3 form-title">Profile Information</h3>
        <div class="qr-container">

          <?php
          $data = "BEGIN:VCARD\nVERSION:3.0\nFN:$name\nEMAIL:$email\nTEL:$phno\nEND:VCARD";
          $qrCodeData = urlencode($data);

          // Convert HSL color to RGB
          
          list($r, $g, $b) = hsl_to_rgb(186, 55, 45);


          // Construct the QR code URL with color customization
          $qrCodeURL = "https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=" . $qrCodeData . "&color=" . $r . "-" . $g . "-" . $b . "&bgcolor=0-0-0";
          function hsl_to_rgb($h, $s, $l)
          {
            $h = $h / 360;
            $s = $s / 100;
            $l = $l / 100;

            $r = $g = $b = 0;

            if ($s == 0) {
              $r = $g = $b = $l;
            } else {
              $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
              $p = 2 * $l - $q;

              $r = hue_to_rgb($p, $q, $h + 1 / 3);
              $g = hue_to_rgb($p, $q, $h);
              $b = hue_to_rgb($p, $q, $h - 1 / 3);
            }

            return array(round($r * 255), round($g * 255), round($b * 255));
          }

          function hue_to_rgb($p, $q, $t)
          {
            if ($t < 0)
              $t += 1;
            if ($t > 1)
              $t -= 1;
            if ($t < 1 / 6)
              return $p + ($q - $p) * 6 * $t;
            if ($t < 1 / 2)
              return $q;
            if ($t < 2 / 3)
              return $p + ($q - $p) * (2 / 3 - $t) * 6;
            return $p;
          }
          ?>






          <div class="qr-container">

            <?php
            if (isset($_SESSION['id'])) {
              // Assuming $_SESSION['id'] contains the user ID
              $id = $_SESSION['id'];
              // Construct the profile URL
              $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
              $host = $_SERVER['HTTP_HOST'];
              $profileURL = $protocol . $host . '/Profile/viewProfile.php?uid=' . $id;


              // URL encode the profile URL
              $profileURLEncoded = urlencode($profileURL);
              // Generate the URL for the second QR code
              $profileQRCodeURL = "https://api.qrserver.com/v1/create-qr-code/?size=140x140&color=" . $r . "-" . $g . "-" . $b . "&bgcolor=0-0-0&data={$profileURLEncoded}";
              echo "<img src='{$profileQRCodeURL}' alt='QR Code'>";
            } else {
              echo "<p>Profile QR Code not available.</p>";
            }
            ?>
            <br>
          </div>

          <a href="<?php echo isset($profileQRCodeURL) ? $profileQRCodeURL : '#'; ?>" download="profile_qr_code.jpg"
            class="form-btn">Download QR Code</a>

          <div class="">
            <button class="" data-select>

            </button>

          </div>

          <ul class="project-list">

          </ul>

      </article>

      <?php

      $retrievedFacebook = $retrievedGitHub = $retrievedLinkedIn = $retrievedlink = "";
      $retrievedAdditionalLinks = [];
      $user_id = $_SESSION['id'];

      $query = "SELECT facebook, github, linkedin, link FROM usertable WHERE id = '$user_id'";
      $result = mysqli_query($con, $query);
      if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $retrievedFacebook = $row['facebook'];
        $retrievedGitHub = $row['github'];
        $retrievedLinkedIn = $row['linkedin'];
        $retrievedlink = $row['link'];
      }

      $query = "SELECT id, link FROM additional_links WHERE user_id = '$user_id'";
      $result = mysqli_query($con, $query);
      if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
          $retrievedAdditionalLinks[] = $row;
        }
      }
      ?>

      <article class="social" data-page="social">
        <header>
          <h2 class="h2 article-title">Social links</h2>
        </header>

        <section class="social-links">
          <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

            <input type="text" class="form-input" name="facebook" placeholder="Input Facebook link"
              value="<?php echo $retrievedFacebook; ?>"><br>

            <input type="text" class="form-input" name="github" placeholder="Input GitHub link"
              value="<?php echo $retrievedGitHub; ?>"><br>

            <input type="text" class="form-input" name="linkedin" placeholder="Input LinkedIn link"
              value="<?php echo $retrievedLinkedIn; ?>"><br>

            <input type="text" class="form-input" name="link" placeholder="Want to add any custom link"
              value="<?php echo $retrievedlink; ?>"><br>

            <div id="additionalLinksContainer">
              <?php if (!empty($retrievedAdditionalLinks)): ?>
                <?php foreach ($retrievedAdditionalLinks as $item): ?>
                  <div class="link-wrapper" data-link-id="<?php echo $item['id']; ?>">
                    <input type="text" class="form-input additional-link-input" name="additionalLinks[]"
                      value="<?php echo htmlspecialchars($item['link']); ?>" readonly>
                    <button type="button" class="delete-link-btn"><i class="fas fa-trash-alt"></i>
                    </button>
                  </div><br>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>

            <input type="hidden" name="deleteLinks" id="deleteLinks">

            <button type="button" id="addLink" class="form-btn">+</button><br><br>

            <?php if (!empty($retrievedFacebook) || !empty($retrievedGitHub) || !empty($retrievedLinkedIn) || !empty($retrievedlink)): ?>
              <input type="submit" name="submit" class="form-btn" value="Update">
            <?php else: ?>
              <input type="submit" name="submit" class="form-btn" value="Submit">
            <?php endif; ?>
          </form>

          <script>
            const container = document.getElementById("additionalLinksContainer");
            const deleteLinksInput = document.getElementById("deleteLinks");

            // Add delete button functionality
            document.addEventListener("click", function (e) {
              if (e.target.closest(".delete-link-btn")) {
                const wrapper = e.target.closest(".link-wrapper"); // <-- FIXED CLASS

                Swal.fire({
                  title: 'Are you sure?',
                  text: "Do you want to delete this link?",
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#d33',
                  cancelButtonColor: '#3085d6',
                  confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                  if (result.isConfirmed) {
                    wrapper.remove(); // This works now
                    Swal.fire('Deleted!', 'Your link has been removed.', 'success');
                  }
                });
              }
            });

            // Add input dynamically
            document.getElementById("addLink").addEventListener("click", function () {
              const existingLinksCount = container.querySelectorAll(".additional-link-input").length;
              if (existingLinksCount < 8) {
                const wrapper = document.createElement("div");
                wrapper.className = "link-wrapper";
                wrapper.innerHTML = `
            <input type="text" class="form-input additional-link-input" name="additionalLinks[]" placeholder="Add your new Link" required>
            <button type="button" class="delete-link-btn"><i class="fas fa-trash-alt"></i>
</button>
          `;
                container.appendChild(wrapper);
                container.appendChild(document.createElement("br"));
              } else {
                Swal.fire({
                  icon: 'warning',
                  title: 'Maximum Limit Reached',
                  text: 'You can add a maximum of 8 links.',
                });
              }
            });
          </script>

          <style>
            .link-wrapper {
              display: flex;
              align-items: center;
              gap: 10px;
            }

            .link-wrapper input {
              flex: 1;
            }

            .delete-link-btn {
              background: #35a7b2;
              color: white;
              border: none;
              border-radius: 4px;
              padding: 5px 10px;
              cursor: pointer;
            }

            .delete-link-btn:hover {
              background: red;
              color: black;
              border: none;
              border-radius: 4px;
              padding: 5px 10px;
              cursor: pointer;
            }
          </style>

          <?php
          if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
            $facebook = htmlspecialchars($_POST['facebook']);
            $github = htmlspecialchars($_POST['github']);
            $linkedin = htmlspecialchars($_POST['linkedin']);
            $link = htmlspecialchars($_POST['link']);

            $sql = "UPDATE usertable SET facebook = '$facebook', github = '$github', linkedin = '$linkedin', link = '$link' WHERE id = '$user_id'";
            $result = mysqli_query($con, $sql);

            if (!$result) {
              echo "<script>Swal.fire('Error', 'Failed to update main table!', 'error');</script>";
            }

            // Delete selected links
            if (!empty($_POST['deleteLinks'])) {
              $idsToDelete = explode(',', $_POST['deleteLinks']);
              foreach ($idsToDelete as $deleteId) {
                $deleteId = intval($deleteId);
                mysqli_query($con, "DELETE FROM additional_links WHERE id = $deleteId AND user_id = '$user_id'");
              }
            }

            // Clear all previous links and insert fresh
            mysqli_query($con, "DELETE FROM additional_links WHERE user_id = '$user_id'");

            if (isset($_POST['additionalLinks']) && is_array($_POST['additionalLinks'])) {
              foreach ($_POST['additionalLinks'] as $additionalLink) {
                $additionalLink = htmlspecialchars($additionalLink);
                if (!empty($additionalLink)) {
                  $sql = "INSERT INTO additional_links (user_id, link) VALUES ('$user_id', '$additionalLink')";
                  $result = mysqli_query($con, $sql);
                  if (!$result) {
                    echo "<script>Swal.fire('Error', 'Failed to insert additional link!', 'error');</script>";
                    break;
                  }
                }
              }
            }

            echo "<script>
        Swal.fire('Success', 'Data updated successfully!', 'success')
        .then(() => window.location = '" . $_SERVER['PHP_SELF'] . "');
      </script>";
          }
          ?>
        </section>
      </article>


    </div>
  </main>

  <!--
    - custom js link
  -->
  <script src="./assets/js/script.js"></script>
  <script script type="text/javascript">
    document.getElementById("image").onchange = function () {
      document.getElementById("form").submit();
    };
  </script>
  <!--
    - ionicon link
  -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

</body>

</html>