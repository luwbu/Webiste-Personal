<?php
include 'koneksi.php';

$keyword = $_POST['keyword'];

$data = $koneksi->query("SELECT p.*, j.nama_jabatan, b.nama_bagian 
                         FROM pegawai p 
                         LEFT JOIN jabatan j ON p.id_jabatan = j.id_jabatan 
                         LEFT JOIN bagian b ON p.id_bagian = b.id_bagian
                         WHERE p.nama LIKE '%$keyword%' OR p.nip LIKE '%$keyword%'");

$no = 1;
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
