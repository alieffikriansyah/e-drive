<?php $this->load->view('layout/header', ['title' => 'Hapus Semua Data']); ?>

<div class="mb-6">
    <h1 class="text-3xl font-extrabold text-mapul-green-md drop-shadow-sm uppercase tracking-wider flex items-center gap-2">
        <i class="fa fa-trash-alt text-mapul-yellow"></i> Hapus Keseluruhan Data
    </h1>
    <p class="text-gray-500 text-sm mt-1 font-medium">Modul administratif khusus untuk membersihkan data.</p>
</div>

<div class="max-w-3xl mx-auto mt-10">
    <div class="bg-red-50 border-2 border-red-500 rounded-xl shadow-lg overflow-hidden">
        <div class="bg-red-600 px-6 py-4 flex items-center gap-3">
            <i class="fa fa-exclamation-triangle text-3xl text-yellow-300 animate-pulse"></i>
            <h4 class="text-xl font-bold text-white uppercase tracking-wider">Peringatan Keras!</h4>
        </div>
        
        <div class="p-6 md:p-8">
            <p class="text-red-900 font-medium mb-4 leading-relaxed">
                Modul ini akan <strong class="text-red-700 bg-red-100 px-1 rounded">MENGHAPUS SEMUA DATA TRANSAKSI DAN OPERASIONAL</strong> 
                (Penjualan, Detail Penjualan, Harga Paten, Produk, Cabang, Satuan, Kategori, dan Pengeluaran).
            </p>
            
            <p class="text-red-900 font-medium mb-8 leading-relaxed">
                Data yang <strong class="text-green-700 bg-green-100 px-1 rounded">TIDAK DIHAPUS</strong> hanyalah: 
                Users, Roles, Role Menu Access, System Menus, dan Log Record Users.
            </p>
            
            <div class="bg-white p-6 rounded-lg border border-red-200 shadow-sm">
                <form id="formHapusData" autocomplete="off">
                    <div class="mb-5">
                        <label class="block text-gray-700 font-bold mb-2">Masukkan Password Khusus untuk Mengkonfirmasi:</label>
                        <input type="password" class="mapul-input border-red-300 focus:ring-red-400 focus:border-red-400 text-center tracking-[0.2em] text-lg" name="password" id="password" required placeholder="••••••••">
                    </div>
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg shadow-md border border-red-800 transition-colors flex items-center justify-center gap-2 text-lg">
                        <i class="fa fa-fire"></i> HAPUS SEMUA DATA SEKARANG <i class="fa fa-fire"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layout/footer'); ?>

<!-- Sweet-Alert  -->
<script src="<?= base_url('assets/js/sweetalert2.min.js') ?>"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var formHapusData = document.getElementById('formHapusData');
    if(formHapusData) {
        formHapusData.addEventListener('submit', function(e) {
            e.preventDefault();
            
            var password = document.getElementById('password').value;

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
                    Swal.fire({
                        title: 'Memproses...',
                        html: 'Sedang menghapus data, mohon tunggu.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });

                    var formData = new FormData();
                    formData.append('password', password);

                    fetch('<?= base_url("hapus_keseluruhan_data/proses_hapus") ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(response => {
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
                    })
                    .catch(error => {
                        Swal.fire(
                            'Error!',
                            'Terjadi kesalahan pada server.',
                            'error'
                        );
                    });
                }
            });
        });
    }
});
</script>