<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ddapp";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_ssn = $_POST['patient'];
    $doctor_ssn = $_POST['doctor_ssn'];
    $drug = $_POST['drug'];
    $quantity = $_POST['quantity'];

    // Perform necessary validations on the form data
    // ...

    // Insert the prescription into the prescriptions table
    $sql = "INSERT INTO prescriptions (patient_ssn, doctor_ssn, drug_id, date_prescribed, quantity) VALUES ('$patient_ssn', '$doctor_ssn', '$drug', CURDATE(), '$quantity')";

    if ($conn->query($sql) === TRUE) {
        echo "Prescription added successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}


$conn->close();
?>
