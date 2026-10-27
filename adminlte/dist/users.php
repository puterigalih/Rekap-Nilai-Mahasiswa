<?php
// koneksi ke database
require_once __DIR__ . "/../../koneksi.php";


$pesan = isset($_GET['pesan']) ? $_GET['pesan'] : '';


// menerima pencarian dan filter
$cariNama = isset($_GET['cari']) ? trim($_GET['cari']) : '';
$predikat = isset($_GET['predikat']) ? trim($_GET['predikat']) : '';

// menerima pengurutan nilai
$urut = isset($_GET['urut']) ? $_GET['urut'] : 'DESC';
$urut = ($urut == 'ASC') ? 'ASC' : 'DESC';

// menerima nomor halaman
$halaman = isset($_GET['halaman']) ? (int) $_GET['halaman'] : 1;
if ($halaman < 1) {
  $halaman = 1;
}

$dataPerHalaman = isset($_GET['jumlah']) ? (int) $_GET['jumlah'] : 5;

if (!in_array($dataPerHalaman, [5, 10, 15])) {
  $dataPerHalaman = 5;
}

// kondisi pencarian
$where = [];

if ($cariNama != '') {
  $cariSQL = mysqli_real_escape_string($conn, $cariNama);
  $where[] = "namalengkap LIKE '%$cariSQL%'";
}

if ($predikat != '') {
  $predikatSQL = mysqli_real_escape_string($conn, $predikat);
  $where[] = "predikat = '$predikatSQL'";
}

$whereSQL = '';
if (count($where) > 0) {
  $whereSQL = 'WHERE ' . implode(' AND ', $where);
}

// menghitung jumlah data sesuai pencarian
$sqlJumlah = "SELECT COUNT(*) AS total
              FROM nilaimhsw
              $whereSQL";

$resultJumlah = mysqli_query($conn, $sqlJumlah);

if (!$resultJumlah) {
  die("Query jumlah data gagal: " . mysqli_error($conn));
}

$totalData = (int) mysqli_fetch_assoc($resultJumlah)['total'];
$totalHalaman = (int) ceil($totalData / $dataPerHalaman);

// memastikan halaman yang diminta tersedia
if ($totalHalaman > 0 && $halaman > $totalHalaman) {
  $halaman = $totalHalaman;
}

$offset = ($halaman - 1) * $dataPerHalaman;

// mengambil data berdasarkan pencarian, urutan, dan halaman
$sql = "SELECT nim, namalengkap, nilaiakhir, nilaihuruf, predikat, created_at
        FROM nilaimhsw
        $whereSQL
        ORDER BY nilaiakhir $urut
        LIMIT $dataPerHalaman OFFSET $offset";

$result = mysqli_query($conn, $sql);

if (!$result) {
  die("Query gagal: " . mysqli_error($conn));
}
?>

<!doctype html>
<html lang="en">
<!--begin::Head-->

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Rekap Nilai Mahasiswa</title>

  <!--begin::Theme Init (prevents flash of incorrect theme on load, #6043)-->
  <script>
    (() => {
      'use strict';
      const root = document.documentElement;

      // Applications with their own theming opt out of AdminLTE's color mode
      // entirely, here as well as in the bundle.
      if (root.getAttribute('data-lte-color-mode') === 'off') {
        return;
      }

      const STORAGE_KEY = 'lte-theme';
      let stored = null;
      try {
        stored = localStorage.getItem(STORAGE_KEY);
      } catch {
        // localStorage may be unavailable (private mode, sandboxed iframe).
      }
      // Mirror the precedence in color-mode.ts: the visitor's stored choice
      // wins, then a theme this page declared itself, then the OS preference.
      const authored = root.getAttribute('data-bs-theme');
      let resolved = 'light';
      if (stored === 'dark' || stored === 'light') {
        resolved = stored;
      } else if (authored === 'dark' || authored === 'light') {
        resolved = authored;
      } else if (globalThis.matchMedia('(prefers-color-scheme: dark)').matches) {
        resolved = 'dark';
      }
      root.setAttribute('data-bs-theme', resolved);
      root.style.colorScheme = resolved;
      // Flag values computed here, so the bundle does not mistake them for a
      // theme the page declared and stop following the OS preference.
      if (resolved !== authored) {
        root.setAttribute('data-lte-theme-resolved', '');
      }
    })();
  </script>
  <!--end::Theme Init-->

  <!--begin::Accessibility Meta Tags-->
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
  <meta name="color-scheme" content="light dark" />
  <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
  <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
  <!--end::Accessibility Meta Tags-->

  <!--begin::Primary Meta Tags-->
  <meta name="title" content="Rekap Nilai Mahasiswa" />
  <meta name="author" content="Mahasiswa Teknik Informatika" />

  <meta
    name="description"
    content="Halaman rekap nilai mahasiswa untuk mengelola dan melihat data nilai akhir, nilai huruf, dan predikat mahasiswa." />
  <meta
    name="keywords"
    content="rekap nilai mahasiswa, data mahasiswa, nilai akhir, nilai huruf, predikat mahasiswa" />
  <!--end::Primary Meta Tags-->

  <!--begin::Accessibility Features-->
  <!-- Skip links will be dynamically added by accessibility.js -->
  <meta name="supported-color-schemes" content="light dark" />
  <link rel="preload" href="./css/adminlte.css" as="style" />
  <!--end::Accessibility Features-->

  <!--begin::Fonts-->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
    integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
    crossorigin="anonymous"
    media="print"
    onload="this.media = 'all'" />
  <!--end::Fonts-->

  <!--begin::Third Party Plugin(OverlayScrollbars)-->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
    crossorigin="anonymous" />
  <!--end::Third Party Plugin(OverlayScrollbars)-->

  <!--begin::Third Party Plugin(Bootstrap Icons)-->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
    crossorigin="anonymous" />
  <!--end::Third Party Plugin(Bootstrap Icons)-->

  <!--begin::Required Plugin(AdminLTE)-->
  <link rel="stylesheet" href="./css/adminlte.css" />
  <!--end::Required Plugin(AdminLTE)-->
  <style>
    .app-main {
      margin-left: 0 !important;
      width: 100% !important;
    }

    .app-header {
      margin-left: 0 !important;
    }
  </style>
</head>
<!--end::Head-->
<!--begin::Body-->

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
  <!--begin::App Wrapper-->
  <div class="app-wrapper">
    <!--begin::Sidebar-->
    <aside class="app-sidebar bg-body-secondary shadow d-none" data-bs-theme="dark">
      <!--begin::Sidebar Brand-->
      <div class="sidebar-brand">
        <!--begin::Brand Link-->
        <a href="./index.html" class="brand-link">
          <!--begin::Brand Image-->
          <img
            src="./assets/img/AdminLTELogo.png"
            alt="AdminLTE Logo"
            class="brand-image opacity-75 shadow" />
          <!--end::Brand Image-->
          <!--begin::Brand Text-->
          <span class="brand-text fw-light">AdminLTE 4</span>
          <!--end::Brand Text-->
        </a>
        <!--end::Brand Link-->
      </div>
      <!--end::Sidebar Brand-->
      <!--begin::Sidebar Search-->
      <div class="sidebar-search" role="search">
        <label for="sidebar-search-input" class="visually-hidden">Filter menu</label>
        <input
          type="search"
          id="sidebar-search-input"
          class="form-control form-control-sm"
          placeholder="Filter menu…"
          autocomplete="off"
          data-lte-toggle="sidebar-search"
          data-lte-target="#navigation" />
        <p class="fs-7 text-secondary mt-2 mb-0" data-lte-search-empty role="status" hidden>
          No matching pages.
        </p>
      </div>
      <!--end::Sidebar Search-->
    </aside>
    <!--end::Sidebar-->
    <!--begin::App Main-->
    <main class="app-main">
      <!--begin::App Content Header-->
      <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
          <!--begin::Row-->
          <div class="row">
            <div class="col-sm-6">
              <h1 class="mb-0 fs-3">Rekap Nilai Mahasiswa</h1>
            </div>
            <div class="col-sm-6">
              <nav aria-label="breadcrumb">
              </nav>
            </div>
          </div>
          <!--end::Row-->
        </div>
        <!--end::Container-->
      </div>
      <!--end::App Content Header-->
      <!--begin::App Content-->
      <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
          <!--begin::Row-->
          <div class="row">
            <div class="col-12">
              <!--begin::Card-->
              <div class="card mb-4">


                <!--begin::Card Header-->
                <div class="card-header">
                  <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 w-100">
                    <h3 class="card-title mb-0">Data Nilai Mahasiswa</h3>

                    <div class="d-flex flex-wrap align-items-center gap-2">
                      <form method="GET" action="users.php"
                        class="d-flex flex-wrap align-items-center gap-2 mb-0">

                        <div class="input-group input-group-sm" style="width: 200px;">
                          <span class="input-group-text">
                            <i class="bi bi-search" aria-hidden="true"></i>
                          </span>
                          <input
                            type="search"
                            name="cari"
                            class="form-control"
                            placeholder="Cari nama mahasiswa..."
                            value="<?php echo htmlspecialchars($cariNama, ENT_QUOTES, 'UTF-8'); ?>">
                        </div>

                        <select name="predikat"
                          class="form-select form-select-sm"
                          style="width: 190px;"
                          onchange="this.form.submit()">
                          <option value="">Semua Predikat</option>
                          <option value="Sangat Memuaskan" <?php if ($predikat == 'Sangat Memuaskan') echo 'selected'; ?>>Sangat Memuaskan</option>
                          <option value="Memuaskan" <?php if ($predikat == 'Memuaskan') echo 'selected'; ?>>Memuaskan</option>
                          <option value="Cukup Memuaskan" <?php if ($predikat == 'Cukup Memuaskan') echo 'selected'; ?>>Cukup Memuaskan</option>
                          <option value="Kurang Memuaskan" <?php if ($predikat == 'Kurang Memuaskan') echo 'selected'; ?>>Kurang Memuaskan</option>
                          <option value="Tidak lulus" <?php if ($predikat == 'Tidak lulus') echo 'selected'; ?>>Tidak Lulus</option>
                        </select>

                        <select name="urut"
                          class="form-select form-select-sm"
                          style="width: 170px;"
                          onchange="this.form.submit()">
                          <option value="DESC" <?php if ($urut == 'DESC') echo 'selected'; ?>>Nilai Tertinggi</option>
                          <option value="ASC" <?php if ($urut == 'ASC') echo 'selected'; ?>>Nilai Terendah</option>
                        </select>

                        <select name="jumlah"
                          class="form-select form-select-sm"
                          style="width: 80px;"
                          onchange="this.form.submit()">
                          <option value="5" <?php if ($dataPerHalaman == 5) echo 'selected'; ?>>5</option>
                          <option value="10" <?php if ($dataPerHalaman == 10) echo 'selected'; ?>>10</option>
                          <option value="15" <?php if ($dataPerHalaman == 15) echo 'selected'; ?>>15</option>
                        </select>

                        <a href="users.php" class="btn btn-sm btn-secondary">Reset</a>
                      </form>

                      <button
                        type="button"
                        class="btn btn-primary btn-sm text-nowrap"
                        data-bs-toggle="modal"
                        data-bs-target="#modal-add-user">
                        <i class="bi bi-plus-lg"></i> Tambah Data
                      </button>
                    </div>
                  </div>
                </div>
                <!--end::Card Header-->

                <!-- Library SweetAlert2 -->
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

                <?php if ($pesan == 'tambah_berhasil') : ?>
                  <script>
                    Swal.fire({
                      icon: 'success',
                      title: 'Berhasil!',
                      text: 'Data mahasiswa berhasil ditambahkan!',
                      confirmButtonText: 'Oke',
                      timer: 2000,
                      timerProgressBar: true,
                      background: '#343a40',
                      color: '#f8f9fa',
                      confirmButtonColor: '#0d6efd'
                    });
                  </script>
                <?php endif; ?>

                <?php if ($pesan == 'tambah_gagal') : ?>
                  <script>
                    Swal.fire({
                      icon: 'error',
                      title: 'Gagal!',
                      text: 'Data mahasiswa gagal ditambahkan. Silakan coba lagi.',
                      confirmButtonText: 'Oke',
                      background: '#343a40',
                      color: '#f8f9fa',
                      confirmButtonColor: '#0d6efd'
                    });
                  </script>
                <?php endif; ?>

                <?php if ($pesan == 'edit_berhasil') : ?>
                  <script>
                    Swal.fire({
                      icon: 'success',
                      title: 'Berhasil!',
                      text: 'Data mahasiswa berhasil diperbarui!',
                      confirmButtonText: 'Oke',
                      timer: 2000,
                      timerProgressBar: true,
                      background: '#343a40',
                      color: '#f8f9fa',
                      confirmButtonColor: '#0d6efd'
                    });
                  </script>
                <?php endif; ?>

                <?php if ($pesan == 'edit_gagal') : ?>
                  <script>
                    Swal.fire({
                      icon: 'error',
                      title: 'Gagal!',
                      text: 'Data mahasiswa gagal diperbarui. Silakan coba lagi.',
                      confirmButtonText: 'Oke',
                      background: '#343a40',
                      color: '#f8f9fa',
                      confirmButtonColor: '#0d6efd'
                    });
                  </script>
                <?php endif; ?>

                <?php if ($pesan == 'hapus_berhasil') : ?>
                  <script>
                    Swal.fire({
                      icon: 'success',
                      title: 'Berhasil!',
                      text: 'Data mahasiswa berhasil dihapus!',
                      confirmButtonText: 'Oke',
                      timer: 2000,
                      timerProgressBar: true,
                      background: '#343a40',
                      color: '#f8f9fa',
                      confirmButtonColor: '#0d6efd'
                    });
                  </script>
                <?php endif; ?>

                <?php if ($pesan == 'hapus_gagal') : ?>
                  <script>
                    Swal.fire({
                      icon: 'error',
                      title: 'Gagal!',
                      text: 'Data mahasiswa gagal dihapus. Silakan coba lagi.',
                      confirmButtonText: 'Oke',
                      background: '#343a40',
                      color: '#f8f9fa',
                      confirmButtonColor: '#0d6efd'
                    });
                  </script>
                <?php endif; ?>

                <!--begin::Card Body-->
                <div class="card-body">

                  <!--begin::Card Body-->
                  <div class="card-body p-0">
                    <div class="table-responsive">
                      <table class="table table-hover align-middle m-0" id="tabel-nilai">
                        <thead>
                          <tr>
                            <th>NIM</th>
                            <th>Nama Lengkap</th>
                            <th>Nilai Akhir</th>
                            <th>Nilai Huruf</th>
                            <th>Predikat</th>
                            <th>Tgl Dibuat</th>
                            <th class="text-end">Aksi</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          // Process the result set
                          if (mysqli_num_rows($result) > 0) {

                            while ($row = mysqli_fetch_assoc($result)) {
                              $fotoMahasiswa = [
                                'icon-1.jpg',
                                'icon-2.jpg',
                                'icon-3.jpg',
                                'icon-4.jpg',
                              ];

                              $indexFoto = ((int) substr($row['nim'], -2)) % count($fotoMahasiswa);
                              $foto = $fotoMahasiswa[$indexFoto];

                              echo "<tr>";
                              echo "<td>" . htmlspecialchars($row['nim']) . "</td>";
                              echo "<td>";
                              echo "<div class='d-flex align-items-center'>";
                              echo "<img src='assets/img/" . $foto . "' class='rounded-circle me-2' width='35' height='35' alt='Foto Mahasiswa'>";
                              echo "<span>" . htmlspecialchars($row['namalengkap']) . "</span>";
                              echo "</div>";
                              echo "</td>";
                              echo "<td>" . $row['nilaiakhir'] . "</td>";
                              echo "<td>" . $row['nilaihuruf'] . "</td>";
                              echo "<td>" . $row['predikat'] . "</td>";
                              echo "<td>" . $row['created_at'] . "</td>";
                              echo "<td class='text-end'>
                            <div class='btn-group btn-group-sm'>

                              <button type='button'
                                class='btn btn-outline-secondary'
                                title='Edit'
                                data-bs-toggle='modal'
                                data-bs-target='#modal-edit-nilai'
                                data-nim='" . htmlspecialchars($row['nim'], ENT_QUOTES) . "'
                                data-nama='" . htmlspecialchars($row['namalengkap'], ENT_QUOTES) . "'
                                data-nilai='" . htmlspecialchars($row['nilaiakhir'], ENT_QUOTES) . "'>
                                <i class='bi bi-pencil'></i>
                              </button>
           
                              <button type='button'
                                  class='btn btn-outline-danger'
                                  title='Hapus'
                                  data-nim='" . htmlspecialchars($row['nim'], ENT_QUOTES, 'UTF-8') . "'
                                  data-nama='" . htmlspecialchars($row['namalengkap'], ENT_QUOTES, 'UTF-8') . "'
                                  onclick='konfirmasiHapus(this.dataset.nim, this.dataset.nama)'>
                                  <i class='bi bi-trash'></i>
                              </button>

                            </div>
                          </td>";
                              echo "</tr>";
                            }
                          } else {
                            echo "<tr><td colspan='7' class='text-center'>Data mahasiswa belum tersedia.</td></tr>";
                          }
                          ?>
                        </tbody>
                      </table>
                    </div>
                    <!-- /.table-responsive -->
                  </div>
                  <!--end::Card Body-->

                  <!--begin::Card Footer-->
                  <div class="card-footer clearfix">
                    <div class="float-start pt-1 fs-7 text-body-secondary">
                      <?php
                      if ($totalData == 0) {
                        echo "Tidak ada data yang cocok.";
                      } else {
                        $awal = $offset + 1;
                        $akhir = min($offset + $dataPerHalaman, $totalData);

                        echo "Menampilkan $awal sampai $akhir dari $totalData nilai";
                      }
                      ?>
                    </div>

                    <ul class="pagination pagination-sm m-0 float-end">
                      <?php if ($totalHalaman > 1): ?>

                        <?php if ($halaman > 1): ?>
                          <li class="page-item">
                            <a class="page-link"
                              href="?cari=<?php echo urlencode($cariNama); ?>&predikat=<?php echo urlencode($predikat); ?>&urut=<?php echo $urut; ?>&jumlah=<?php echo $dataPerHalaman; ?>&halaman=<?php echo $halaman - 1; ?>">
                              &laquo;
                            </a>
                          </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalHalaman; $i++): ?>
                          <li class="page-item <?php echo ($i == $halaman) ? 'active' : ''; ?>">
                            <a class="page-link"
                              href="?cari=<?php echo urlencode($cariNama); ?>&predikat=<?php echo urlencode($predikat); ?>&urut=<?php echo $urut; ?>&jumlah=<?php echo $dataPerHalaman; ?>&halaman=<?php echo $i; ?>">
                              <?php echo $i; ?>
                            </a>
                          </li>
                        <?php endfor; ?>

                        <?php if ($halaman < $totalHalaman): ?>
                          <li class="page-item">
                            <a class="page-link"
                              href="?cari=<?php echo urlencode($cariNama); ?>&predikat=<?php echo urlencode($predikat); ?>&urut=<?php echo $urut; ?>&jumlah=<?php echo $dataPerHalaman; ?>&halaman=<?php echo $halaman + 1; ?>">
                              &raquo;
                            </a>
                          </li>
                        <?php endif; ?>

                      <?php endif; ?>
                    </ul>
                  </div>
                  <!--end::Card Footer-->
                </div>
                <!--end::Card-->
              </div>
              <!-- /.col -->
            </div>
            <!--end::Row-->

            <!--begin::Add User Modal-->
            <!-- Modal Tambah Data Nilai -->
            <div
              class="modal fade"
              id="modal-add-user"
              tabindex="-1"
              aria-labelledby="modal-add-user-label"
              aria-hidden="true">

              <div class="modal-dialog">
                <div class="modal-content">

                  <form action="../../nilaimhsw.php" method="POST">

                    <div class="modal-header">
                      <h5 class="modal-title" id="modal-add-user-label">
                        Tambah Data Nilai Mahasiswa
                      </h5>
                      <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                    </div>

                    <div class="modal-body">

                      <div class="mb-3">
                        <label for="nim" class="form-label">NIM</label>
                        <input
                          type="text"
                          class="form-control"
                          id="nim"
                          name="nim"
                          maxlength="10"
                          required>
                      </div>

                      <div class="mb-3">
                        <label for="namalengkap" class="form-label">
                          Nama Lengkap
                        </label>
                        <input
                          type="text"
                          class="form-control"
                          id="namalengkap"
                          name="namalengkap"
                          required>
                      </div>

                      <div class="mb-3">
                        <label for="nilaiakhir" class="form-label">
                          Nilai Akhir
                        </label>
                        <input
                          type="number"
                          class="form-control"
                          id="nilaiakhir"
                          name="nilaiakhir"
                          min="0"
                          max="100"
                          step="0.01"
                          required>
                      </div>

                    </div>

                    <div class="modal-footer">
                      <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Batal
                      </button>

                      <button type="submit" class="btn btn-primary">
                        Simpan Data
                      </button>
                    </div>

                  </form>

                </div>
              </div>
            </div>
            <!-- End Modal Tambah Data Nilai -->

            <!-- Modal Edit Nilai -->
            <div class="modal fade" id="modal-edit-nilai" tabindex="-1"
              aria-labelledby="modal-edit-nilai-label" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">

                  <form action="../../update_nilaimhsw.php" method="POST">
                    <div class="modal-header">
                      <h5 class="modal-title" id="modal-edit-nilai-label">
                        Edit Data Nilai Mahasiswa
                      </h5>
                      <button type="button" class="btn-close"
                        data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                      <div class="mb-3">
                        <label for="edit-nim" class="form-label">NIM</label>
                        <input type="text" class="form-control" id="edit-nim"
                          name="nim" readonly required>
                      </div>

                      <div class="mb-3">
                        <label for="edit-nama" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="edit-nama"
                          name="namalengkap" required>
                      </div>

                      <div class="mb-3">
                        <label for="edit-nilai" class="form-label">Nilai Akhir</label>
                        <input type="number" class="form-control" id="edit-nilai"
                          name="nilaiakhir" min="0" max="100" step="0.01" required>
                      </div>
                    </div>

                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Batal</button>
                      <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                  </form>

                </div>
              </div>
            </div>
            <!-- End Modal Edit Nilai -->


            <script>
              // Mengisi data mahasiswa ke modal Edit
              document.getElementById('modal-edit-nilai')
                .addEventListener('show.bs.modal', function(event) {
                  const tombol = event.relatedTarget;

                  document.getElementById('edit-nim').value =
                    tombol.getAttribute('data-nim');

                  document.getElementById('edit-nama').value =
                    tombol.getAttribute('data-nama');

                  document.getElementById('edit-nilai').value =
                    tombol.getAttribute('data-nilai');
                });

              function konfirmasiHapus(nim, nama) {
                Swal.fire({
                  title: 'Hapus data mahasiswa?',
                  text: 'Data ' + nama + ' akan dihapus dan tidak dapat dikembalikan.',
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonText: 'Ya, hapus!',
                  cancelButtonText: 'Batal',
                  confirmButtonColor: '#dc3545',
                  cancelButtonColor: '#6c757d',
                  background: '#343a40',
                  color: '#f8f9fa'
                }).then((result) => {
                  if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '../../hapusnilaimhsw.php';

                    const inputNim = document.createElement('input');
                    inputNim.type = 'hidden';
                    inputNim.name = 'nim';
                    inputNim.value = nim;

                    form.appendChild(inputNim);
                    document.body.appendChild(form);
                    form.submit();
                  }
                });
              }
            </script>
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content-->
    </main>
    <!--end::App Main-->
    <!--begin::Footer-->
    <footer class="app-footer">
      <!--begin::To the end-->
      <div class="float-end d-none d-sm-inline">
        Pemrograman Web Semester 5 Desain menggunakan AdminLTE
      </div>
      <!--end::To the end-->
      <!--begin::Copyright-->
      <strong>
        Hak cipta &copy; pada Prodi TI - 2026. &nbsp;
        <a href="https://adminlte.io" class="text-decoration-none">AdminLTE.io</a>.
      </strong>
      Seluruh hak cipta dilindungi.
      <!--end::Copyright-->
    </footer>
    <!--end::Footer-->
  </div>
  <!--end::App Wrapper-->
  <!--begin::Script-->
  <!--begin::Third Party Plugin(OverlayScrollbars)-->
  <script
    src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
    crossorigin="anonymous"></script>
  <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
  <script
    src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    crossorigin="anonymous"></script>
  <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"
    crossorigin="anonymous"></script>
  <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
  <script src="./js/adminlte.js"></script>
  <!--end::Required Plugin(AdminLTE)-->
  <!--begin::OverlayScrollbars Configure-->
  <script>
    const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
    const Default = {
      scrollbarTheme: 'os-theme-light',
      scrollbarAutoHide: 'leave',
      scrollbarClickScroll: true,
    };
    document.addEventListener('DOMContentLoaded', function() {
      const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);

      // Disable OverlayScrollbars on mobile devices to prevent touch interference
      const isMobile = window.innerWidth <= 992;

      if (
        sidebarWrapper &&
        OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined &&
        !isMobile
      ) {
        OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
          scrollbars: {
            theme: Default.scrollbarTheme,
            autoHide: Default.scrollbarAutoHide,
            clickScroll: Default.scrollbarClickScroll,
          },
        });
      }
    });
  </script>
  <!--end::OverlayScrollbars Configure-->

  <!--begin::Color Mode Toggle-->
  <!-- The light/dark/auto switcher ships in adminlte.js as the ColorMode
     module (since 4.1) — no page script needed. Only the no-flash snippet
     in <head> stays inline, because it must run before first paint. -->
  <!--end::Color Mode Toggle-->

  <!--end::Script-->

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const inputCari = document.querySelector('input[name="cari"]');

      if (!inputCari) {
        return;
      }

      let waktuTunggu;

      inputCari.addEventListener('input', function() {
        clearTimeout(waktuTunggu);

        waktuTunggu = setTimeout(function() {
          const url = new URL(window.location.href);

          url.searchParams.set('cari', inputCari.value.trim());
          url.searchParams.set('halaman', '1');

          sessionStorage.setItem('fokusPencarian', 'ya');

          window.location.href = url.toString();
        }, 800);
      });

      if (sessionStorage.getItem('fokusPencarian') === 'ya') {
        sessionStorage.removeItem('fokusPencarian');
        inputCari.focus();
        inputCari.setSelectionRange(
          inputCari.value.length,
          inputCari.value.length
        );
      }
    });
  </script>

</body>
<!--end::Body-->

</html>