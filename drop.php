<?php
// Suppress warnings to avoid showing them on the page
error_reporting(E_ERROR | E_PARSE);

// Establish a connection to the Oracle database
$conn = oci_connect(
    's5pradha',
    '06105193',
    '(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=oracle.scs.ryerson.ca)(PORT=1521))(CONNECT_DATA=(SID=orcl)))'
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Tables</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Table Deletion Results</h1>
        <?php if (!$conn): ?>
            <!-- Display connection failure message -->
            <div class="alert alert-danger text-center">
                Unable to connect to the database.
            </div>
        <?php else: ?>
            <!-- Display success connection message -->
            <div class="alert alert-success text-center">
                Connected to the Oracle database.
            </div>
            <?php
            // Array of DROP TABLE queries
            $dropQueries = [
                "DROP TABLE TRAVELER CASCADE CONSTRAINTS", // Fixed the table name
                "DROP TABLE BOOKING CASCADE CONSTRAINTS",
                "DROP TABLE TRAVEL_PACKAGE CASCADE CONSTRAINTS",
                "DROP TABLE TOUR CASCADE CONSTRAINTS",
                "DROP TABLE EMPLOYEE CASCADE CONSTRAINTS",
                "DROP TABLE DESTINATION CASCADE CONSTRAINTS",
                "DROP TABLE COMPANY CASCADE CONSTRAINTS"
            ];

            // Display results in a table
            echo "<div class='table-responsive'>";
            echo "<table class='table table-bordered'>";
            echo "<thead><tr><th>Query</th><th>Status</th><th>Error Message</th></tr></thead>";
            echo "<tbody>";

            // Execute each query
            foreach ($dropQueries as $query) {
                $stid = oci_parse($conn, $query);
                $r = @oci_execute($stid, OCI_COMMIT_ON_SUCCESS); // Suppress errors with '@'
                echo "<tr>";
                echo "<td>" . htmlspecialchars($query) . "</td>";
                if ($r) {
                    echo "<td class='text-success'>Success</td>";
                    echo "<td>-</td>";
                } else {
                    $e = oci_error($stid); // Get error details
                    echo "<td class='text-danger'>Failed</td>";
                    echo "<td>" . htmlspecialchars($e['message']) . "</td>";
                }
                echo "</tr>";
                // Free the statement resource
                oci_free_statement($stid);
            }

            echo "</tbody>";
            echo "</table>";
            echo "</div>";

            // Close the database connection
            oci_close($conn);
            ?>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
