<?php
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
    <title>Query Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Query Results</h1>
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
            // Array of queries with descriptive keys
            $queries = [
                "Finding Which Company Booked the Trip" => "
                    SELECT COMPANY.COMPANY_ID, BOOKING_ID
                    FROM COMPANY, BOOKING
                    WHERE COMPANY.COMPANY_ID = BOOKING.COMPANY_ID 
                    AND BOOKING_ID = 303",
                
                "Select Bookings within Certain Time" => "
                    SELECT BOOKING.BOOKING_ID, FIRST_NAME, LAST_NAME
                    FROM BOOKING, TRAVELER
                    WHERE BOOKING.BOOKING_ID = TRAVELER.BOOKING_ID
                    AND BOOKING_DATE BETWEEN TO_DATE('2023-10-01', 'YYYY-MM-DD') 
                    AND TO_DATE('2024-10-31', 'YYYY-MM-DD')",
                
                "Show Tour Guides" => "
                    SELECT EMPLOYEE_ID, FIRST_NAME, LAST_NAME, COMPANY_ID
                    FROM EMPLOYEE
                    WHERE ROLE = 'Guide'
                    ORDER BY LAST_NAME",
                
                "Show POI in Certain Destination" => "
                    SELECT DESTINATION_ID, POI_ID
                    FROM DESTINATION
                    WHERE DESTINATION_ID = 101",  
                 
                "Show Tours in Certain Destination" => "
                    SELECT TOUR_ID, TOUR_NAME, TOUR_TYPE, TOUR_LANGUAGE, DESTINATION_ID
                    FROM TOUR
                    WHERE DESTINATION_ID = 101
                    ORDER BY TOUR_LANGUAGE"  
            ];

            // Execute each query and display results
            foreach ($queries as $description => $query) {
                $stid = oci_parse($conn, $query);
                if (!$stid) {
                    $e = oci_error($conn);
                    echo "Error parsing the query: " . htmlspecialchars($e['message']);
                    exit;
                }
                $r = oci_execute($stid);
                if (!$r) {
                    $e = oci_error($stid);
                    echo "Error executing the query: " . htmlspecialchars($e['message']);
                    exit;
                }

                echo "<h3 class='mt-4'>$description</h3>";
                if ($r) {
                    // Fetch and display results
                    echo "<table class='table table-bordered table-striped'>";
                    echo "<thead><tr>";
                    // Display column names dynamically
                    $num_columns = oci_num_fields($stid);
                    for ($i = 1; $i <= $num_columns; $i++) {
                        echo "<th>" . htmlspecialchars(oci_field_name($stid, $i)) . "</th>";
                    }
                    echo "</tr></thead><tbody>";
                    // Display rows
                    while ($row = oci_fetch_assoc($stid)) {
                        echo "<tr>";
                        foreach ($row as $value) {
                            echo "<td>" . htmlspecialchars($value) . "</td>";
                        }
                        echo "</tr>";
                    }
                    echo "</tbody></table>";
                } else {
                    // Display query failure message
                    $error = oci_error($stid);
                    echo "<div class='alert alert-danger'>Failed to execute query: " . htmlspecialchars($error['message']) . "</div>";
                }
                // Free the statement resource
                oci_free_statement($stid);
            }

            // Close the database connection
            oci_close($conn);
            ?>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
