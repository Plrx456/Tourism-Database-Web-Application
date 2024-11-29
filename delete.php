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

// Fetch rows for a given table
function fetchRows($conn, $tableName) {
    $rows = [];
    $query = "SELECT * FROM $tableName";
    $stid = oci_parse($conn, $query);
    oci_execute($stid);
    while ($row = oci_fetch_assoc($stid)) {
        $rows[] = $row;
    }
    oci_free_statement($stid);
    return $rows;
}

// Fetch primary key for a given table
function fetchPrimaryKey($conn, $tableName) {
    $query = "SELECT cols.column_name
              FROM all_constraints cons, all_cons_columns cols
              WHERE cols.table_name = '$tableName'
                AND cons.constraint_type = 'P'
                AND cons.constraint_name = cols.constraint_name
                AND cons.owner = cols.owner";
    $stid = oci_parse($conn, $query);
    oci_execute($stid);
    $primaryKey = oci_fetch_assoc($stid)['COLUMN_NAME'] ?? null;
    oci_free_statement($stid);
    return $primaryKey;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Delete Function</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Dynamic Delete</h1>
        <?php if (!$conn): ?>
            <div class="alert alert-danger text-center">
                Unable to connect to the database.
            </div>
        <?php else: ?>
            <div class="alert alert-success text-center">
                Connected to the Oracle database.
            </div>

            <!-- Select Table Form -->
            <form method="POST" class="mb-4">
                <label for="deleteTable" class="form-label">Select Table</label>
                <select name="deleteTable" id="deleteTable" class="form-control" onchange="this.form.submit()">
                    <option value="">Select Table</option>
                    <?php
                    // Fetch and display table names dynamically
                    $tables = fetchTables($conn);
                    foreach ($tables as $table) {
                        $selected = isset($_POST['deleteTable']) && $_POST['deleteTable'] === $table ? 'selected' : '';
                        echo "<option value='$table' $selected>$table</option>";
                    }
                    ?>
                </select>
            </form>

            <?php
            // Display rows for the selected table
            if (isset($_POST['deleteTable']) && $_POST['deleteTable'] !== '') {
                $table = $_POST['deleteTable'];
                $primaryKey = fetchPrimaryKey($conn, $table);
                $rows = fetchRows($conn, $table);

                if ($primaryKey && $rows): ?>
                    <form method="POST">
                        <input type="hidden" name="tableName" value="<?= htmlspecialchars($table) ?>">
                        <div class="mb-3">
                            <label for="deleteRow" class="form-label">Select Row to Delete</label>
                            <select name="deleteRow" id="deleteRow" class="form-control" required>
                                <option value="">Select Row</option>
                                <?php foreach ($rows as $row): ?>
                                    <?php $keyValue = htmlspecialchars($row[$primaryKey]); ?>
                                    <option value="<?= $keyValue ?>">
                                        <?= $primaryKey ?> = <?= $keyValue ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" name="deleteSubmit" class="btn btn-danger">Delete</button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-warning">No rows found or primary key missing for the selected table.</div>
                <?php endif;
            }

            // Handle Delete
            if (isset($_POST['deleteSubmit'])) {
                $table = $_POST['tableName'];
                $primaryKey = fetchPrimaryKey($conn, $table);
                $keyValue = $_POST['deleteRow'];

                // Build and execute DELETE query
                $query = "DELETE FROM $table WHERE $primaryKey = '$keyValue'";
                $stid = oci_parse($conn, $query);
                $r = @oci_execute($stid, OCI_COMMIT_ON_SUCCESS);
                if ($r) {
                    echo "<div class='alert alert-success'>Row deleted successfully from $table.</div>";
                } else {
                    $error = oci_error($stid);
                    echo "<div class='alert alert-danger'>Failed to delete row: " . htmlspecialchars($error['message']) . "</div>";
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
