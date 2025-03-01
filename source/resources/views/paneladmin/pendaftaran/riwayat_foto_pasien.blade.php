@extends('paneladmin.templateadmin')
@section('konten_utama_admin')
<div class="row default-dashboard">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header">
          @include('komponen.information_user', ['title_card' => "Foto Diri", 'informasi_apa' => "informasi foto peserta"])
        </div>
        <div class="card-body">
          <div class="row formulir_group">
            <div class="col-md-6">
              <h3>Citra Sebelum</h3>
              <button id="ambil_dari_webcame" class="btn btn-warning mt-2 mb-2 w-100"><i class="fa fa-camera"></i> Ambil Dari Device</button>
              <input type="file" id="citra_pasien" class="form-control mt-3 mb-3" accept="image/*">
              <div id="cropper-container">
                <div id="panggil_webcame" style="display:none">
                  <video id="webcam-preview" autoplay playsinline></video>
                  <button class="btn btn-primary w-100 mt-2 mb-2" id="tangkap_citra_cropper_js"> Tangkap Citra </button>
                </div>
                <div id="citra_proses_crop"><img id="tampilan_citra_unggahan" style="display: none;"></div>
              </div>
            </div>
            <div class="col-md-6">
              <h3>Citra Sesudah Dengan AR 1:1</h3>
              <button id="crop-btn" class="btn btn-primary w-100 mt-2 mb-2"> Potong Foto </button>
              <div id="preview_citra_pasien_canvas" class="d-flex justify-content-center align-items-center bg-light" >
                  <canvas id="preview_citra_pasien"></canvas>
              </div>
            </div>
          </div>
          <button class="btn w-100 btn-primary mt-3 formulir_group formulir_group_button" id="simpan_foto_perserta"><i class="fa fa-save"></i> Simpan Foto</button>
          @if (isset($data['dataNavigasi']))
            @include('komponen.navigasi_riwayat_informasi', $data['dataNavigasi'])
          @endif
        </div>
        <div class="card-footer">
          <input type="text" class="form-control" id="kotak_pencarian_daftarpeserta" placeholder="Cari data berdasarkan nama peserta">
          <div class="table">
            <table class="table display" id="datatables_daftarpeserta_unggah_citra"></table>
          </div>
        </div>
      </div>
    </div>
</div>
<div class="modal fade" id="modalLihatFoto" tabindex="-1" role="dialog" aria-labelledby="modalLihatFotoLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="modalTambahPenggunaLabel">Foto Pasien MCU Artha Medica Clinic</h5>
              <i class="fa fa-times" data-bs-dismiss="modal" style="cursor: pointer;"></i>
          </div>
          <div class="modal-body">
              <img id="foto_lihat" class="rounded img-thumbnail mx-auto d-block">
          </div>
          <div class="modal-footer">
              <h5>Nama Peserta : <span id="nama_peserta_foto"></span></h5>
          </div>
      </div>
  </div>
</div>
@endsection
@section('css_load')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css" integrity="sha512-UtLOu9C7NuThQhuXXrGwx9Jb/z9zPQJctuAgNUBK3Z6kkSYT9wJ+2+dh6klS+TDBCV9kNPBbAxbVD+vCcfGPaA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
#cropper-container {
    padding: 0px; /* Add padding if needed */
    background-color: #ffffff;
    border-radius: 5px; /* Optional: to make the edges rounded */
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); /* Optional: add shadow for a better look */
    overflow: hidden;
}
#tampilan_citra_unggahan {
    display: block;
    max-width: 100%; /* Pastikan gambar tidak melampaui kontainer */
    max-height: 100%; /* Atur agar gambar sesuai kontainer */
    object-fit: cover; /* Isi seluruh div tanpa area abu-abu */
}
#preview_citra_pasien {
    display: none; /* Tampilkan hanya saat diperlukan */
    max-width: 100%;
    object-fit: cover;
    border-radius: 5px;
}
#webcam-preview {
  object-fit: fill;
  width: 100%;
  height: 100%;

}
</style>
@endsection
@section('js_load')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js" integrity="sha512-JyCZjCOZoyeQZSd5+YEAcFgz2fowJ1F1hyJOXgtKu4llIa0KneLcidn5bwfutiehUTiOuK87A986BZJMko0eWQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('vendor/erayadigital/riwayat/foto_pasien.js') }}"></script>
<script>
  let param_nomor_identitas = '{{$data['nomor_identitas']}}'
  let param_nama_peserta = '{{$data['nama_peserta']}}'
</script>
@endsection
