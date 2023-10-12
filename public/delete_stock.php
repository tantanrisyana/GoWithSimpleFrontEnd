<?php
// Start the session
session_start();

$message = '';

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
}
?>

<html>

<head>
    <!-- Add Bootstrap CSS and JS links here -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="static/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="static/bootstrap/css/style.css"> <!-- You might need to adjust the path -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="static/bootstrap/js/bootstrap.min.js"></script>
    <title>Delete Stock</title>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="index.php">Home</a>
    <a class="navbar-brand" href="add_stock.php">Tambah Stock</a>
    <a class="navbar-brand" href="delete_stock.php">Hapus Stock</a>
</nav>

<div class="container">
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <label for="ID">ID : </label>
        <input type="text" id="ID" name="ID"><br><br>
        <input type="submit" class="btn btn-primary" value="Submit">
    </form>
</div>

<?php
if (!empty($message)) {
    echo '<script type="text/javascript">
            $(document).ready(function(){
                $("#myModal").modal("show");
            });
        </script>';
}
?>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Delete Result</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php echo $message; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



</body>

</html>
