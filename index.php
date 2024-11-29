<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Operations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #fefefe;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        h1 {
            color: #343a40;
            font-family: 'Arial', sans-serif;
        }
        .list-group-item {
            background-color: #f8f9fa;
            color: #495057;
            font-size: 18px;
            font-weight: bold;
            border: none;
            transition: transform 0.2s ease, background-color 0.2s ease;
        }
        .list-group-item:hover {
            background-color: #007bff;
            color: #ffffff;
            transform: scale(1.05);
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.5);
        }
        .list-group-item:active {
            background-color: #0056b3;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Tourism Agency Booking System</h1>
        <p class="text-center text-secondary">Select an Operation:</p>
        <div class="d-flex justify-content-center">
            <div class="list-group w-75">
                <a href="create.php" class="list-group-item list-group-item-action text-center">
                    <strong>1.</strong> Create Tables
                </a>
                <a href="insert.php" class="list-group-item list-group-item-action text-center">
                    <strong>2.</strong> Populate Tables
                </a>
                <a href="insert_dynamic.php" class="list-group-item list-group-item-action text-center">
                    <strong>3.</strong> Insert Custom Value to Tables
                </a>
                <a href="view.php" class="list-group-item list-group-item-action text-center">
                    <strong>4.</strong> View All Data Tables
                </a>
                <a href="query.php" class="list-group-item list-group-item-action text-center">
                    <strong>5.</strong> Query Tables
                </a>
                <a href="edit.php" class="list-group-item list-group-item-action text-center">
                    <strong>6.</strong> Edit Data in Tables
                </a>
                <a href="delete.php" class="list-group-item list-group-item-action text-center">
                    <strong>7.</strong> Delete Row from Table
                </a>
                <a href="drop.php" class="list-group-item list-group-item-action text-center">
                    <strong>8.</strong> Drop Tables
                </a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
