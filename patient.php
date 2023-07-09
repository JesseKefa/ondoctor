<?php
// Check if the user is logged in
session_start();
if (!isset($_SESSION["name"])) {
    header("Location: patient_login.php");
    exit();
}

// Retrieve the patient's information from the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ddapp";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$name = $_SESSION["name"];

$query = "SELECT * FROM patients WHERE name = '$name' LIMIT 1";
$result = mysqli_query($conn, $query);
$patient = mysqli_fetch_assoc($result);

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

        h2 {
            text-align: center;
        }

        .profile {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .profile img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
        }

        .profile .name {
            font-size: 24px;
            font-weight: bold;
        }

        .logout {
            text-align: right;
        }

        .logout a {
            color: red;
            text-decoration: none;
        }

        .upload-form {
            margin-top: 20px;
        }

        .upload-form label {
            display: block;
            margin-bottom: 8px;
        }

        .upload-form input[type="file"] {
            margin-bottom: 8px;
        }

        .upload-form input[type="submit"] {
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
        <div class="profile">
            <img src="profile_pictures/<?php echo $patient['profile_picture']; ?>" alt="Profile Picture">
            <div class="name"><?php echo $patient['name']; ?></div>
        </div>
        <div class="logout">
            <a href="logout.php">Logout</a>
        </div>
        <h2>Welcome to the Patient Landing Page</h2>
        <p>Here, you can view and manage your patient information.</p>
        <div class="upload-form">
            <h2>Upload Profile Picture</h2>
            <form method="post" enctype="multipart/form-data">
                <label for="profile_picture">Choose an image:</label>
                <input type="file" id="profile_picture" name="profile_picture" required>
                <input type="submit" value="Upload">
            </form>
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] == 0) {
                    $file_name = $_FILES["profile_picture"]["name"];
                    $file_tmp = $_FILES["profile_picture"]["tmp_name"];
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    $allowed_extensions = array("jpg", "jpeg", "png");

                    if (in_array($file_ext, $allowed_extensions)) {
                        $new_file_name = "profile_" . uniqid() . "." . $file_ext;
                        $upload_path = "profile_pictures/" . $new_file_name;

                        if (move_uploaded_file($file_tmp, $upload_path)) {
                            // Update the profile picture in the database
                            $query = "UPDATE patients SET profile_picture = '$new_file_name' WHERE name = '$name'";
                            mysqli_query($conn, $query);
                            echo "<p class='success-message'>Profile picture uploaded successfully!</p>";
                        } else {
                            echo "<p class='error-message'>Failed to upload the profile picture.</p>";
                        }
                    } else {
                        echo "<p class='error-message'>Invalid file format. Allowed formats: JPG, JPEG, PNG.</p>";
                    }
                } else {
                    echo "<p class='error-message'>Please choose a file to upload.</p>";
                }
            }
            ?>
        </div>
    </div>
</body>
</html>
