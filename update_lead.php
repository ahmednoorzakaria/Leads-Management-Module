<?php
include 'db_connection.php';

if (isset($_GET['id'])) {
    $leadId = $_GET['id'];

    //validate the leadID
    if (!filter_var($leadId, FILTER_VALIDATE_INT)) {
        die("Invalid lead ID");
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $assignedTo = $_POST['assigned_to'];
        $status = $_POST['status'];

        if (!empty($assignedTo) && !filter_var($assignedTo, FILTER_VALIDATE_INT)) {
            die("Invalid agent ID.");
        }

        $stmt = $conn->prepare("SELECT COUNT(*) FROM agents WHERE id = ?");
        $stmt->bind_param("i", $assignedTo);
        $stmt->execute();
        $stmt->bind_result($agentCount);
        $stmt->fetch();

        if ($agentCount == 0) {
            die("Agent not found");
        }

        $stmt->close();

        //Update the lead into the database
        $stmt = $conn->prepare("UPDATE leads SET assigned_to=?, status=? WHERE id=?");
        $stmt->bind_param("ssi", $assignedTo, $status, $leadId);
        if ($stmt->execute()) {
            header("Location: /leads/list_leads.php");
            exit();
        } else {
            die("Failed to update lead: " . htmlspecialchars($stmt->error));
        }
        $stmt->close();
    }

    //fetch the lead
    $stmt = $conn->prepare("SELECT * FROM leads WHERE id =?");
    $stmt->bind_param("i", $leadId);
    $stmt->execute();
    $leadResult = $stmt->get_result();
    $stmt->close();
    if ($leadResult && $leadResult->num_rows > 0) {
        $lead = $leadResult->fetch_assoc();
    } else {
        die("Lead not found");
    }
} else {
    die("Lead ID not provided");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>Update Lead</title>
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Update Lead Status</h2>
        <form method="POST">
            <div class="form-group">
                <label for="assigned_to">Assign Agent:</label>
                <select class="form-control" id="assigned_to" name="assigned_to">
                    <option value="">Unassigned</option>
                    <?php
                    $stmt = $conn->prepare("SELECT * FROM agents");
                    $stmt->execute();
                    $agentsResult = $stmt->get_result();
                    while ($agent = $agentsResult->fetch_assoc()) {
                        $selected = $agent['id'] == $lead['assigned_to'] ? 'selected' : "";
                        echo "<option value='{$agent['id']}' $selected>{$agent['name']}</option>";
                    }
                    $stmt->close();
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="status">Status:</label>
                <select class="form-control" id="status" name="status">
                    <option value="New" <?= $lead['status'] == 'New' ? 'selected' : '' ?>>New</option>
                    <option value="In progress" <?= $lead['status'] == 'In Progress' ? 'selected' : '' ?>>In progress</option>
                    <option value="Converted" <?= $lead['status'] == 'Converted' ? 'selected' : '' ?>>Converted</option>
                    <option value="Closed" <?= $lead['status'] == 'Closed' ? 'selected' : '' ?>>Closed</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update Lead</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>