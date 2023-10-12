<?php
function curl($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

$curl = curl("http://localhost:8000/stocks");

// Convert JSON to array
$data = json_decode($curl, TRUE);

// Pagination
$itemsPerPage = 10; // Number of items to display per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$totalItems = count($data);
$totalPages = ceil($totalItems / $itemsPerPage);
$startIndex = ($page - 1) * $itemsPerPage;
$dataToDisplay = array_slice($data, $startIndex, $itemsPerPage);
?>

<html>
<head>
    <!-- Add Bootstrap CSS and JS links here -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="static/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="static/bootstrap/css/style.css"> <!-- You might need to adjust the path -->
    <title>Stock List</title>
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="index.php">Home</a>
    <a class="navbar-brand" href="add_stock.php">Tambah Stock</a>
    <a class="navbar-brand" href="delete_stock.php">Hapus Stock</a>
</nav>

<div class="container">
    <h1>Stock List</h1>
    <table class="table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Tanggal</th>
            <th>Nama Barang</th>
            <th>Jumlah</th>
            <th>Keterangan</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($dataToDisplay as $row) { ?>
            <tr>
                <td><?php echo $row["ID"]; ?></td>
                <td><?php echo $row["Tanggal"]; ?></td>
                <td><?php echo $row["NamaBarang"]; ?></td>
                <td><?php echo $row["Jumlah"]; ?></td>
                <td><?php echo $row["Keterangan"]; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <!-- Pagination Links -->
    <ul class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
            <li class="page-item <?php if ($i === $page) echo 'active'; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php } ?>
    </ul>
</div>

<script src="static/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
