<?php
// Start the session
session_start();

// Check if the user is not logged in
if (!isset($_SESSION["name"])) {
  header("Location: doctor_login.php");
  exit();
}

// Database configuration
$hostname = "localhost";
$username = "root";
$password = "";
$database = "ddapp"; // Update with your actual database name

// Establish a database connection
$conn = mysqli_connect($hostname, $username, $password, $database);

// Check if the connection was successful
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Check if a file is uploaded
  if (isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] == 0) {
    $name = $_SESSION["name"];
    $file_name = $_FILES["profile_picture"]["name"];
    $file_tmp = $_FILES["profile_picture"]["tmp_name"];
    $file_size = $_FILES["profile_picture"]["size"];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    // Check file size
    if ($file_size > 5242880) { // 5MB (in bytes)
      $error_message = "File size exceeds the allowed limit (5MB).";
    } else {
      // Generate a unique file name to avoid conflicts
      $new_file_name = uniqid("profile_", true) . "." . $file_ext;
      
      // Set the file upload path
      $upload_path = "profile_pictures/" . $new_file_name;
      
      // Move the uploaded file to the desired location
      if (move_uploaded_file($file_tmp, $upload_path)) {
        // Prepare the SQL query to insert the file details into the database
        $query = "INSERT INTO profile_pictures (doctor_name, file_name, file_path) 
                  VALUES ('$name', '$file_name', '$upload_path')";
        
        // Execute the query
        if (mysqli_query($conn, $query)) {
          $success_message = "Profile picture uploaded successfully!";
        } else {
          $error_message = "Error: " . mysqli_error($conn);
        }
      } else {
        $error_message = "Failed to upload the profile picture.";
      }
    }
  } else {
    $error_message = "Please choose a file to upload.";
  }
}

// Fetch the doctor's profile picture from the database
$name = $_SESSION["name"];
$query = "SELECT file_path FROM profile_pictures WHERE doctor_name = '$name' ORDER BY id DESC LIMIT 1";
$result = mysqli_query($conn, $query);
$profile_picture = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Doctor Landing Page</title>
  <style>
    body {
      background-color: #f2f2f2;
      font-family: Arial, sans-serif;
    }

    .container {
      max-width: 800px;
      margin: 0 auto;
      padding: 40px;
      background-color: #ffffff;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .logout-button {
      background-color: #e74c3c;
      color: #ffffff;
      border: none;
      padding: 8px 16px;
      font-size: 14px;
      border-radius: 4px;
      cursor: pointer;
    }

    .profile-section {
      display: flex;
      align-items: center;
    }

    .profile-picture {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 20px;
    }

    .profile-info {
      font-size: 16px;
      font-weight: bold;
    }

    .upload-form {
      margin-top: 20px;
    }

    .success-message {
      color: green;
      margin-top: 10px;
    }

    .error-message {
      color: red;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <div class="profile-section">
        <?php if ($profile_picture && file_exists($profile_picture["file_path"])) : ?>
          <img class="profile-picture" src="<?php echo $profile_picture["file_path"]; ?>" alt="Profile Picture">
        <?php else : ?>
          <img class="profile-picture" src="default_profile_picture.png" alt="Profile Picture">
        <?php endif; ?>
        <div class="profile-info">
          Welcome, <?php echo $_SESSION["name"]; ?>!
        </div>
      </div>
      <form method="post" action="logout.php">
        <button class="logout-button" type="submit">Logout</button>
      </form>
    </div>
    <div class="upload-form">
      <h2>Upload Profile Picture</h2>
      <form method="post" enctype="multipart/form-data">
        <input type="file" name="profile_picture" required><br><br>
        <input type="submit" value="Upload">
      </form>
      <?php if (isset($success_message)): ?>
        <p class="success-message"><?php echo $success_message; ?></p>
      <?php endif; ?>
      <?php if (isset($error_message)): ?>
        <p class="error-message"><?php echo $error_message; ?></p>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
