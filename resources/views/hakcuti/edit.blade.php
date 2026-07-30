<form action="/hakcuti/{{ $hakcuti->id }}/update" method="POST" id="frmHakCutiEdit">
    @csrf
    <div class="row">
        <div class="col-12">
            <div class="form-group mb-3">
                <label class="form-label">Karyawan</label>
                <select name="nik" id="nik_edit" class="form-select">
                    <option value="">Pilih Karyawan</option>
                    @foreach ($karyawan as $k)
                        <option value="{{ $k->nik }}" {{ $hakcuti->nik == $k->nik ? 'selected' : '' }}>{{ $k->nik }} - {{ $k->nama_lengkap }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group mb-3">
                <label class="form-label">Jenis Cuti</label>
                <select name="kode_cuti" id="kode_cuti_edit" class="form-select">
                    <option value="">Pilih Jenis Cuti</option>
                    @foreach ($jeniscuti as $jc)
                        <option value="{{ $jc->kode_cuti }}" {{ $hakcuti->kode_cuti == $jc->kode_cuti ? 'selected' : '' }}>{{ $jc->nama_cuti }} (Bawaan: {{ $jc->jml_hari }} Hari)</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group mb-3">
                <label class="form-label">Tahun</label>
                <select name="tahun" id="tahun_edit" class="form-select">
                    <option value="">Pilih Tahun</option>
                    @php
                    $tahunskrg = date("Y");
                    @endphp
                    @for ($t=$tahunskrg-1; $t<=$tahunskrg+2; $t++)
                        <option value="{{ $t }}" {{ $hakcuti->tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group mb-3">
                <label class="form-label">Jumlah Hari Cuti (Quota Kustom)</label>
                <input type="number" id="jml_hari_edit" value="{{ $hakcuti->jml_hari }}" class="form-control" name="jml_hari" placeholder="Contoh: 12">
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary w-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-send" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M10 14l11 -11"></path>
                        <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5"></path>
                    </svg>
                    Simpan
                </button>
            </div>
        </div>
    </div>
</form>

<script>
    $(function() {
        $("#frmHakCutiEdit").submit(function() {
            var nik = $("#nik_edit").val();
            var kode_cuti = $("#kode_cuti_edit").val();
            var tahun = $("#tahun_edit").val();
            var jml_hari = $("#jml_hari_edit").val();
            
            if (nik == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Karyawan Harus Dipilih !',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                });
                return false;
            } else if (kode_cuti == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Jenis Cuti Harus Dipilih !',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                });
                return false;
            } else if (tahun == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Tahun Harus Dipilih !',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                });
                return false;
            } else if (jml_hari == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Jumlah Hari Harus Diisi !',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    $("#jml_hari_edit").focus();
                });
                return false;
            }
        });
    });
</script>
