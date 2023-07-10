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

// Process prescription form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_ssn = $_POST['patient_ssn'];
    $doctor_ssn = $_POST['doctor_ssn'];
    $drug = $_POST['drug'];
    $quantity = $_POST['quantity'];

    // Perform necessary validations on the form data
    // ...

    // Insert the prescription into the prescriptions table
    $sql = "INSERT INTO prescriptions (patient_ssn, doctor_ssn, drug_id, date_prescribed, quantity) VALUES ('$patient_ssn', '$doctor_ssn', '$drug_id', NOW(), '$quantity')";

    if ($conn->query($sql) === TRUE) {
        echo "Prescription added successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>
