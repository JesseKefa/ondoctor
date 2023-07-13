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
      background-color: green;
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
    
    .prescription-form {
      margin-top: 20px;
      background-color: #f2f2f2;
      padding: 20px;
    }
    
    .prescription-form label {
      display: block;
      margin-bottom: 10px;
    }
    
    .prescription-form select,
    .prescription-form input[type="number"] {
      width: 100%;
      padding: 8px;
      margin-bottom: 10px;
    }
    
    .prescription-form input[type="submit"] {
      background-color: green;
      color: #ffffff;
      border: none;
      padding: 8px 16px;
      font-size: 14px;
      border-radius: 4px;
      cursor: pointer;
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

  <h1>Doctor Dashboard</h1>

  <?php

  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname = "ddapp";

  $conn = new mysqli($servername, $username, $password, $dbname);

  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }

  $doctor_ssn = $_SESSION["ssn"];
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
  ?>

  <div class="prescription-form">
    <h2>Prescribe Drugs</h2>
    <form action="doctor.php" method="post">
      <input type="hidden" name="doctor_ssn" value="<?php echo $doctor_ssn; ?>">
      <label for="patient">Select Patient:</label>
      <select name="patient" id="patient">
        <?php
        $result = $conn->query("SELECT ssn, name FROM patients");
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . $row['ssn'] . '">' . $row['name'] . '</option>';
        }
        ?>
      </select>
      <br>
      <label for="drug">Prescribe Drug:</label>
      <select name="drug" id="drug">
        <?php
        $drugResult = $conn->query("SELECT id, trade_name FROM drugs");
        while ($drugRow = $drugResult->fetch_assoc()) {
            echo '<option value="' . $drugRow['id'] . '">' . $drugRow['trade_name'] . '</option>';
        }
        ?>
      </select>
      <br>
      <label for="quantity">Quantity:</label>
      <input type="number" name="quantity" id="quantity" min="1">
      <br>
      <input type="submit" name="prescribe" value="Prescribe">
    </form>
  </div>

  <?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["prescribe"])) {
  $doctor_ssn = $_POST["doctor_ssn"];
  $patient_ssn = $_POST["patient"];
  $drug_id = $_POST["drug"];
  $quantity = $_POST["quantity"];

  // Perform the prescription processing here

  // Example prescription processing logic
  $prescription_success = false;

  // Check if all required fields are provided
  if (!empty($doctor_ssn) && !empty($patient_ssn) && !empty($drug_id) && !empty($quantity)) {
    // Perform additional validation or database operations if required

    // Example: Insert the prescription into the database
    $query = "INSERT INTO prescriptions (doctor_ssn, patient_ssn, drug_id, quantity) 
              VALUES ('$doctor_ssn', '$patient_ssn', '$drug_id', '$quantity')";

    // Execute the query
    if (mysqli_query($conn, $query)) {
      // Prescription was successfully saved
      $prescription_success = true;
    } else {
      // Error occurred while saving the prescription
      echo "Error: " . mysqli_error($conn);
    }
  } else {
    // Required fields are missing
    echo "Please provide all the required information.";
  }

  // Display success or error message
  if ($prescription_success) {
    echo "<p>Prescription successful!</p>";
  } else {
    echo "<p>Prescription failed. Please try again.</p>";
  }
}
?>

</body>
</html>
