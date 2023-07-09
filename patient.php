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

    .doctors-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .doctor-row {
            width: 48%;
            margin-bottom: 20px;
        }

        .doctor-card {
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .doctor-card h2 {
            margin: 0;
        }

        .doctor-card p {
            margin: 10px 0;
        }

        .doctor-card h3 {
            margin: 20px 0 10px;
        }

        .doctor-card ul {
            padding-left: 20px;
        }

        .submit-button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #4caf50;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .submit-button:hover {
            background-color: #45a049;
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

  <h1>Choose Your Primary Physician</h1>
    <form action="appointment.php" method="POST">
        <div class="doctors-container">
            <div class="doctor-row">
                <div class="doctor-card">
                    <h2>Dr. Sarah Johnson, MD</h2>
                    <p>Specialization: Internal Medicine</p>
                    <p>Education: Bachelor of Medicine, Master of Medicine</p>
                    <p>Experience: Over 10 years in clinical practice</p>
                    <h3>Reviews:</h3>
                    <ul>
                        <li>"Dr. Johnson is an incredible physician who takes the time to listen to her patients and provides thorough explanations. Highly recommended!" - John D.</li>
                    </ul>
                    <input type="radio" name="primary_physician" value="Dr. Sarah Johnson, MD"> Choose Dr. Sarah Johnson, MD as your primary physician<br>
                </div>

                <div class="doctor-card">
                    <h2>Dr. Michael Smith, MD</h2>
                    <p>Specialization: Cardiology</p>
                    <p>Education: Doctor of Medicine</p>
                    <p>Experience: Over 15 years in clinical practice</p>
                    <h3>Reviews:</h3>
                    <ul>
                        <li>"Dr. Smith is a knowledgeable and caring cardiologist. He explains everything clearly and addresses all concerns. Highly recommended!" - Lisa M.</li>
                    </ul>
                    <input type="radio" name="primary_physician" value="Dr. Michael Smith, MD"> Choose Dr. Michael Smith, MD as your primary physician<br>
                </div>
            </div>

            <div class="doctor-row">
                <div class="doctor-card">
                    <h2>Dr. Emily Davis, MD</h2>
                    <p>Specialization: Pediatrics</p>
                    <p>Education: Doctor of Medicine</p>
                    <p>Experience: Over 8 years in clinical practice</p>
                    <h3>Reviews:</h3>
                    <ul>
                        <li>"Dr. Davis is fantastic with children. She is patient, kind, and knowledgeable. My kids love her!" - Jennifer R.</li>
                    </ul>
                    <input type="radio" name="primary_physician" value="Dr. Emily Davis, MD"> Choose Dr. Emily Davis, MD as your primary physician<br>
                </div>

                <div class="doctor-card">
                    <h2>Dr. James Anderson, MD</h2>
                    <p>Specialization: Orthopedics</p>
                    <p>Education: Doctor of Medicine</p>
                    <p>Experience: Over 12 years in clinical practice</p>
                    <h3>Reviews:</h3>
                    <ul>
                        <li>"Dr. Anderson is an excellent orthopedic surgeon. He helped me recover from a knee injury and provided great post-operative care." - Mark T.</li>
                    </ul>
                    <input type="radio" name="primary_physician" value="Dr. James Anderson, MD"> Choose Dr. James Anderson, MD as your primary physician<br>
                </div>
            </div>

            <div class="doctor-row">
                <div class="doctor-card">
                    <h2>Dr. Laura Roberts, MD</h2>
                    <p>Specialization: Dermatology</p>
                    <p>Education: Doctor of Medicine</p>
                    <p>Experience: Over 10 years in clinical practice</p>
                    <h3>Reviews:</h3>
                    <ul>
                        <li>"Dr. Roberts is a skilled dermatologist. She has helped me improve my skin condition and always provides valuable skincare advice." - Sarah L.</li>
                    </ul>
                    <input type="radio" name="primary_physician" value="Dr. Laura Roberts, MD"> Choose Dr. Laura Roberts, MD as your primary physician<br>
                </div>

                
            </div>


        </div>

        <input class="submit-button" type="submit" value="Schedule Appointment">
    </form>



</body>
</html>
