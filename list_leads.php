<?php include 'db_connection.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>List Leads</title>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .table thead th {
            background-color: #007bff;
            color: #fff;
        }
        .form-inline .form-group {
            margin-right: 15px;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Leads</h2>
        <form method="GET" class="form-inline mb-4">
            <div class="form-group">
                <label for="status" class="mr-2">Status:</label>
                <select name="status" id="status" class="form-control">
                    <option value="">ALL</option>
                    <option value="New">New</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Converted">Converted</option>
                    <option value="Closed">Closed</option>
                </select>
            </div>
            <div class="form-group">
                <label for="agent" class="mr-2">Agent:</label>
                <select name="agent" id="agent" class="form-control">
                    <option value="">ALL</option>
                    <?php
                    $stmt = $conn->prepare("SELECT * FROM agents");
                    $stmt->execute();
                    $agentsResult = $stmt->get_result();
                    while ($agent = $agentsResult->fetch_assoc()) {
                        echo "<option value='{$agent['id']}'>{$agent['name']}</option>";
                    }
                    $stmt->close();
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        <!-- Leads List -->

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Interest</th>
                    <th>Assigned Agent</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $statusFilter = isset($_GET['status']) && $_GET['status'] !== '' ? "AND status= ?" : '';
                $agentFilter = isset($_GET['agent']) && $_GET['agent'] !== '' ? "AND assigned_to = ?" : '';
                $query = "SELECT leads.*, agents.name AS agent_name FROM leads LEFT JOIN agents ON leads.assigned_to = agents.id WHERE 1=1 $statusFilter $agentFilter";
                $stmt = $conn->prepare($query);

                if ($statusFilter && $agentFilter) {
                    $stmt->bind_param("ss", $_GET['status'], $_GET['agent']);
                } elseif ($statusFilter) {
                    $stmt->bind_param("s", $_GET['status']);
                } elseif ($agentFilter) {
                    $stmt->bind_param("s", $_GET['agent']);
                }

                $stmt->execute();
                $result = $stmt->get_result();

                while ($lead = $result->fetch_assoc()) {
                    $notification = '';
                    if ($lead['status'] == 'New' || $lead['assigned_to'] == null) {
                        $notification = '<span class="badge badge-warning">New</span>';
                    }
                    echo "<tr>
                    <td>" . htmlspecialchars($lead['name']) . " $notification</td>
                    <td>" . htmlspecialchars($lead['email']) . "</td>
                    <td>" . htmlspecialchars($lead['phone']) . "</td>
                    <td>" . htmlspecialchars($lead['interest']) . "</td>
                    <td>" . htmlspecialchars($lead['agent_name'] ?: 'Unassigned') . "</td>
                    <td>" . htmlspecialchars($lead['status']) . "</td>
                    <td>
                        <a href='update_lead.php?id=" . htmlspecialchars($lead['id']) . "' class='btn btn-sm btn-warning'>Update</a>
                    </td>
                    </tr>";
                }
                $stmt->close();
                ?>
            </tbody>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>