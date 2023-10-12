<?php

// Start the session
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Initialize an array to store validation errors
    $errors = [];

    // Validate and sanitize data from the form
    $tanggal = date("c", strtotime($_POST["Tanggal"]));
    $namaBarang = $_POST["NamaBarang"];
    $jumlah = intval($_POST["Jumlah"]);
    $keterangan = $_POST["Keterangan"];

    // Validate Jumlah as an integer
    if (!is_numeric($jumlah) || floor($jumlah) != $jumlah) {
        $errors[] = 'Jumlah harus berupa angka bulat.';
    }

    // Check for empty fields
    if (empty($tanggal) || empty($namaBarang) || empty($keterangan)) {
        $errors[] = 'Semua field harus diisi.';
    }

    if (empty($errors)) {
        // If there are no validation errors, proceed with the API call
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://localhost:8000/stocks',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            // Kirim data sebagai payload
            CURLOPT_POSTFIELDS => json_encode([
                'Tanggal' => $tanggal,
                'NamaBarang' => $namaBarang,
                'Jumlah' => $jumlah,
                'Keterangan' => $keterangan,
            ]),
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'), // Set content type to JSON
        ));

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        // Decode the JSON response for easier handling
        $apiResponse = json_decode($response, true);

        if ($httpCode == 201) {
            $_SESSION['message'] = 'Data berhasil ditambahkan.';
        } else {
            $_SESSION['message'] = 'Penambahan data gagal dengan kode HTTP: ' . $httpCode;
            $_SESSION['api_response'] = $apiResponse;
        }

        // Redirect to avoid form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        // If there are validation errors, store them in the session
        $_SESSION['errors'] = $errors;
    }
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="static/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="static/bootstrap/css/style.css"> <!-- You might need to adjust the path -->
    <title>Add Stock</title>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="index.php">Home</a>
    <a class="navbar-brand" href="add_stock.php">Tambah Stock</a>
    <a class="navbar-brand" href="delete_stock.php">Hapus Stock</a>
</nav>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-6">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <div class="form-group">
                    <label for="Tanggal">Tanggal:</label>
                    <input type="date" class="form-control" id="Tanggal" name="Tanggal" required>
                </div>

                <div class="form-group">
                    <label for="NamaBarang">Nama Barang:</label>
                    <input type="text" class="form-control" id="NamaBarang" name="NamaBarang" required>
                </div>

                <div class="form-group">
                    <label for="Jumlah">Jumlah:</label>
                    <input type="text" class="form-control" id="Jumlah" name="Jumlah" required>
                </div>

                <div class="form-group">
                    <label for="Keterangan">Keterangan:</label>
                    <input type="text" class="form-control" id="Keterangan" name="Keterangan" required>
                </div>

                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>

        <div class="col-md-6">
            <!-- Display success message -->
            <?php
            if (isset($_SESSION['message'])) {
                echo '<div class="alert alert-success mt-3">' . $_SESSION['message'] . '</div>';

                // Clear the session variables
                unset($_SESSION['message']);
                unset($_SESSION['api_response']);
            }

            // Display validation errors, if any
            if (isset($_SESSION['errors'])) {
                echo '<div class="alert alert-danger mt-3">';
                foreach ($_SESSION['errors'] as $error) {
                    echo $error . '<br>';
                }
                echo '</div>';

                // Clear the session variable
                unset($_SESSION['errors']);
            }
            ?>
        </div>
    </div>
</div>

<script src="static/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>