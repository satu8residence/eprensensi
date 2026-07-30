@extends('layouts.admin.tabler')
@section('content')

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Utilities > Bersihkan Foto
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Bersihkan Foto Presensi</h3>
                        <p class="text-muted">Pilih Periode Tanggal Presensi</p>

                        <form action="/bersihkanfoto" method="POST" id="frmBersihkanFoto">
                            @csrf
                            <div class="row g-3 align-items-end mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">Tanggal Awal</label>
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <rect x="4" y="4" width="16" height="16" rx="2"></rect>
                                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                                <line x1="4" y1="10" x2="20" y2="10"></line>
                                            </svg>
                                        </span>
                                        <input type="text" id="dari" name="dari" class="form-control datepicker" placeholder="Pilih tanggal awal" autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tanggal Akhir</label>
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <rect x="4" y="4" width="16" height="16" rx="2"></rect>
                                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                                <line x1="4" y1="10" x2="20" y2="10"></line>
                                            </svg>
                                        </span>
                                        <input type="text" id="sampai" name="sampai" class="form-control datepicker" placeholder="Pilih tanggal akhir" autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-danger w-100" id="btnHapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash-x" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M4 7h16"></path>
                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                                            <path d="M10 12l4 4m0 -4l-4 4"></path>
                                        </svg>
                                        Hapus Foto Presensi
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="alert alert-warning" role="alert" style="background-color: #fff3cd; border-color: #ffeeba; color: #856404;">
                            <div class="d-flex">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <circle cx="12" cy="12" r="9"></circle>
                                        <line x1="12" y1="8" x2="12" y2="12"></line>
                                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                    </svg>
                                </div>
                                <div>
                                    <strong>Peringatan:</strong> Tindakan menghapus FILE FOTO presensi tidak dapat dibatalkan. Data presensi tetap tersimpan, hanya file fotonya yang dihapus. Pastikan Anda telah memilih periode tanggal yang tepat.
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    $(function() {
        $(".datepicker").datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'yyyy-mm-dd'
        });

        $("#frmBersihkanFoto").submit(function(e) {
            e.preventDefault();
            var form = this;
            var dari = $("#dari").val();
            var sampai = $("#sampai").val();

            if (dari == "" || sampai == "") {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Silakan pilih periode tanggal terlebih dahulu!',
                    icon: 'warning'
                });
                return false;
            }

            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Tindakan menghapus FILE FOTO presensi dari " + dari + " s/d " + sampai + " tidak dapat dibatalkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus Permanen!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
