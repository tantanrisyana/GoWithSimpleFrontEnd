<?php
// Start a session to persist messages across requests
session_start();

// Initialize the message variable
$message = '';

// Function to make a cURL request and retrieve data from the specified URL
function curl($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

// Retrieve data from the API endpoint
$curl = curl("http://localhost:8000/stocks");

// Convert JSON to array
$data = json_decode($curl, TRUE);

// Handle JSON decoding error
if ($data === null) {
    die('Error decoding JSON response');
}

// Pagination
$itemsPerPage = 10; // Number of items to display per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$totalItems = count($data);
$totalPages = ceil($totalItems / $itemsPerPage);
$startIndex = ($page - 1) * $itemsPerPage;
$dataToDisplay = array_slice($data, $startIndex, $itemsPerPage);

// Handle form submission for deleting data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["ID"])) {
    $ID = $_POST["ID"];

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://localhost:8000/stocks/' . $ID,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'DELETE',
    ));

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ($httpCode == 200) {
        $_SESSION['message'] = 'Data berhasil dihapus.';
    } else {
        $_SESSION['message'] = 'Penghapusan data gagal dengan kode HTTP: ' . $httpCode;
        $_SESSION['api_response'] = $response;
    }

    curl_close($curl);

    if ($httpCode == 204) {
        $message = 'Data dengan ID ' . $ID . ' berhasil dihapus.';
    } elseif ($httpCode == 404) {
        $message = 'Data dengan ID ' . $ID . ' tidak ditemukan. Tidak ada data yang dihapus.';
    } else {
        $message = 'Penghapusan gagal dengan kode HTTP: ' . $httpCode;
    }

    // Redirect back to the same page after processing the form
    header("Location: $_SERVER[PHP_SELF]");
    exit();
}
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
    <a class="navbar-brand" href="aboutme.php">About Me</a>
</nav>

<div class="container">
    <h1>Stock List</h1>
    <table class="table">
        <thead>
        <!-- Form for adding stock -->
        <form method="post" action="add_stock.php">
                       
                        <button type="submit" class="btn btn-primary btn-sm">Add Stock</button>
        </form>    
        <tr>
            <th>NO</th>
            <th>ACTION</th>
            <th>Tanggal</th>
            <th>Nama Barang</th>
            <th>Jumlah</th>
            <th>Keterangan</th>
        </tr>
        </thead>
        <tbody>
        <?php
        // Calculate the starting value for $no
        $no = ($page - 1) * $itemsPerPage + 1;

        // Loop through the data to display
        foreach ($dataToDisplay as $row) {
        ?>
            <tr>
                <td><?php echo $no; ?></td>
                <td>
                    <!-- Form for deleting data -->
                    <form method="post" action="">
                        <input type="hidden" name="ID" value="<?php echo $row["ID"]; ?>">
                        <button type="submit" class="btn btn-primary btn-sm">Delete</button>
                    </form>
                    
                    
                </td>
                <td><?php echo $row["Tanggal"]; ?></td>
                <td><?php echo $row["NamaBarang"]; ?></td>
                <td><?php echo $row["Jumlah"]; ?></td>
                <td><?php echo $row["Keterangan"]; ?></td>
            </tr>
        <?php
            $no++; // Increment $no
        } ?>
        </tbody>
    </table>

    <!-- Pagination Links -->
    <ul class="pagination">
        <?php
        // Generate pagination links
        for ($i = 1; $i <= $totalPages; $i++) {
        ?>
            <li class="page-item <?php if ($i === $page) echo 'active'; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?><?php if (!empty($_GET['ID'])) echo '&ID=' . $_GET['ID']; ?>"><?php echo $i; ?></a>
            </li>
        <?php } ?>
    </ul>
</div>

<script src="static/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
