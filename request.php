

<?php

$fullName = $_POST['full_name'];
$email = $_POST['email'];
$appointmentDate = $_POST['appointment_date'];
$department = $_POST['department'];
$phoneNumber = $_POST['phone_number'];
$message = $_POST['message'];
$primaryPhysician = $_POST['primary_physician'];




$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ddapp";

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}


$sql = "INSERT INTO appointments (full_name, email, appointment_date, department, phone_number, message, primary_physician)
        VALUES ('$fullName', '$email', '$appointmentDate', '$department', '$phoneNumber', '$message', '$primaryPhysician')";

if ($conn->query($sql) === TRUE) {
  echo "Appointment request submitted successfully.";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>

