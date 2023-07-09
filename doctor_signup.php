<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ddapp";

// Establish a database connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check if the connection was successful
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST["name"];
  $specialty = $_POST["specialty"];
  $yearsOfExperience = $_POST["years_of_experience"];
  $password = $_POST["password"];

  // Hash the password
  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  // Prepare the SQL query to insert edoctor details
  $query = "INSERT INTO doctors (name, specialty, years_of_experience, password)
            VALUES ('$name', '$specialty', '$yearsOfExperience', '$hashedPassword')";

  // Execute the query
  if (mysqli_query($conn, $query)) {
    echo "Edoctor registered successfully!";
  } else {
    echo "Error: " . mysqli_error($conn);
  }

  // Close the database connection
  mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edoctor Signup</title>
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
    <h2>Edoctor Signup</h2>
    <form method="post">
      <label for="name">Name:</label>
      <input type="text" id="name" name="name" required><br><br>
      <label for="specialty">Specialty:</label>
      <input type="text" id="specialty" name="specialty" required><br><br>
      <label for="years_of_experience">Years of Experience:</label>
      <input type="number" id="years_of_experience" name="years_of_experience" required><br><br>
      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required><br><br>
      <input type="submit" value="Signup">
    </form>
  </div>
</body>
</html>