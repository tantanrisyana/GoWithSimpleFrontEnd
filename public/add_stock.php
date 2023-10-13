<?php

// Start the session
session_start();

// Initialize variables
$successMessage = '';
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize data from the form
    $tanggal = isset($_POST["Tanggal"]) ? date("c", strtotime($_POST["Tanggal"])) : null;
    $namaBarang = isset($_POST["NamaBarang"]) ? $_POST["NamaBarang"] : null;
    $jumlah = isset($_POST["Jumlah"]) ? intval($_POST["Jumlah"]) : null;
    $keterangan = isset($_POST["Keterangan"]) ? $_POST["Keterangan"] : null;

    // Validate Jumlah as an integer
    if (!is_numeric($jumlah) || floor($jumlah) != $jumlah) {
        $errors['Jumlah'] = 'Jumlah harus berupa angka bulat.';
    }

    // Check for empty fields
    if (empty($tanggal)) {
        $errors['Tanggal'] = 'Tanggal harus diisi.';
    }

    if (empty($namaBarang)) {
        $errors['NamaBarang'] = 'Nama Barang harus diisi.';
    }

    if (empty($keterangan)) {
        $errors['Keterangan'] = 'Keterangan harus diisi.';
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
            $successMessage = 'Data berhasil ditambahkan.';
        } else {
            $errors[] = 'Penambahan data gagal dengan kode HTTP: ' . $httpCode;
        }
    }
}
?>
<!DOCTYPE html>
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
                    <?php if (!empty($errors['Tanggal']) && $_SERVER["REQUEST_METHOD"] == "POST"): ?>
                        <div class="text-danger"><?php echo $errors['Tanggal']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="NamaBarang">Nama Barang:</label>
                    <input type="text" class="form-control" id="NamaBarang" name="NamaBarang" required>
                    <?php if (!empty($errors['NamaBarang']) && $_SERVER["REQUEST_METHOD"] == "POST"): ?>
                        <div class="text-danger"><?php echo $errors['NamaBarang']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="Jumlah">Jumlah:</label>
                    <input type="text" class="form-control" id="Jumlah" name="Jumlah" required>
                    <?php if (!empty($errors['Jumlah']) && $_SERVER["REQUEST_METHOD"] == "POST"): ?>
                        <div class="text-danger"><?php echo $errors['Jumlah']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="Keterangan">Keterangan:</label>
                    <input type="text" class="form-control" id="Keterangan" name="Keterangan" required>
                    <?php if (!empty($errors['Keterangan']) && $_SERVER["REQUEST_METHOD"] == "POST"): ?>
                        <div class="text-danger"><?php echo $errors['Keterangan']; ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>

        <div class="col-md-6">
            <!-- Display success message -->
            <?php if (!empty($successMessage) && $_SERVER["REQUEST_METHOD"] == "POST"): ?>
                <div class="alert alert-success mt-3"><?php echo $successMessage; ?></div>
            <?php endif; ?>

            <!-- Display validation errors, if any -->
            <?php if (!empty($errors) && $_SERVER["REQUEST_METHOD"] == "POST"): ?>
                <div class="alert alert-danger mt-3">
                    <?php foreach ($errors as $errorKey => $errorMessage): ?>
                        <?php echo $errorMessage . '<br>'; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="static/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
