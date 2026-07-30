@extends('layouts.admin.tabler')
@section('content')

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Lembur
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
                        <!-- Button + Input Lembur -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <button class="btn btn-primary" id="btnTambahLembur">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                    Input Lembur
                                </button>
                            </div>
                        </div>

                        <!-- Search Filters Form -->
                        <form action="/lembur" method="GET" class="mb-4">
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <input type="text" id="dari" name="dari" value="{{ Request('dari') }}" class="form-control datepicker" placeholder="Dari" autocomplete="off">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" id="sampai" name="sampai" value="{{ Request('sampai') }}" class="form-control datepicker" placeholder="Sampai" autocomplete="off">
                                </div>
                                <div class="col-md-3">
                                    <select name="kode_cabang" class="form-select">
                                        <option value="">Semua Cabang</option>
                                        @foreach ($cabang as $c)
                                        <option value="{{ $c->kode_cabang }}" {{ Request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}>{{ $c->nama_cabang }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="kode_dept" class="form-select">
                                        <option value="">Semua Departemen</option>
                                        @foreach ($departemen as $d)
                                        <option value="{{ $d->kode_dept }}" {{ Request('kode_dept') == $d->kode_dept ? 'selected' : '' }}>{{ $d->nama_dept }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mt-2">
                                    <select name="status" class="form-select">
                                        <option value="">Semua Status</option>
                                        <option value="0" {{ Request('status') === '0' ? 'selected' : '' }}>Pending</option>
                                        <option value="1" {{ Request('status') === '1' ? 'selected' : '' }}>Approved</option>
                                        <option value="2" {{ Request('status') === '2' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <input type="text" name="nama_karyawan" value="{{ Request('nama_karyawan') }}" class="form-control" placeholder="Nama Karyawan">
                                </div>
                                <div class="col-md-3 mt-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <circle cx="10" cy="10" r="7"></circle>
                                            <line x1="21" y1="21" x2="15" y2="15"></line>
                                        </svg>
                                        Cari Data
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Overtime Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>TANGGAL</th>
                                        <th>NIK</th>
                                        <th>NAMA KARYAWAN</th>
                                        <th>CABANG</th>
                                        <th>WAKTU LEMBUR</th>
                                        <th>LEMBUR IN</th>
                                        <th>LEMBUR OUT</th>
                                        <th>JML JAM</th>
                                        <th>STATUS</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($lembur as $l)
                                    <tr>
                                        <td>{{ date('d F Y', strtotime($l->tanggal)) }}</td>
                                        <td>{{ $l->nik }}</td>
                                        <td>{{ $l->nama_lengkap }}</td>
                                        <td>{{ $l->nama_cabang }}</td>
                                        <td>
                                            <span class="badge bg-success mb-1">{{ date('d-m-Y H:i', strtotime($l->tanggal_dari)) }}</span><br>
                                            <span class="badge bg-danger">{{ date('d-m-Y H:i', strtotime($l->tanggal_sampai)) }}</span>
                                        </td>
                                        <td>
                                            @if ($l->jam_in)
                                            @php
                                                $presence_start = strtotime($l->tanggal . ' ' . $l->jam_in);
                                                $ot_start = strtotime($l->tanggal_dari);
                                                $lembur_in = max($presence_start, $ot_start);
                                            @endphp
                                            {{ date('d-m-Y H:i', $lembur_in) }}
                                            @else
                                            <span class="text-muted"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clock" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><circle cx="12" cy="12" r="9"></circle><polyline points="12 7 12 12 15 15"></polyline></svg></span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($l->jam_out)
                                            @php
                                                $presence_start = strtotime($l->tanggal . ' ' . $l->jam_in);
                                                $presence_end = strtotime($l->tanggal . ' ' . $l->jam_out);
                                                if ($presence_end < $presence_start) {
                                                    $presence_end += 86400;
                                                }
                                                $ot_end = strtotime($l->tanggal_sampai);
                                                $lembur_out = min($presence_end, $ot_end);
                                            @endphp
                                            {{ date('d-m-Y H:i', $lembur_out) }}
                                            @else
                                            <span class="text-muted"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clock" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><circle cx="12" cy="12" r="9"></circle><polyline points="12 7 12 12 15 15"></polyline></svg></span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($l->jam_in && $l->jam_out)
                                            @php
                                                $presence_start = strtotime($l->tanggal . ' ' . $l->jam_in);
                                                $presence_end = strtotime($l->tanggal . ' ' . $l->jam_out);
                                                if ($presence_end < $presence_start) {
                                                    $presence_end += 86400;
                                                }
                                                $ot_start = strtotime($l->tanggal_dari);
                                                $ot_end = strtotime($l->tanggal_sampai);
                                                if ($ot_end < $ot_start) {
                                                    $ot_end += 86400;
                                                }
                                                $intersect_start = max($presence_start, $ot_start);
                                                $intersect_end = min($presence_end, $ot_end);
                                                $diff = 0;
                                                if ($intersect_start < $intersect_end) {
                                                    $diff = ($intersect_end - $intersect_start) / 3600;
                                                }
                                                echo round($diff, 2);
                                            @endphp
                                            @else
                                            <span class="text-muted"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clock" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><circle cx="12" cy="12" r="9"></circle><polyline points="12 7 12 12 15 15"></polyline></svg></span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($l->status == 0)
                                            <span class="badge bg-warning"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-hourglass-high" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M6.5 7h11"></path><path d="M6 20v-2a6 6 0 1 1 12 0v2a1 1 0 0 1 -1 1h-10a1 1 0 0 1 -1 -1z"></path><path d="M6 4v2a6 6 0 1 0 12 0v-2a1 1 0 0 0 -1 -1h-10a1 1 0 0 0 -1 1z"></path></svg> Pending</span>
                                            @elseif ($l->status == 1)
                                            <span class="badge bg-success"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l5 5l10 -10"></path></svg> Approved</span>
                                            @else
                                            <span class="badge bg-danger"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg> Rejected</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                @if ($l->status == 0)
                                                <!-- Approve & Reject forms -->
                                                <form action="/lembur/{{ $l->kode_lembur }}/approve" method="POST" class="me-1">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm" title="Setujui">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l5 5l10 -10"></path></svg>
                                                    </button>
                                                </form>
                                                <form action="/lembur/{{ $l->kode_lembur }}/reject" method="POST" class="me-1">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Tolak">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                                    </button>
                                                </form>
                                                @endif
                                                <a href="#" class="edit btn btn-info btn-sm me-1" kode_lembur="{{ $l->kode_lembur }}" title="Edit">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path>
                                                        <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"></path>
                                                        <path d="M16 5l3 3"></path>
                                                    </svg>
                                                </a>
                                                <form action="/lembur/{{ $l->kode_lembur }}/delete" method="POST">
                                                    @csrf
                                                    <a class="btn btn-danger btn-sm delete-confirm" title="Hapus">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash-filled" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                            <path d="M20 6a1 1 0 0 1 .117 1.993l-.117 .007h-.081l-.919 11a3 3 0 0 1 -2.824 2.995l-.176 .005h-8c-1.598 0 -2.904 -1.249 -2.992 -2.75l-.005 -.167l-.923 -11.083h-.08a1 1 0 0 1 -.117 -1.993l.117 -.007h16z" stroke-width="0" fill="currentColor"></path>
                                                            <path d="M14 2a2 2 0 0 1 2 2a1 1 0 0 1 -1.993 .117l-.007 -.117h-4l-.007 .117a1 1 0 0 1 -1.993 -.117a2 2 0 0 1 1.85 -1.995l.15 -.005h4z" stroke-width="0" fill="currentColor"></path>
                                                        </svg>
                                                    </a>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted">Data Lembur Tidak Ditemukan.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{ $lembur->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Input Lembur --}}
<div class="modal modal-blur fade" id="modal-inputlembur" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Input Data Lembur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/lembur/store" method="POST" id="frmLembur">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Lembur</label>
                            <input type="text" id="tanggal_lembur" name="tanggal" class="form-control datepicker" placeholder="YYYY-MM-DD" autocomplete="off" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Jam Mulai</label>
                            <input type="time" name="dari_jam" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Jam Selesai</label>
                            <input type="time" name="sampai_jam" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategori Lembur</label>
                            <select name="kategori" class="form-select" required>
                                <option value="1">Hari Kerja</option>
                                <option value="2">Hari Libur</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Istirahat</label>
                            <select name="istirahat" class="form-select" required>
                                <option value="0">Tidak</option>
                                <option value="1">Ya (Potong 1 Jam)</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Keterangan Lembur</label>
                            <textarea name="keterangan" class="form-control" rows="2" placeholder="Tulis keterangan lembur..." required></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Departemen</label>
                            <select name="kode_dept" id="select_dept" class="form-select" required>
                                <option value="">Pilih Departemen</option>
                                @foreach ($departemen as $d)
                                <option value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Karyawan</label>
                            <div class="form-group border rounded p-2" style="max-height: 200px; overflow-y: auto;" id="list_karyawan">
                                <span class="text-muted">Pilih departemen terlebih dahulu.</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit Lembur --}}
<div class="modal modal-blur fade" id="modal-editlembur" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data Lembur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="frmEditLembur">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Lembur</label>
                            <input type="text" id="edit_tanggal" name="tanggal" class="form-control datepicker" placeholder="YYYY-MM-DD" autocomplete="off" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Jam Mulai</label>
                            <input type="time" id="edit_dari_jam" name="dari_jam" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Jam Selesai</label>
                            <input type="time" id="edit_sampai_jam" name="sampai_jam" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategori Lembur</label>
                            <select name="kategori" id="edit_kategori" class="form-select" required>
                                <option value="1">Hari Kerja</option>
                                <option value="2">Hari Libur</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Istirahat</label>
                            <select name="istirahat" id="edit_istirahat" class="form-select" required>
                                <option value="0">Tidak</option>
                                <option value="1">Ya (Potong 1 Jam)</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Keterangan Lembur</label>
                            <textarea name="keterangan" id="edit_keterangan" class="form-control" rows="2" required></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Departemen</label>
                            <select name="kode_dept" id="edit_select_dept" class="form-select" required>
                                <option value="">Pilih Departemen</option>
                                @foreach ($departemen as $d)
                                <option value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Karyawan</label>
                            <div class="form-group border rounded p-2" style="max-height: 200px; overflow-y: auto;" id="edit_list_karyawan">
                                <span class="text-muted">Pilih departemen terlebih dahulu.</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
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

        $("#btnTambahLembur").click(function() {
            $("#modal-inputlembur").modal("show");
        });

        // Fetch employees dynamically based on department
        function fetchKaryawan(kode_dept, target_div, selected_niks = []) {
            if (kode_dept != "") {
                $.ajax({
                    type: 'POST',
                    url: '/lembur/getkaryawanbydept',
                    data: {
                        _token: "{{ csrf_token() }}",
                        kode_dept: kode_dept
                    },
                    success: function(response) {
                        var html = "";
                        if (response.length > 0) {
                            response.forEach(function(k) {
                                var is_checked = selected_niks.includes(k.nik) ? 'checked' : '';
                                html += '<div class="form-check">';
                                html += '  <input class="form-check-input" type="checkbox" name="nik[]" value="' + k.nik + '" id="chk_' + k.nik + '" ' + is_checked + '>';
                                html += '  <label class="form-check-label" for="chk_' + k.nik + '">';
                                html += '    ' + k.nama_lengkap + ' (' + k.nik + ')';
                                html += '  </label>';
                                html += '</div>';
                            });
                        } else {
                            html = '<span class="text-danger">Tidak ada karyawan di departemen ini.</span>';
                        }
                        $(target_div).html(html);
                    }
                });
            } else {
                $(target_div).html('<span class="text-muted">Pilih departemen terlebih dahulu.</span>');
            }
        }

        $("#select_dept").change(function() {
            fetchKaryawan($(this).val(), "#list_karyawan");
        });

        $("#edit_select_dept").change(function() {
            fetchKaryawan($(this).val(), "#edit_list_karyawan");
        });

        // Edit button handler
        $(".edit").click(function(e) {
            e.preventDefault();
            var kode_lembur = $(this).attr("kode_lembur");
            $.ajax({
                type: 'POST',
                url: '/lembur/edit',
                data: {
                    _token: "{{ csrf_token() }}",
                    kode_lembur: kode_lembur
                },
                success: function(response) {
                    var l = response.lembur;
                    var assigned = response.assigned_niks;

                    // Populate fields
                    $("#edit_tanggal").val(l.tanggal);
                    $("#edit_dari_jam").val(l.tanggal_dari.substring(11, 16));
                    $("#edit_sampai_jam").val(l.tanggal_sampai.substring(11, 16));
                    $("#edit_kategori").val(l.kategori);
                    $("#edit_istirahat").val(l.istirahat);
                    $("#edit_keterangan").val(l.keterangan);
                    $("#edit_select_dept").val(l.kode_dept);

                    // Form action URL
                    $("#frmEditLembur").attr("action", "/lembur/" + l.kode_lembur + "/update");

                    // Load employees checkbox with checked state
                    fetchKaryawan(l.kode_dept, "#edit_list_karyawan", assigned);

                    $("#modal-editlembur").modal("show");
                }
            });
        });

        // Delete confirmation
        $(".delete-confirm").click(function(e) {
            var form = $(this).closest('form');
            e.preventDefault();
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Data lembur ini akan dihapus secara permanen!",
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
