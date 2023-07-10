<?php

session_start();


if (!isset($_SESSION["name"])) {
  header("Location: doctor_login.php");
  exit();
}


$hostname = "localhost";
$username = "root";
$password = "";
$database = "ddapp"; 

$conn = mysqli_connect($hostname, $username, $password, $database);


if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

  if (isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] == 0) {
    $name = $_SESSION["name"];
    $file_name = $_FILES["profile_picture"]["name"];
    $file_tmp = $_FILES["profile_picture"]["tmp_name"];
    $file_size = $_FILES["profile_picture"]["size"];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    

    if ($file_size > 5242880) { 
      $error_message = "File size exceeds the allowed limit (5MB).";
    } else {

      $new_file_name = uniqid("profile_", true) . "." . $file_ext;
      

      $upload_path = "profile_pictures/" . $new_file_name;
      

      if (move_uploaded_file($file_tmp, $upload_path)) {
    
        $query = "INSERT INTO profile_pictures (doctor_name, file_name, file_path) 
                  VALUES ('$name', '$file_name', '$upload_path')";
        
    
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
    table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
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
        <button class="logout-button"><a href="index.php">Home</a></button>
        
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



  <h1>Doctor Dashboard</h1>

<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ddapp";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch doctor's patients
$doctor_ssn = $_GET['ssn']; // Assuming the doctor's SSN is passed via URL parameter
$sql = "SELECT patients.ssn, patients.name, patients.address, patients.age FROM patients JOIN doctors ON patients.primary_physician_ssn = doctors.ssn WHERE doctors.ssn = '$doctor_ssn'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo '<h2>Patients</h2>';
    echo '<table>';
    echo '<tr><th>SSN</th><th>Name</th><th>Address</th><th>Age</th></tr>';

    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $row['ssn'] . '</td>';
        echo '<td>' . $row['name'] . '</td>';
        echo '<td>' . $row['address'] . '</td>';
        echo '<td>' . $row['age'] . '</td>';
        echo '</tr>';
    }

    echo '</table>';
} else {
    echo '<p>No patients found.</p>';
}

// Prescription form
echo '<h2>Prescribe Drugs</h2>';
echo '<form action="prescription.php" method="post">';
echo '<input type="hidden" name="doctor_ssn" value="' . $doctor_ssn . '">';
echo '<label for="patient">Select Patient:</label>';
echo '<select name="patient" id="patient">';

$result = $conn->query("SELECT ssn, name FROM patients");
while ($row = $result->fetch_assoc()) {
    echo '<option value="' . $row['ssn'] . '">' . $row['name'] . '</option>';
}

echo '</select>';
echo '<br>';
echo '<label for="drug">Prescribe Drug:</label>';
echo '<input type="text" name="drug" id="drug">';
echo '<br>';
echo '<label for="quantity">Quantity:</label>';
echo '<input type="number" name="quantity" id="quantity" min="1">';
echo '<br>';
echo '<input type="submit" value="Prescribe">';
echo '</form>';

// Close the database connection
$conn->close();
?>







  


</body>
</html>
