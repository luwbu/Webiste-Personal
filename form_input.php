<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Pegawai</title>
    <!-- JQUERY -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables CSS & JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
</head>
<body>

<h2>Form Pegawai</h2>

<!-- Tanggal hari ini -->
<p id="tanggal"></p>

<?php
// Proses Edit
$editMode = false;
if (isset($_GET['id'])) {
    $editMode = true;
    $id = $_GET['id'];
    $data = $koneksi->query("SELECT * FROM pegawai WHERE id_pegawai = '$id'");
    $row = $data->fetch_assoc();
}
?>

<form method="POST">
    <input type="hidden" name="id_pegawai" value="<?= $editMode ? $row['id_pegawai'] : '' ?>">
    <input type="text" name="nip" placeholder="NIP" value="<?= $editMode ? $row['nip'] : '' ?>" required><br>
    <input type="text" name="nama" placeholder="Nama" value="<?= $editMode ? $row['nama'] : '' ?>" required><br>

    <select name="id_jabatan" required>
        <option value="">-- Pilih Jabatan --</option>
        <?php
        $jabatan = $koneksi->query("SELECT * FROM jabatan");
        while ($j = $jabatan->fetch_assoc()) {
            $selected = ($editMode && $j['id_jabatan'] == $row['id_jabatan']) ? "selected" : "";
            echo "<option value='$j[id_jabatan]' $selected>$j[nama_jabatan]</option>";
        }
        ?>
    </select><br>

    <select name="id_bagian" required>
        <option value="">-- Pilih Bagian --</option>
        <?php
        $bagian = $koneksi->query("SELECT * FROM bagian");
        while ($b = $bagian->fetch_assoc()) {
            $selected = ($editMode && $b['id_bagian'] == $row['id_bagian']) ? "selected" : "";
            echo "<option value='$b[id_bagian]' $selected>$b[nama_bagian]</option>";
        }
        ?>
    </select><br>

    <input type="date" name="tanggal_masuk" value="<?= $editMode ? $row['tanggal_masuk'] : '' ?>" required><br>

    <?php if ($editMode): ?>
        <button type="submit" name="update">Update</button>
        <a href="form_input.php">Batal</a>
    <?php else: ?>
        <button type="submit" name="simpan">Simpan</button>
    <?php endif; ?>
</form>

<?php
// Simpan
if (isset($_POST['simpan'])) {
    $nip = $_POST['nip'];
    $nama = $_POST['nama'];
    $id_jabatan = $_POST['id_jabatan'];
    $id_bagian = $_POST['id_bagian'];
    $tanggal_masuk = $_POST['tanggal_masuk'];

    $simpan = $koneksi->query("INSERT INTO pegawai (nip, nama, id_jabatan, id_bagian, tanggal_masuk)
                                VALUES ('$nip', '$nama', '$id_jabatan', '$id_bagian', '$tanggal_masuk')");

    echo $simpan ? "Data berhasil disimpan!<meta http-equiv='refresh' content='1'>" : "Gagal menyimpan data.";
}

// Update
if (isset($_POST['update'])) {
    $id = $_POST['id_pegawai'];
    $nip = $_POST['nip'];
    $nama = $_POST['nama'];
    $id_jabatan = $_POST['id_jabatan'];
    $id_bagian = $_POST['id_bagian'];
    $tanggal_masuk = $_POST['tanggal_masuk'];

    $update = $koneksi->query("UPDATE pegawai SET 
        nip = '$nip',
        nama = '$nama',
        id_jabatan = '$id_jabatan',
        id_bagian = '$id_bagian',
        tanggal_masuk = '$tanggal_masuk'
        WHERE id_pegawai = '$id'
    ");

    echo $update ? "Data berhasil diupdate!<meta http-equiv='refresh' content='1;url=form_input.php'>" : "Gagal update.";
}

// Hapus
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $hapus = $koneksi->query("DELETE FROM pegawai WHERE id_pegawai = '$id'");
    echo $hapus ? "Data berhasil dihapus!<meta http-equiv='refresh' content='1;url=form_input.php'>" : "Gagal hapus data.";
}
?>

<!-- Pencarian AJAX -->
<h2>Cari Pegawai (AJAX)</h2>
<input type="text" id="keyword" placeholder="Cari nama atau NIP">
<button id="cariBtn">Cari</button>
<button id="refreshBtn">Refresh</button>

<h2>Data Pegawai</h2>
<table id="pegawaiTable" class="display">
    <thead>
    <tr>
        <th>No</th>
        <th>NIP</th>
        <th>Nama</th>
        <th>Jabatan</th>
        <th>Bagian</th>
        <th>Tanggal Masuk</th>
        <th>Aksi</th>
    </tr>
    </thead>
    <tbody id="dataPegawai">
    <?php
    $no = 1;
    $data = $koneksi->query("SELECT p.*, j.nama_jabatan, b.nama_bagian 
                             FROM pegawai p 
                             LEFT JOIN jabatan j ON p.id_jabatan = j.id_jabatan 
                             LEFT JOIN bagian b ON p.id_bagian = b.id_bagian");
    while ($row = $data->fetch_assoc()) {
        echo "<tr>
                <td>$no</td>
                <td>$row[nip]</td>
                <td>$row[nama]</td>
                <td>$row[nama_jabatan]</td>
                <td>$row[nama_bagian]</td>
                <td>$row[tanggal_masuk]</td>
                <td>
                    <a href='form_input.php?id=$row[id_pegawai]'>Edit</a> |
                    <a href='form_input.php?hapus=$row[id_pegawai]' onclick=\"return confirm('Yakin hapus?')\">Hapus</a>
                </td>
              </tr>";
        $no++;
    }
    ?>
    </tbody>
</table>

<h4>Total Pegawai: <?= $data->num_rows ?></h4>

<script>
$(document).ready(function(){

    // Tampilkan tanggal hari ini
    var today = new Date();
    var tgl = today.getDate() + '-' + (today.getMonth()+1) + '-' + today.getFullYear();
    $('#tanggal').html('Tanggal hari ini: ' + tgl);

    // Inisialisasi DataTables
    $('#pegawaiTable').DataTable();

    // Tombol Cari dengan AJAX
    $('#cariBtn').click(function(){
        var keyword = $('#keyword').val();
        $.ajax({
            url: 'cari_pegawai.php',
            type: 'POST',
            data: {keyword: keyword},
            success: function(data){
                $('#dataPegawai').html(data);
            },
            error: function(){
                alert('Terjadi kesalahan saat pencarian.');
            }
        });
    });

    // Tombol Refresh
    $('#refreshBtn').click(function(){
        location.reload(); // reload halaman untuk reset data
    });

});
</script>

</body>
</html>
