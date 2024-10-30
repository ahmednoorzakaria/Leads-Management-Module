<?php include 'db_connection.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CREATE LEAD</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: auto;
        }
        .form-control {
            font-size: 1.2em;
        }
    </style>
</head>

<body>
    <div class="container form-container">
        <h2>Create New Lead</h2>

        <form action='create_lead.php' method="post">
            <div class="form-group">
                <label for="formGroupExampleInput">NAME</label> 
                <input type="text" class="form-control" id="formGroupExampleInput" placeholder="Name " name="name" required>
            </div>
            <div class="form-group">
                <label for="formGroupExampleInput2">EMAIL</label>
                <input type="text" class="form-control" id="formGroupExampleInput2" placeholder="Email " name="email">
            </div>
             <div class="form-group">
                <label for="formGroupExampleInput2">PHONE</label>
                <input type="text" class="form-control" id="formGroupExampleInput2" placeholder="Phone " name="phone">
            </div>
             <div class="form-group">
                <label for="formGroupExampleInput2">INTEREST</label>
                <input type="text" class="form-control" id="formGroupExampleInput2" placeholder="Interest " name="interest">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $interest = $_POST['interest'];
            $stmt = $conn->prepare("INSERT INTO leads (name, email, phone, interest) VALUES (?, ?, ?, ?)");
            if ($stmt === false) {
            die("prepare failed: " . htmlspecialchars($conn->error));
            }
            $stmt->bind_param("ssss", $name, $email, $phone, $interest);
            if ($stmt->execute() === false) {
            die("execute failed: " . htmlspecialchars($stmt->error));
            } else {
            $success_message = "Lead added successfully!";
            }
            $stmt->close();
            $conn->close();
            echo "<div class='alert alert-success' role='alert'>$success_message</div>";
            echo "<script>
                setTimeout(function(){
                window.location.reload();
                }, 2000);
              </script>";
        }
        ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>