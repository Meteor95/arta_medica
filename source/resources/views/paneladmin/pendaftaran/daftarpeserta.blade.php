@extends('paneladmin.templateadmin')
@section('konten_utama_admin')
<div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header">
          <h4>Daftar Peserta</h4><span>Peserta yang terdaftar pada sistem dan belum mendapatkan jadwal. Informasi yang ditampikan berasal dari pendataran via website atau pendaftaran langsung yang datang ke lokasi MCU Arta Medica Clinic. Daftar peserta akan dihapus secara otomatis setelah 7 hari dari tanggal pendaftaran agar tidak menyebabkan duplikasi data.</span>
          <a href="{{ route('admin.pendaftaran.formulir_tambah_peserta') }}" class="btn btn-success w-100 mt-2" id="btn_tambahpeserta">Tambah Peserta MCU</a>
        </div>
        <div class="card-body">
          <input type="text" class="form-control" id="kotak_pencarian_daftarpeserta" placeholder="Cari informasi berdasarkan No Pemesanan, Identifikasi atau Nama Peserta">
          <table class="table display" id="datatables_daftarpeserta"></table>
        </div>
      </div>
    </div>
</div>
@endsection
@section('css_load')
<style>
.dtfc-fixed-right {
    background-color: #f6f6f6 !important;
}
.dtfc-fixed-right_header {
    background-color: #ffffff !important;
}
body.dark-only .dtfc-fixed-right_header {
    background-color: #2a3650 !important;
}
</style>
@endsection
@section('js_load')
<script src="https://cdn.datatables.net/fixedcolumns/4.0.2/js/dataTables.fixedColumns.min.js"></script>
<script src="{{ asset('vendor/erayadigital/pendaftaran/peserta.js') }}"></script>
@endsection
