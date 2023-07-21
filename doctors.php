<?php
session_start();
if (!isset($_SESSION["name"])) {
  header("Location: patient_login.php");
  exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ddapp";

$conn = mysqli_connect($servername, $username, $password, $dbname);

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
      
        $query = "INSERT INTO patient_profile_pictures (patient_ssn, file_name, file_path) 
                  SELECT ssn, '$file_name', '$upload_path' FROM patients WHERE name = '$name'";

   
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
$query = "SELECT file_path FROM patient_profile_pictures 
          JOIN patients ON patient_profile_pictures.patient_ssn = patients.ssn
          WHERE patients.name = '$name' ORDER BY patient_profile_pictures.id DESC LIMIT 1";
$result = mysqli_query($conn, $query);
$profile_picture = mysqli_fetch_assoc($result);

mysqli_close($conn);
?>


<!DOCTYPE html>
<html>
<head>
  <title>Patient Landing Page</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="copyright" content="MACode ID, https://macodeid.com/">
  <link rel="stylesheet" href="../ddapp/assets/css/maicons.css">
  <link rel="stylesheet" href="../ddapp/assets/css/bootstrap.css">
  <link rel="stylesheet" href="../ddapp/assets/vendor/owl-carousel/css/owl.carousel.css">
  <link rel="stylesheet" href="../ddapp/assets/vendor/animate/animate.css">
  <link rel="stylesheet" href="vidstyle.css">
  <link rel="stylesheet" href="../ddapp/assets/css/theme.css">
  <style>
    body {
      background-color: #f2f2f2;
      font-family: Arial, sans-serif;
    }

    .container-1 {
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
      margin-top: 0px;
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

  
  <div class="back-to-top"></div>

  <header>
    <div class="topbar">
      <div class="container">
        <div class="row">
          <div class="col-sm-8 text-sm">
            <div class="site-info">
              
              <a href="tel:+254740860629"><span class="mai-call text-primary"></span> +254740860629</a>
              <span class="divider">|</span>
              <a href="mailto:admin@ondoc.com"><span class="mai-mail text-primary"></span> admin@ondoc.com</a>
            </div>
          </div>
          <div class="col-sm-4 text-right text-sm">
            <div class="social-mini-button">
              
             
          </div>
        </div> 
      </div> 
    </div> 

    <nav class="navbar navbar-expand-lg navbar-light shadow-sm">
      <div class="container">
        <a class="navbar-brand" href="home.php"><span class="text-primary">On</span>Doc</a>

       

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupport" aria-controls="navbarSupport" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupport">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item active">
              <a class="nav-link" href="patient.php">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="doctors.php">Doctors</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="about.php">About Us</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="contact.php">Contact</a>
            
            <li class="nav-item">
              <li  ><a class="btn btn-primary" href="logout.php">Logout</a></li>
          </ul>
        </div> 
      </div> 
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
      
    </div>
 
    </nav>
  </header>

  <div class="page-hero bg-image overlay-dark" style="background-image: url(../assets/img/doctor.jpg);">
    <div class="banner-section">
      <div class="container text-center wow ">
        
        <h1 class="font-weight-normal">Our Doctors</h1>
      </div> 
    </div> 
  </div>

  <div class="page-section bg-light">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-10">

          <div class="row">
            
            <div class="col-md-6 col-lg-4 py-3 wow ">
              <div class="card-doctor">
                <div class="header">
                 <img src="../assets/img/doctors/doc male.png" alt="">
                  <div class="meta">
                    
                  </div>
                </div>
                
          </div>

        </div>
      </div>
    </div> 
  </div> 
  <div class="page-section">
    <div class="container">
      <h1 class="text-center mb-5 wow ">Our Doctors</h1>

  

<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ddapp";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$sql = "SELECT ssn, name, specialty, years_of_experience FROM doctors";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo '<table>';
    echo '<tr><th>SSN</th><th>Name</th><th>Specialty</th><th>Years of experience</th></tr>';

    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $row['ssn'] . '</td>';
        echo '<td>' . $row['name'] . '</td>';
        echo '<td>' . $row['specialty'] . '</td>';
        echo '<td>' . $row['years_of_experience'] . '</td>';
        echo '</tr>';
    }

    echo '</table>';
} else {
    echo '<p>No doctors found.</p>';
}
?> 
  


  <?php
  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname = "ddapp";

  $conn = new mysqli($servername, $username, $password, $dbname);

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['full_name']) && isset($_POST['email']) && isset($_POST['appointment_date']) && isset($_POST['department']) && isset($_POST['phone_number']) && isset($_POST['message'])) {
      $fullName = $_POST['full_name'];
      $email = $_POST['email'];
      $appointmentDate = $_POST['appointment_date'];
      $department = $_POST['department'];
      $phoneNumber = $_POST['phone_number'];
      $message = $_POST['message'];
     

      $sql = "INSERT INTO appointments (full_name, email, appointment_date, department, phone_number, message)
              VALUES ('$fullName', '$email', '$appointmentDate', '$department', '$phoneNumber', '$message')";

      if ($conn->query($sql) === TRUE) {
        echo "Appointment request submitted successfully.";
      } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
      }
    }
  }

  $conn->close();
  ?>

<div class="page-section">
  <div class="container">
    <h1 class="text-center wow">Request a Prescription</h1>

    <form class="main-form" method="post" action="">
      <div class="row mt-5">
        <div class="col-12 col-sm-6 py-2 wow">
          <input type="text" class="form-control" placeholder="Full name" name="full_name">
        </div>
        <div class="col-12 col-sm-6 py-2 wow">
          <input type="email" class="form-control" placeholder="Email address" name="email">
        </div>
        <div class="col-12 col-sm-6 py-2 wow" data-wow-delay="300ms">
          <input type="date" class="form-control" name="appointment_date">
        </div>
        <div class="col-12 col-sm-6 py-2 wow" data-wow-delay="300ms">
          <select name="department" id="department" class="custom-select">
            <option value="general">General Health</option>
            <option value="cardiology">Cardiology</option>
            <option value="dental">Dental</option>
            <option value="neurology">Neurology</option>
            <option value="orthopaedics">Orthopaedics</option>
          </select>
        </div>
        <div class="col-12 py-2 wow" data-wow-delay="300ms">
          <input type="text" class="form-control" placeholder="Phone number" name="phone_number">
        </div>
        <div class="col-12 py-2 wow" data-wow-delay="300ms">
          <textarea name="message" id="message" class="form-control" rows="6" placeholder="Enter message.." name="message"></textarea>
        </div>
      </div>

      <button type="submit" class="btn btn-primary mt-3 wow">Submit Request</button>

    </form>
  </div>
</div>



















<div class="page-section">
  <div class="container">
    <h1 class="text-center wow">Update Primary Physician</h1>

    <form class="main-form" method="post" action="">
      <div class="row mt-5">
        <div class="col-12 col-sm-6 py-2 wow">
          <input type="text" class="form-control" placeholder="Full name" name="full_name">
        </div>
        <div class="col-12 py-2 wow" data-wow-delay="300ms">
        <input type="text" class="form-control" placeholder="Primary Physician" name="primary_physician">
        </div>
      </div>

      <button type="submit" class="btn btn-primary mt-3 wow">Update Primary Physician</button>
    </form>
  </div>
</div>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = $_POST['full_name'];
    $primaryPhysician = $_POST['primary_physician'];

    // Form validation
    if (empty($fullName) || empty($primaryPhysician)) {
        echo "Please fill in all the fields.";
    } else {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "ddapp";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Update the primary physician SSN for the patient
        $sql = "UPDATE patients SET primary_physician_ssn='$primaryPhysician' WHERE name='$fullName'";

        if ($conn->query($sql) === TRUE) {
            echo "Primary physician updated successfully for patient: $fullName";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        // Close the connection
        $conn->close();
    }
}
?>














<script src="../assets/js/jquery-3.5.1.min.js"></script>

<script src="../assets/js/bootstrap.bundle.min.js"></script>

<script src="../assets/vendor/owl-carousel/js/owl.carousel.min.js"></script>

<script src="../assets/vendor/wow/wow.min.js"></script>

<script src="../assets/js/theme.js"></script>
  
</body>
</html>