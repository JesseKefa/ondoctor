<?php

$host = 'localhost';
$db = 'ddapp';
$user = 'root';
$password = '';


try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}


$users = array();

try {
    $stmt = $conn->prepare("SELECT * FROM patients");
    $stmt->execute();
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($patients as $patient) {
        $users[] = array(
            'id' => $patient['ssn'],
            'username' => $patient['name'],
            'type' => 'Patient'
        );
    }

    $stmt = $conn->prepare("SELECT * FROM doctors");
    $stmt->execute();
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($doctors as $doctor) {
        $users[] = array(
            'id' => $doctor['ssn'],
            'username' => $doctor['name'],
            'type' => 'Doctor'
        );
    }

    $stmt = $conn->prepare("SELECT * FROM pharmaceutical_companies");
    $stmt->execute();
    $pharmaceuticalCompanies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($pharmaceuticalCompanies as $company) {
        $users[] = array(
            'id' => $company['id'],
            'username' => $company['name'],
            'type' => 'Pharmaceutical Company'
        );
    }

    $stmt = $conn->prepare("SELECT * FROM pharmacies");
    $stmt->execute();
    $pharmacies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($pharmacies as $pharmacy) {
        $users[] = array(
            'id' => $pharmacy['id'],
            'username' => $pharmacy['name'],
            'type' => 'Pharmacy'
        );
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = $_GET['id'];

    if ($action == 'edit') {
        $user = array();
        $type = '';

        // Fetch user details from the database based on the ID and type
        switch ($_GET['type']) {
            case 'Patient':
                $stmt = $conn->prepare("SELECT * FROM patients WHERE ssn = :id");
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                $type = 'Patient';
                break;

            case 'Doctor':
                $stmt = $conn->prepare("SELECT * FROM doctors WHERE ssn = :id");
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                $type = 'Doctor';
                break;

            case 'Pharmaceutical Company':
                $stmt = $conn->prepare("SELECT * FROM pharmaceutical_companies WHERE id = :id");
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                $type = 'Pharmaceutical Company';
                break;

            case 'Pharmacy':
                $stmt = $conn->prepare("SELECT * FROM pharmacies WHERE id = :id");
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                $type = 'Pharmacy';
                break;
        }

        echo '<h2>Edit User</h2>';
        echo '<form action="admin.php?action=update&id=' . $id . '&type=' . $type . '" method="POST">';
        echo '<input type="hidden" name="id" value="' . $user['id'] . '">';
        echo '<label for="username">Username:</label>';
        echo '<input type="text" id="username" name="username" value="' . $user['name'] . '" required><br><br>';
        echo '<label for="password">Password:</label>';
        echo '<input type="password" id="password" name="password" required><br><br>';
        echo '<input type="submit" value="Update">';
        echo '</form>';
    } elseif ($action == 'update') {
       
        $id = $_POST['id'];
        $username = $_POST['username'];
        $password = $_POST['password'];

        
        switch ($_GET['type']) {
            case 'Patient':
                $stmt = $conn->prepare("UPDATE patients SET name = :username, password = :password WHERE ssn = :id");
                break;

            case 'Doctor':
                $stmt = $conn->prepare("UPDATE doctors SET name = :username, password = :password WHERE ssn = :id");
                break;

            case 'Pharmaceutical Company':
                $stmt = $conn->prepare("UPDATE pharmaceutical_companies SET name = :username, password = :password WHERE id = :id");
                break;

            case 'Pharmacy':
                $stmt = $conn->prepare("UPDATE pharmacies SET name = :username, password = :password WHERE id = :id");
                break;
        }

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);

        try {
            $stmt->execute();
            echo "User updated successfully!";
        } catch(PDOException $e) {
            echo "Error updating user: " . $e->getMessage();
        }
    } elseif ($action == 'delete') {
        // Delete user from the database based on the provided ID and type
        switch ($_GET['type']) {
            case 'Patient':
                $stmt = $conn->prepare("DELETE FROM patients WHERE ssn = :id");
                break;

            case 'Doctor':
                $stmt = $conn->prepare("DELETE FROM doctors WHERE ssn = :id");
                break;

            case 'Pharmaceutical Company':
                $stmt = $conn->prepare("DELETE FROM pharmaceutical_companies WHERE id = :id");
                break;

            case 'Pharmacy':
                $stmt = $conn->prepare("DELETE FROM pharmacies WHERE id = :id");
                break;
        }

        $stmt->bindParam(':id', $id);

        try {
            $stmt->execute();
            echo "User deleted successfully!";
        } catch(PDOException $e) {
            echo "Error deleting user: " . $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>OnDoc - Admin Dashboard</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        
        th, td {
            text-align: left;
            padding: 8px;
        }
        
        th {
            background-color: #4CAF50;
            color: white;
        }
        
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        
        a {
            text-decoration: none;
        }
    </style>
</head>
<body>
    <h1>OnDoc - Admin Dashboard</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Type</th>
            <th>Action</th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo $user['username']; ?></td>
                <td><?php echo $user['type']; ?></td>
                <td>
                    <a href="admin.php?action=edit&id=<?php echo $user['id']; ?>&type=<?php echo $user['type']; ?>">Edit</a> |
                    <a href="admin.php?action=delete&id=<?php echo $user['id']; ?>&type=<?php echo $user['type']; ?>">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
