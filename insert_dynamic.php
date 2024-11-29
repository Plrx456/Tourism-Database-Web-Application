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

// Fetch columns for a given table
function fetchColumns($conn, $tableName) {
    $columns = [];
    $query = "SELECT column_name, data_type FROM all_tab_columns WHERE table_name = '$tableName'";
    $stid = oci_parse($conn, $query);
    oci_execute($stid);
    while ($row = oci_fetch_assoc($stid)) {
        $columns[] = $row;
    }
    oci_free_statement($stid);
    return $columns;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Insert Function</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Dynamic Insert</h1>
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
                <label for="insertTable" class="form-label">Select Table</label>
                <select name="insertTable" id="insertTable" class="form-control" onchange="this.form.submit()">
                    <option value="">Select Table</option>
                    <?php
                    // Fetch and display table names dynamically
                    $tables = fetchTables($conn);
                    foreach ($tables as $table) {
                        $selected = isset($_POST['insertTable']) && $_POST['insertTable'] === $table ? 'selected' : '';
                        echo "<option value='$table' $selected>$table</option>";
                    }
                    ?>
                </select>
            </form>

            <?php
            // Display column input fields if a table is selected
            if (isset($_POST['insertTable']) && $_POST['insertTable'] !== '') {
                $table = $_POST['insertTable'];
                $columns = fetchColumns($conn, $table);
                if ($columns): ?>
                    <form method="POST">
                        <input type="hidden" name="tableName" value="<?= htmlspecialchars($table) ?>">
                        <?php foreach ($columns as $column): ?>
                            <div class="mb-3">
                                <label for="<?= $column['COLUMN_NAME'] ?>" class="form-label">
                                    <?= htmlspecialchars($column['COLUMN_NAME']) ?> (<?= htmlspecialchars($column['DATA_TYPE']) ?>)
                                </label>
                                <input type="text" name="columnValues[<?= $column['COLUMN_NAME'] ?>]" id="<?= $column['COLUMN_NAME'] ?>" class="form-control" required>
                            </div>
                        <?php endforeach; ?>
                        <button type="submit" name="insertSubmit" class="btn btn-primary">Insert</button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-warning">No columns found for the selected table.</div>
                <?php endif;
            }

            // Handle Insert
            if (isset($_POST['insertSubmit'])) {
                $table = $_POST['tableName'];
                $columnValues = $_POST['columnValues'];

                // Build Insert Query
                $columns = implode(', ', array_keys($columnValues));
                $values = implode(", ", array_map(function ($value) {
                    return "'" . htmlspecialchars($value, ENT_QUOTES) . "'";
                }, $columnValues));

                $query = "INSERT INTO $table ($columns) VALUES ($values)";
                $stid = oci_parse($conn, $query);
                $r = @oci_execute($stid, OCI_COMMIT_ON_SUCCESS);
                if ($r) {
                    echo "<div class='alert alert-success'>Row inserted successfully into $table.</div>";
                } else {
                    $error = oci_error($stid);
                    echo "<div class='alert alert-danger'>Failed to insert row: " . htmlspecialchars($error['message']) . "</div>";
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
