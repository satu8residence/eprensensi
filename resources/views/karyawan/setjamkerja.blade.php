<div class="row">
    <div class="col-12">
        <table class="table table-striped table-bordered mb-3">
            <tr>
                <th width="150">NIK</th>
                <td>{{ $karyawan->nik }}</td>
            </tr>
            <tr>
                <th>NAMA</th>
                <td>{{ $karyawan->nama_lengkap }}</td>
            </tr>
            <tr>
                <th>DEPARTEMEN</th>
                <td>{{ $karyawan->nama_dept }}</td>
            </tr>
            <tr>
                <th>CABANG</th>
                <td>{{ $karyawan->nama_cabang ?? 'Kantor Pusat' }}</td>
            </tr>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
            <li class="nav-item">
                <a href="#tabs-daily" class="nav-link active" data-bs-toggle="tab">Set Jam Kerja</a>
            </li>
            <li class="nav-item">
                <a href="#tabs-date" class="nav-link" data-bs-toggle="tab">Set Jam Kerja By Date</a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content">
            <!-- Tab 1: Daily Shift Config -->
            <div class="tab-pane active show" id="tabs-daily">
                <form action="/karyawan/{{ $karyawan->nik }}/setjamkerja/store" method="POST" id="frmSetDaily">
                    @csrf
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>HARI</th>
                                <th>JAM KERJA</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                            @endphp
                            @foreach ($days as $day)
                            <tr>
                                <td>
                                    {{ $day }}
                                    <input type="hidden" name="hari[]" value="{{ $day }}">
                                </td>
                                <td>
                                    <select name="kode_jam_kerja[]" class="form-select">
                                        <option value="">Pilih Jam Kerja</option>
                                        @foreach ($jamkerja as $j)
                                        <option value="{{ $j->kode_jam_kerja }}" 
                                            {{ (isset($daily_schedules[$day]) && $daily_schedules[$day] == $j->kode_jam_kerja) ? 'selected' : '' }}>
                                            {{ $j->nama_jam_kerja }} ({{ date('H:i', strtotime($j->jam_masuk)) }} - {{ date('H:i', strtotime($j->jam_pulang)) }})
                                        </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="row mt-3">
                        <div class="col-12">
                            <button class="btn btn-primary w-100" type="submit">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-send" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M10 14l11 -11"></path>
                                    <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5"></path>
                                </svg>
                                Update Jam Kerja
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tab 2: Custom Date Shift Config -->
            <div class="tab-pane" id="tabs-date">
                <form action="/karyawan/{{ $karyawan->nik }}/setjamkerja/storebydate" method="POST" class="mb-4">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="form-label">Pilih Tanggal</label>
                                <input type="text" id="tanggal_khusus" name="tanggal" class="form-control" placeholder="YYYY-MM-DD" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="form-label">Pilih Jam Kerja / Shift</label>
                                <select name="kode_jam_kerja" class="form-select" required>
                                    <option value="">Pilih Jam Kerja</option>
                                    @foreach ($jamkerja as $j)
                                    <option value="{{ $j->kode_jam_kerja }}">
                                        {{ $j->nama_jam_kerja }} ({{ date('H:i', strtotime($j->jam_masuk)) }} - {{ date('H:i', strtotime($j->jam_pulang)) }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-success w-100">Tambah</button>
                        </div>
                    </div>
                </form>

                <h4 class="card-title mt-3">Jadwal Khusus Karyawan</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Jam Kerja / Shift</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($date_schedules as $d)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ date('d-m-Y', strtotime($d->tanggal)) }}</td>
                                <td>{{ $d->nama_jam_kerja }} ({{ date('H:i', strtotime($d->jam_masuk)) }} - {{ date('H:i', strtotime($d->jam_pulang)) }})</td>
                                <td>
                                    <form action="/karyawan/setjamkerja/{{ $d->id }}/deletebydate" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal khusus tanggal ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm">
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
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada jadwal khusus berdasarkan tanggal.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        $("#tanggal_khusus").datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'yyyy-mm-dd'
        });
    });
</script>
