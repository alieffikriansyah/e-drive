<?php
require_once APPPATH . 'views/layout/header.php';
require_once APPPATH . 'views/layout/sidebar.php';
?>

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Log Aktivitas User</h4>
                </div>
            </div>
        </div>

        <div class="row" id="authSection">
            <div class="col-md-6 offset-md-3 mt-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Keamanan Ekstra</h4>
                        <p class="text-center">Silakan masukkan password khusus untuk melihat data log aktivitas.</p>
                        <form id="formAuth" autocomplete="off">
                            <div class="form-group text-center">
                                <input type="password" class="form-control text-center" name="password" id="authPassword" required placeholder="********">
                            </div>
                            <div class="text-center mt-3">
                                <button type="submit" class="btn btn-primary">Buka Log</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row d-none" id="dataSection">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label>Mulai Tanggal</label>
                                <input type="date" id="tgl_awal" class="form-control" value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-3">
                                <label>Sampai Tanggal</label>
                                <input type="date" id="tgl_akhir" class="form-control" value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-3 align-self-end mt-2 mt-md-0">
                                <button type="button" class="btn btn-primary btn-block" onclick="loadData()">Filter</button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="tableLog">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Waktu</th>
                                        <th>User</th>
                                        <th>Aksi</th>
                                        <th>Keterangan</th>
                                        <th>IP Address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data dimuat via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APPPATH . 'views/layout/footer.php'; ?>
<script src="<?= base_url('assets/libs/sweetalert2/sweetalert2.min.js') ?>"></script>

<script>
    <?php if(!empty($_SESSION['log_record_authenticated'])): ?>
        $('#authSection').addClass('d-none');
        $('#dataSection').removeClass('d-none');
        loadData();
    <?php endif; ?>

    $('#formAuth').on('submit', function(e) {
        e.preventDefault();
        var password = $('#authPassword').val();
        
        $.ajax({
            url: '<?= base_url("log_record_users/auth") ?>',
            type: 'POST',
            dataType: 'json',
            data: { password: password },
            success: function(response) {
                if(response.status) {
                    $('#authSection').addClass('d-none');
                    $('#dataSection').removeClass('d-none');
                    loadData();
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            }
        });
    });

    function loadData() {
        var tgl_awal = $('#tgl_awal').val();
        var tgl_akhir = $('#tgl_akhir').val();

        Swal.fire({
            title: 'Memuat Data...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading() }
        });

        $.ajax({
            url: '<?= base_url("log_record_users/load_data") ?>',
            type: 'GET',
            dataType: 'json',
            data: { tgl_awal: tgl_awal, tgl_akhir: tgl_akhir },
            success: function(response) {
                Swal.close();
                var html = '';
                if(response.status && response.data.length > 0) {
                    $.each(response.data, function(i, v) {
                        html += `
                            <tr>
                                <td>${i + 1}</td>
                                <td>${v.created_at}</td>
                                <td>${v.nama_user || '-'}</td>
                                <td><span class="badge badge-info">${v.action}</span></td>
                                <td>${v.keterangan}</td>
                                <td>${v.ip_address || '-'}</td>
                            </tr>
                        `;
                    });
                } else {
                    html = '<tr><td colspan="6" class="text-center">Tidak ada data ditemukan</td></tr>';
                }
                $('#tableLog tbody').html(html);
            }
        });
    }
</script>