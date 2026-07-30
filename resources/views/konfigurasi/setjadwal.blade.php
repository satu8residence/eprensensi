@extends('layouts.admin.tabler')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Jadwal Shift Karyawan (Mingguan)
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
                        <div class="row">
                            <div class="col-12">
                                @if (Session::get('success'))
                                <div class="alert alert-success">
                                    {{ Session::get('success') }}
                                </div>
                                @endif

                                @if (Session::get('warning'))
                                <div class="alert alert-warning">
                                    {{ Session::get('warning') }}
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <a href="#" class="btn btn-primary" id="btnTambahSetJadwal">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-upload" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path>
                                        <polyline points="7 9 12 4 17 9"></polyline>
                                        <line x1="12" y1="4" x2="12" y2="16"></line>
                                    </svg>
                                    Upload Jadwal Shift (Excel/CSV)
                                </a>
                                <a href="/konfigurasi/setjadwal/download-template" class="btn btn-success ms-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-download" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path>
                                        <polyline points="7 11 12 16 17 11"></polyline>
                                        <line x1="12" y1="4" x2="12" y2="16"></line>
                                    </svg>
                                    Download Template Excel (CSV)
                                </a>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <strong>Informasi Kode Jadwal Shift:</strong>
                                    <ul class="mb-0">
                                        @foreach($jadwal_kerja as $jk)
                                            <li><strong>{{ $jk->kode_jadwal }}</strong>: {{ $jk->nama_jadwal }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Kode Set Jadwal</th>
                                            <th>Tanggal Mulai (Senin)</th>
                                            <th>Tanggal Selesai (Minggu)</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($setjadwal as $d)
                                        <tr>
                                            <td>{{ $loop->iteration + $setjadwal->firstItem() - 1 }}</td>
                                            <td>{{ $d->kode_setjadwal }}</td>
                                            <td>{{ date('d-m-Y', strtotime($d->dari)) }}</td>
                                            <td>{{ date('d-m-Y', strtotime($d->sampai)) }}</td>
                                            <td>
                                                <form action="/konfigurasi/setjadwal/{{ $d->kode_setjadwal }}/delete" method="POST" class="delete-form" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm delete-confirm">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                            <line x1="4" y1="6" x2="20" y2="6"></line>
                                                            <path d="M4 6l1-1a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2l1 1"></path>
                                                            <path d="M9 11v6"></path>
                                                            <path d="M13 11v6"></path>
                                                            <path d="M5 6v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2V6"></path>
                                                        </svg>
                                                        Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="mt-2">
                                    {{ $setjadwal->links('vendor.pagination.bootstrap-5') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Upload --}}
<div class="modal modal-blur fade" id="modal-uploadjadwal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Jadwal Shift Mingguan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/konfigurasi/setjadwal/store" method="POST" id="frmUploadJadwal" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label class="form-label">Tanggal Mulai (Hari Senin)</label>
                                <input type="date" id="dari" name="dari" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label class="form-label">Tanggal Selesai (Hari Minggu)</label>
                                <input type="date" id="sampai" name="sampai" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label class="form-label">File Excel (CSV)</label>
                                <input type="file" name="file_csv" class="form-control" accept=".csv,.txt" required>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="form-group">
                                <button class="btn btn-primary w-100" type="submit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-send" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M10 14l11 -11"></path>
                                        <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5"></path>
                                    </svg>
                                    Simpan & Upload
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(function() {
        $("#btnTambahSetJadwal").click(function(e) {
            e.preventDefault();
            $("#modal-uploadjadwal").modal("show");
        });

        // Autofill Tanggal Selesai (Minggu) if Tanggal Mulai (Senin) is changed
        $("#dari").change(function() {
            var dateVal = $(this).val();
            if (dateVal) {
                var date = new Date(dateVal);
                // Calculate next Sunday (6 days after Monday)
                date.setDate(date.getDate() + 6);
                var year = date.getFullYear();
                var month = String(date.getMonth() + 1).padStart(2, '0');
                var day = String(date.getDate()).padStart(2, '0');
                $("#sampai").val(year + '-' + month + '-' + day);
            }
        });

        $(".delete-confirm").click(function(e) {
            var form = $(this).closest('form');
            e.preventDefault();
            Swal.fire({
                title: 'Apakah Anda Yakin Data Ini Mau Di Hapus ?',
                text: "Jika Ya Maka Konfigurasi Jadwal Minggu Ini Akan Dihapus Permanent",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
