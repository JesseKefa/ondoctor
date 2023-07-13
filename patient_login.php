<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ddapp";


$conn = mysqli_connect($servername, $username, $password, $dbname);


if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST["name"];
  $password = $_POST["password"];


  $query = "SELECT * FROM patients WHERE name = '$name' LIMIT 1";

  $result = mysqli_query($conn, $query);

  if ($result) {
  
    $patient = mysqli_fetch_assoc($result);

    if ($patient && password_verify($password, $patient['password'])) {
      
      session_start();
      $_SESSION["name"] = $name;

      header("Location: patient.php");
      exit();
    } else {
      echo "<p style='color: red;'>Invalid name or password</p>";
    }
  } else {
    echo "<p style='color: red;'>Error: " . mysqli_error($conn) . "</p>";
  }

 
  mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Patient Login</title>
  <style>
    body {
      background-color: #f2f2f2;
      font-family: Arial, sans-serif;
    }
  
    .container {
      max-width: 400px;
      margin: 0 auto;
      padding: 40px;
      background-color: #ffffff;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
  
    h2 {
      text-align: center;
    }
  
    form {
      margin-top: 20px;
    }
  
    label {
      display: block;
      margin-bottom: 8px;
    }
  
    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
    }
  
    input[type="submit"] {
      display: block;
      width: 100%;
      padding: 8px;
      margin-top: 20px;
      background-color: #4CAF50;
      color: #ffffff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
  
    input[type="submit"]:hover {
      background-color: #45a049;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Patient Login</h2>
    <form method="post">
      <label for="name">Name:</label>
      <input type="text" id="name" name="name" required><br><br>
      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required><br><br>
      <input type="submit" value="Login">
      <p>Don't have an account? <a href="patient_signup.php">Signup here</a></p>
    </form>
  </div>
</body>
</html>
