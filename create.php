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
    <title>Create Tables</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Table Creation Results</h1>
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
            // Array of CREATE TABLE queries
            $createQueries = [
                "CREATE TABLE COMPANY (
                    COMPANY_ID NUMBER NOT NULL ENABLE, 
                    ADDRESS VARCHAR2(50 BYTE) NOT NULL ENABLE, 
                    PHONE_NUMBER NUMBER NOT NULL ENABLE, 
                    COMPANY_NAME VARCHAR2(50 BYTE) NOT NULL ENABLE, 
                    CONSTRAINT COMPANY_PK PRIMARY KEY (COMPANY_ID)
                )",
                "CREATE TABLE DESTINATION (
                    DESTINATION_ID NUMBER NOT NULL ENABLE, 
                    POI_ID NUMBER NOT NULL ENABLE, 
                    CONSTRAINT DESTINATION_PK PRIMARY KEY (DESTINATION_ID)
                )",
                "CREATE TABLE TRAVEL_PACKAGE (
                    DESTINATION_ID NUMBER NOT NULL ENABLE, 
                    TRAVEL_PACKAGE_ID NUMBER NOT NULL ENABLE, 
                    PACKAGE_PRICE NUMBER NOT NULL ENABLE, 
                    CONSTRAINT TRAVEL_PACKAGE_PK PRIMARY KEY (TRAVEL_PACKAGE_ID),
                    CONSTRAINT TRAVEL_PACKAGE_FK2 FOREIGN KEY (DESTINATION_ID) REFERENCES DESTINATION (DESTINATION_ID) ENABLE
                )",
                "CREATE TABLE BOOKING (
                    BOOKING_ID NUMBER NOT NULL ENABLE, 
                    DEPOSIT_AMOUNT NUMBER NOT NULL ENABLE, 
                    BOOKING_DATE DATE NOT NULL ENABLE, 
                    COMPANY_ID NUMBER NOT NULL ENABLE, 
                    TRAVEL_PACKAGE_ID NUMBER NOT NULL ENABLE,
                    CONSTRAINT BOOKING_PK PRIMARY KEY (BOOKING_ID),
                    CONSTRAINT BOOKING_FK1 FOREIGN KEY (COMPANY_ID) REFERENCES COMPANY (COMPANY_ID) ENABLE,
                    CONSTRAINT BOOKING_FK2 FOREIGN KEY (TRAVEL_PACKAGE_ID) REFERENCES TRAVEL_PACKAGE (TRAVEL_PACKAGE_ID) ENABLE
                )",
                "CREATE TABLE EMPLOYEE (
                    EMPLOYEE_ID NUMBER NOT NULL ENABLE, 
                    PHONE_NUMBER NUMBER NOT NULL ENABLE, 
                    LEAVE_DATE DATE NOT NULL ENABLE, 
                    ROLE VARCHAR2(50 BYTE) NOT NULL ENABLE, 
                    COMPANY_ID NUMBER NOT NULL ENABLE, 
                    HIRE_DATE DATE NOT NULL ENABLE, 
                    LAST_NAME VARCHAR2(50 BYTE) NOT NULL ENABLE, 
                    FIRST_NAME VARCHAR2(50 BYTE) NOT NULL ENABLE, 
                    CONSTRAINT EMPLOYEE_PK PRIMARY KEY (EMPLOYEE_ID),
                    CONSTRAINT FK_COMPANYID_EMPLOYEE FOREIGN KEY (COMPANY_ID) REFERENCES COMPANY (COMPANY_ID) ENABLE
                )",
                "CREATE TABLE TOUR (
                    TOUR_ID NUMBER NOT NULL ENABLE, 
                    EMPLOYEE_ID NUMBER NOT NULL ENABLE, 
                    TOUR_TYPE VARCHAR2(50 BYTE) NOT NULL ENABLE, 
                    DESTINATION_ID NUMBER NOT NULL ENABLE, 
                    TOUR_LANGUAGE VARCHAR2(50 BYTE) NOT NULL ENABLE, 
                    TOUR_NAME VARCHAR2(50 BYTE) NOT NULL ENABLE, 
                    CONSTRAINT TOUR_PK PRIMARY KEY (TOUR_ID),
                    CONSTRAINT TOUR_FK1 FOREIGN KEY (EMPLOYEE_ID) REFERENCES EMPLOYEE (EMPLOYEE_ID) ENABLE,
                    CONSTRAINT TOUR_FK2 FOREIGN KEY (DESTINATION_ID) REFERENCES DESTINATION (DESTINATION_ID) ENABLE
                )",
                "CREATE TABLE TRAVELER (
                    TRAVELER_ID NUMBER NOT NULL ENABLE, 
                    FIRST_NAME VARCHAR2(50 BYTE) NOT NULL ENABLE, 
                    LAST_NAME VARCHAR2(50 BYTE) NOT NULL ENABLE, 
                    BOOKING_ID NUMBER NOT NULL ENABLE, 
                    CONSTRAINT TRAVELER_PK PRIMARY KEY (TRAVELER_ID),
                    CONSTRAINT TRAVELER_FK1 FOREIGN KEY (BOOKING_ID) REFERENCES BOOKING (BOOKING_ID) ENABLE
                )"
            ];

            // Display results in a table
            echo "<div class='table-responsive'>";
            echo "<table class='table table-bordered'>";
            echo "<thead><tr><th>Query</th><th>Status</th></tr></thead>";
            echo "<tbody>";

            // Execute each query
            foreach ($createQueries as $query) {
                $stid = oci_parse($conn, $query);
                $r = @oci_execute($stid, OCI_COMMIT_ON_SUCCESS); // Suppress errors with '@'
                echo "<tr>";
                echo "<td>" . htmlspecialchars($query) . "</td>";
                if ($r) {
                    echo "<td class='text-success'>Success</td>";
                } else {
                    $e = oci_error($stid); // Capture error details
                    echo "<td class='text-danger'>" . htmlspecialchars($e['message']) . "</td>";
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
