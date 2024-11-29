<?php
// Suppress warnings to avoid showing them on the page
error_reporting(E_ERROR | E_PARSE);

// Establish a connection to the Oracle database
$conn = oci_connect(
    's5pradha',
    '06105193',
    '(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=oracle.scs.ryerson.ca)(PORT=1521))(CONNECT_DATA=(SID=orcl)))'
);

// Fetch table names for the dropdown
function fetchTables($conn) {
    $tables = [];
    $query = "SELECT table_name FROM all_tables WHERE owner = 'S5PRADHA'";
    $stid = oci_parse($conn, $query);
    oci_execute($stid);
    while ($row = oci_fetch_assoc($stid)) {
        $tables[] = $row['TABLE_NAME'];
    }
    oci_free_statement($stid);
    return $tables;
}

// Fetch column names for a table
function fetchColumns($conn, $table) {
    $columns = [];
    $query = "SELECT column_name FROM all_tab_columns WHERE table_name = UPPER('$table')";
    $stid = oci_parse($conn, $query);
    oci_execute($stid);
    while ($row = oci_fetch_assoc($stid)) {
        $columns[] = $row['COLUMN_NAME'];
    }
    oci_free_statement($stid);
    return $columns;
}

// Fetch record values for the first column
function fetchRecordValues($conn, $table, $column) {
    $records = [];
    $query = "SELECT $column FROM $table";
    $stid = oci_parse($conn, $query);
    oci_execute($stid);
    while ($row = oci_fetch_assoc($stid)) {
        $records[] = $row[$column];
    }
    oci_free_statement($stid);
    return $records;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Database Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Manage Database Records</h1>
        <?php if (!$conn): ?>
            <div class="alert alert-danger text-center">
                Unable to connect to the database.
            </div>
        <?php else: ?>
            <div class="alert alert-success text-center">
                Connected to the Oracle database.
            </div>
            <div class="row">
                <!-- Update Form -->
                <div class="col-md-6">
                    <h3>Update Row</h3>
                    <form method="POST">
                        <div class="mb-3">
                            <label for="updateTable" class="form-label">Table Name</label>
                            <select name="updateTable" class="form-control" onchange="this.form.submit()">
                                <option value="">Select Table</option>
                                <?php
                                // Fetch and display table names dynamically
                                $tables = fetchTables($conn);
                                foreach ($tables as $table) {
                                    $selected = (isset($_POST['updateTable']) && $_POST['updateTable'] === $table) ? 'selected' : '';
                                    echo "<option value='$table' $selected>$table</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <?php
                        // Populate the record dropdown for updating
                        if (isset($_POST['updateTable']) && $_POST['updateTable'] != '') {
                            $updateTable = $_POST['updateTable'];
                            $columns = fetchColumns($conn, $updateTable);

                            if (!empty($columns)) {
                                $primaryColumn = $columns[0]; // Assume the first column as the primary key
                                $records = fetchRecordValues($conn, $updateTable, $primaryColumn);

                                echo '<div class="mb-3">';
                                echo '<label for="updateRecord" class="form-label">Select Record to Update</label>';
                                echo '<select name="updateRecord" class="form-control" required>';
                                foreach ($records as $record) {
                                    echo "<option value='$record'>$record</option>";
                                }
                                echo '</select>';
                                echo '</div>';
                            } else {
                                echo '<div class="alert alert-warning">No columns found for the selected table.</div>';
                            }
                        }
                        ?>
                        <div class="mb-3">
                            <label for="updateValues" class="form-label">Set Values (e.g., col1='val1', col2='val2')</label>
                            <input type="text" name="updateValues" class="form-control" required>
                        </div>
                        <button type="submit" name="updateSubmit" class="btn btn-warning">Update</button>
                    </form>
                </div>
            </div>
            <?php
            // Handle Update
            if (isset($_POST['updateSubmit'])) {
                $table = $_POST['updateTable'];
                $recordId = $_POST['updateRecord'];
                $setValues = $_POST['updateValues'];
                $primaryColumn = fetchColumns($conn, $table)[0]; // Use the first column dynamically
                $query = "UPDATE $table SET $setValues WHERE $primaryColumn = '$recordId'";
                $stid = oci_parse($conn, $query);
                $r = @oci_execute($stid, OCI_COMMIT_ON_SUCCESS);
                if ($r) {
                    echo "<div class='alert alert-success'>Row(s) updated successfully in $table.</div>";
                } else {
                    echo "<div class='alert alert-danger'>Failed to update row(s): " . htmlspecialchars($query) . "</div>";
                }
                oci_free_statement($stid);
            }

            // Close connection
            oci_close($conn);
            ?>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
