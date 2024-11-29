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
    <title>Data Insertion Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Data Insertion Results</h1>
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
            // Array of SQL INSERT queries
            $insertQueries = [
                // Insert into COMPANY
                "INSERT INTO COMPANY (COMPANY_ID, ADDRESS, PHONE_NUMBER, COMPANY_NAME) VALUES (1, '123 Maple St', 1234567890, 'Adventure Co.')",
                "INSERT INTO COMPANY (COMPANY_ID, ADDRESS, PHONE_NUMBER, COMPANY_NAME) VALUES (2, '456 Oak St', 9876543210, 'Travel Masters')",
                "INSERT INTO COMPANY (COMPANY_ID, ADDRESS, PHONE_NUMBER, COMPANY_NAME) VALUES (3, '789 Pine St', 1122334455, 'Globetrotters Inc.')",

                // Insert into DESTINATION
                "INSERT INTO DESTINATION (DESTINATION_ID, POI_ID) VALUES (101, 1001)",
                "INSERT INTO DESTINATION (DESTINATION_ID, POI_ID) VALUES (102, 1002)",
                "INSERT INTO DESTINATION (DESTINATION_ID, POI_ID) VALUES (103, 1003)",

                // Insert into TRAVEL_PACKAGE
                "INSERT INTO TRAVEL_PACKAGE (DESTINATION_ID, TRAVEL_PACKAGE_ID, PACKAGE_PRICE) VALUES (101, 201, 1500)",
                "INSERT INTO TRAVEL_PACKAGE (DESTINATION_ID, TRAVEL_PACKAGE_ID, PACKAGE_PRICE) VALUES (102, 202, 2000)",
                "INSERT INTO TRAVEL_PACKAGE (DESTINATION_ID, TRAVEL_PACKAGE_ID, PACKAGE_PRICE) VALUES (103, 203, 2500)",

                // Insert into BOOKING
                "INSERT INTO BOOKING (BOOKING_ID, DEPOSIT_AMOUNT, BOOKING_DATE, COMPANY_ID, TRAVEL_PACKAGE_ID) VALUES (301, 500, TO_DATE('2023-10-01', 'YYYY-MM-DD'), 1, 201)",
                "INSERT INTO BOOKING (BOOKING_ID, DEPOSIT_AMOUNT, BOOKING_DATE, COMPANY_ID, TRAVEL_PACKAGE_ID) VALUES (302, 600, TO_DATE('2023-10-10', 'YYYY-MM-DD'), 2, 202)",
                "INSERT INTO BOOKING (BOOKING_ID, DEPOSIT_AMOUNT, BOOKING_DATE, COMPANY_ID, TRAVEL_PACKAGE_ID) VALUES (303, 700, TO_DATE('2023-10-20', 'YYYY-MM-DD'), 3, 203)",

                // Insert into EMPLOYEE
                "INSERT INTO EMPLOYEE (EMPLOYEE_ID, PHONE_NUMBER, LEAVE_DATE, ROLE, COMPANY_ID, HIRE_DATE, LAST_NAME, FIRST_NAME) 
                    VALUES (401, 5551234567, TO_DATE('2024-12-31', 'YYYY-MM-DD'), 'Manager', 1, TO_DATE('2022-01-01', 'YYYY-MM-DD'), 'Smith', 'John')",
                "INSERT INTO EMPLOYEE (EMPLOYEE_ID, PHONE_NUMBER, LEAVE_DATE, ROLE, COMPANY_ID, HIRE_DATE, LAST_NAME, FIRST_NAME) 
                    VALUES (402, 5559876543, TO_DATE('2024-12-31', 'YYYY-MM-DD'), 'Guide', 2, TO_DATE('2021-06-15', 'YYYY-MM-DD'), 'Doe', 'Jane')",
                "INSERT INTO EMPLOYEE (EMPLOYEE_ID, PHONE_NUMBER, LEAVE_DATE, ROLE, COMPANY_ID, HIRE_DATE, LAST_NAME, FIRST_NAME) 
                    VALUES (403, 5551122334, TO_DATE('2024-12-31', 'YYYY-MM-DD'), 'Driver', 3, TO_DATE('2020-03-10', 'YYYY-MM-DD'), 'Lee', 'Chris')",

                // Insert into TOUR
                "INSERT INTO TOUR (TOUR_ID, EMPLOYEE_ID, TOUR_TYPE, DESTINATION_ID, TOUR_LANGUAGE, TOUR_NAME) VALUES (501, 401, 'Historical', 101, 'English', 'Ancient Wonders')",
                "INSERT INTO TOUR (TOUR_ID, EMPLOYEE_ID, TOUR_TYPE, DESTINATION_ID, TOUR_LANGUAGE, TOUR_NAME) VALUES (502, 402, 'Adventure', 102, 'Spanish', 'Mountain Explorer')",
                "INSERT INTO TOUR (TOUR_ID, EMPLOYEE_ID, TOUR_TYPE, DESTINATION_ID, TOUR_LANGUAGE, TOUR_NAME) VALUES (503, 403, 'Cultural', 103, 'French', 'City Highlights')",

                // Insert into TRAVELER
                "INSERT INTO TRAVELER (TRAVELER_ID, FIRST_NAME, LAST_NAME, BOOKING_ID) VALUES (601, 'Alice', 'Johnson', 301)",
                "INSERT INTO TRAVELER (TRAVELER_ID, FIRST_NAME, LAST_NAME, BOOKING_ID) VALUES (602, 'Bob', 'Brown', 302)",
                "INSERT INTO TRAVELER (TRAVELER_ID, FIRST_NAME, LAST_NAME, BOOKING_ID) VALUES (603, 'Carol', 'Davis', 303)"
            ];

            // Display results in a table
            echo "<div class='table-responsive'>";
            echo "<table class='table table-bordered'>";
            echo "<thead><tr><th>Query</th><th>Status</th></tr></thead>";
            echo "<tbody>";

            // Execute each query
            foreach ($insertQueries as $query) {
                $stid = oci_parse($conn, $query);
                $r = @oci_execute($stid, OCI_COMMIT_ON_SUCCESS); // Suppress errors with '@'
                echo "<tr>";
                echo "<td>" . htmlspecialchars($query) . "</td>";
                if ($r) {
                    echo "<td class='text-success'>Success</td>";
                } else {
                    $error = oci_error($stid);
                    echo "<td class='text-danger'>Failed: " . htmlspecialchars($error['message']) . "</td>";
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
