<?php
require_once APPPATH . 'views/layout/header.php';
require_once APPPATH . 'views/layout/sidebar.php';
?>

<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Hapus Keseluruhan Data</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card bg-danger">
                    <div class="card-body text-white">
                        <h4 class="card-title text-white">⚠️ PERINGATAN KERAS!</h4>
                        <p class="card-title-desc text-white">
                            Modul ini akan <strong>MENGHAPUS SEMUA DATA TRANSAKSI DAN OPERASIONAL</strong> (Penjualan, Detail Penjualan, Stok, Harga Paten, Produk, Cabang, Satuan, Kategori, Pengeluaran, dan Log Users).<br><br>
                            Data yang <strong>TIDAK DIHAPUS</strong> hanyalah: Users, Roles, dan System Menus.
                        </p>
                        
                        <div class="mt-4">
                            <form id="formHapusData" autocomplete="off">
                                <div class="form-group">
                                    <label>Masukkan Password Khusus untuk Mengkonfirmasi:</label>
                                    <input type="password" class="form-control" name="password" id="password" required placeholder="Masukkan password khusus...">
                                </div>
                                <button type="submit" class="btn btn-warning mt-3">🔥 HAPUS SEMUA DATA SEKARANG 🔥</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APPPATH . 'views/layout/footer.php'; ?>

<!-- Sweet-Alert  -->
<script src="<?= base_url('assets/libs/sweetalert2/sweetalert2.min.js') ?>"></script>

<script>
$(document).ready(function() {
    $('#formHapusData').on('submit', function(e) {
        e.preventDefault();
        
        var password = $('#password').val();

        Swal.fire({
            title: 'Apakah Anda Yakin?',
            text: "Tindakan ini TIDAK BISA DIBATALKAN. Semua data akan terhapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus Semua!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading
                Swal.fire({
                    title: 'Memproses...',
                    html: 'Sedang menghapus data, mohon tunggu.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });

                $.ajax({
                    url: '<?= base_url("hapus_keseluruhan_data/proses_hapus") ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: { password: password },
                    success: function(response) {
                        if(response.status) {
                            Swal.fire(
                                'Terhapus!',
                                response.message,
                                'success'
                            ).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Gagal!',
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'Error!',
                            'Terjadi kesalahan pada server.',
                            'error'
                        );
                    }
                });
            }
        })
    });
});
</script>