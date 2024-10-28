@extends('paneladmin.templateadmin')
@section('konten_utama_admin')
<div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header">
          <h4>Daftar Pengguna Aplikasi MCU</h4><span>Silahkan kelola pengguna aplikasi MCU disini. Anda dapat menambahkan, mengubah, dan menghapus atau bahkan mengaktifkan pengguna aplikasi MCU.</span>
        </div>
        <div class="card-body">
          <button class="btn btn-primary w-100 mb-3" id="tambah_penggunaaplikasi"><i class="fa fa-user-plus"></i> Tambah Pengguna</button>
          <div class="col-md-12">
            <input type="text" class="form-control" id="kotak_pencarian_penggunaaplikasi" placeholder="Cari data berdasarkan nama hak akses">
            <div class="table">
              <table class="table display" id="datatables_penggunaaplikasi"></table>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
<!-- Modal Tambah Pengguna -->
<div class="modal modal-lg fade" id="modalTambahPengguna" tabindex="-1" role="dialog" aria-labelledby="modalTambahPenggunaLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTambahPenggunaLabel">Formulir Data Informasi Pengguna</h5>
      </div>
      <div class="modal-body">
      <form id="form_pendaftaran" class="row g-3 needs-validation custom-input" novalidate>
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 pb-2 p-0">
          <h5>Isikan Dengan Benar dan Akurat :</h5>
          <ul class="nav nav-pills nav-warning" id="j-pills-tab" role="tablist">
            <li class="nav-item"><a class="nav-link active" id="j-pills-web-designer-tab" data-bs-toggle="pill" href="#j-pills-web-designer" role="tab" aria-controls="j-pills-web-designer" aria-selected="true">Kredential</a></li>
            <li class="nav-item"><a class="nav-link" id="j-pills-UX-designer-tab" data-bs-toggle="pill" href="#j-pills-UX-designer" role="tab" aria-controls="j-pills-UX-designer" aria-selected="false">Detail Pengguna</a></li>
            <li class="nav-item"><a class="nav-link" id="j-pills-IOT-developer-tab" data-bs-toggle="pill" href="#j-pills-IOT-developer" role="tab" aria-controls="j-pills-IOT-developer" aria-selected="false">Keterangan</a></li>
          </ul>
        </div>
        <div class="card-body px-0 pb-0"> 
          <div class="tab-content" id="j-pills-tabContent">
          <div class="tab-pane fade active show" id="j-pills-web-designer" role="tabpanel" aria-labelledby="j-pills-web-designer-tab">
              <div class="row"> 
                <div class="col"> 
                  <div class="mb-3 row"> 
                    <label class="col-sm-3 mt-2">Nama Pengguna</label>
                    <div class="col-sm-9">
                      <input class="form-control" maxlength="255" minlength="8" id="floatingInputValue" type="text" placeholder="Contoh : erayadigitalsolusindo" value="" required>
                      <div class="invalid-feedback">Nama pengguna wajib diisi, minimal 8 karakter dan maksimal 255 karakter</div>
                      <div class="valid-feedback">Terlihat bagus! Formatnya sudah benar.</div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row"> 
                <div class="col"> 
                  <div class="mb-3 row"> 
                    <label class="col-sm-3 mt-2">Alamat Surel</label>
                    <div class="col-sm-9">
                      <input class="form-control" id="email" type="email" placeholder="Contoh : hai@erayadifital.co.id" value="" required>
                      <div class="invalid-feedback">Masukan alamat surel yang valid</div>
                      <div class="valid-feedback">Terlihat bagus! Format surel sudah valid!</div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row"> 
                <div class="col"> 
                  <div class="mb-3 row"> 
                    <label class="col-sm-3 mt-2">Katasandi</label>
                    <div class="col-sm-9">
                      <div class="input-group">
                        <input class="form-control" minlength="8" id="katasandi" type="password" placeholder="Buatlah katasandi yang sulit ditebak dan mudah diingat" value="" required>
                        <button class="btn btn-outline-primary" type="button" id="toogleshowpassword"><i class="fa fa-eye"></i></button>
                        <div class="invalid-feedback">Katasandi wajib diisi, minimal 8 karakter</div>
                        <div class="valid-feedback">Terlihat bagus!Katasandi sudah terisi</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row"> 
                <div class="col"> 
                  <div class="mb-3 row"> 
                    <label class="col-sm-3 mt-2">Hak Akses Pengguna</label>
                    <div class="col-sm-9">
                      <select id="select2_hak_akses" required></select>
                      <div class="invalid-feedback" style="margin-top: -18px;">Pilih hak akses yang valid</div>
                      <div class="valid-feedback" style="margin-top: -18px;">Terlihat bagus! Hak akses sudah dipilih</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane fade" id="j-pills-UX-designer" role="tabpanel" aria-labelledby="j-pills-UX-designer-tab">
              <div class="row"> 
                <div class="col"> 
                  <div class="mb-3 row"> 
                    <label class="col-sm-3 mt-2">Nama Pegawai</label>
                    <div class="col-sm-9">
                      <input class="form-control" id="nama_pegawai" type="text" placeholder="Contoh : Mochamad Aries Setyawan" value="" required>
                      <div class="invalid-feedback">Masukan nama pegawai yang valid</div>
                      <div class="valid-feedback">Terlihat bagus! Nama pegawai sudah terisi</div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <div class="mb-3 row">
                    <label class="col-sm-3 mt-2">NIP</label>
                    <div class="col-sm-9">
                      <input class="form-control" id="nip" type="text" placeholder="Nomor Induk Pegawai" value="" required>
                      <div class="invalid-feedback">Masukan NIP yang valid</div>
                      <div class="valid-feedback">Terlihat bagus! NIP sudah terisi</div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <div class="mb-3 row">
                    <label class="col-sm-3 mt-2">Jabatan</label>
                    <div class="col-sm-9">
                      <input class="form-control" id="jabatan" type="text" placeholder="Jabatan Pegawai" value="" required>
                      <div class="invalid-feedback">Masukan jabatan yang valid</div>
                      <div class="valid-feedback">Terlihat bagus! Jabatan sudah terisi</div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <div class="mb-3 row">
                    <label class="col-sm-3 mt-2">Departemen</label>
                    <div class="col-sm-9">
                      <input class="form-control" id="departemen" type="text" placeholder="Departemen Pegawai" value="" required>
                      <div class="invalid-feedback">Masukan departemen yang valid</div>
                      <div class="valid-feedback">Terlihat bagus! Departemen sudah terisi</div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <div class="mb-3 row">
                    <label class="col-sm-3 mt-2">Tanggal Lahir</label>
                    <div class="col-sm-9">
                      <input class="form-control" id="tanggal_lahir" type="text" placeholder="dd-mm-yyyy" required>
                      <div class="invalid-feedback">Masukan tanggal lahir yang valid</div>
                      <div class="valid-feedback">Terlihat bagus! Tanggal lahir sudah terisi</div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <div class="mb-3 row">
                    <label class="col-sm-3 mt-2">Tanggal Diterima</label>
                    <div class="col-sm-9">
                      <input class="form-control" id="tanggal_diterima" type="text" placeholder="dd-mm-yyyy" required>
                      <div class="invalid-feedback">Masukan tanggal diterima yang valid</div>
                      <div class="valid-feedback">Terlihat bagus! Tanggal diterima sudah terisi</div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <div class="mb-3 row">
                    <label class="col-sm-3 mt-2">Jenis Kelamin</label>
                    <div class="col-sm-9">
                      <select class="form-control" id="jenis_kelamin" placeholder="Pilih Jenis Kelamin">
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                        <option value="Alien">Alien</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <div class="mb-3 row">
                    <label class="col-sm-3 mt-2">Alamat</label>
                    <div class="col-sm-9">
                      <textarea class="form-control" id="alamat" rows="3" placeholder="Alamat tempat tinggal sekarang agar mudah dihubungi" required></textarea>
                      <div class="invalid-feedback">Masukan alamat yang valid</div>
                      <div class="valid-feedback">Terlihat bagus! Alamat sudah terisi</div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <div class="mb-3 row">
                    <label class="col-sm-3 mt-2">No. Telepon</label>
                    <div class="col-sm-9">
                      <input class="form-control" id="no_telepon" type="tel" placeholder="Nomor Telepon Pegawai" value="" required>
                      <div class="invalid-feedback">Masukan no telepon yang valid</div>
                      <div class="valid-feedback">Terlihat bagus! No telepon sudah terisi</div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <div class="mb-3 row">
                    <label class="col-sm-3 mt-2">Status Pegawai</label>
                    <div class="col-sm-9">
                      <select class="form-control" id="status_pegawai" placeholder="Pilih Status Pegawai">
                        <option value="Tetap">Tetap</option>
                        <option value="Kontrak">Kontrak</option>
                        <option value="Magang">Magang</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane fade" id="j-pills-IOT-developer" role="tabpanel" aria-labelledby="j-pills-IOT-developer-tab">
                <p class="mb-0">
                  <ol type="1">
                      <li>Informasi pada tab "Kredensial" wajib diisi semua. Data ini digunakan untuk masuk ke dalam sistem Artha Medica MCU.</li>
                      <li>Informasi pada tab "Kredensial" tersinkronisasi dengan aplikasi Artha Medica MCU Mobile. Anda dapat mendaftarkan pengguna baru melalui halaman ini.</li>
                      <li>Penambahan kredensial aplikasi hanya dapat dilakukan melalui aplikasi website pada menu ini untuk alasan keamanan dan sifat internal aplikasi.</li>
                      <li>Informasi pada tab "Profil" tidak wajib diisi semua. Isi sesuai dengan informasi yang dapat Anda berikan. Informasi profil digunakan untuk identitas pada log pemeriksaan MCU.</li>
                      <li>Pastikan untuk mengisi data dengan akurat, terutama untuk informasi medis yang relevan dengan MCU.</li>
                  </ol>  
                </p>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Batal</button>
        <button type="submit" class="btn btn-primary" id="btnSimpanPengguna"><i class="fa fa-save"></i> Simpan Data</button>
      </div>
      </form>
    </div>
  </div>
</div>
@endsection
@section('css_load')
@component('komponen.css.datatables')
@endcomponent
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<style>
.select2-container--default .select2-selection--single .select2-selection__arrow {
    margin-top: 10px;
    margin-right: 10px;
}
.select2-container--open .select2-dropdown--below {
  margin-top: -20px;
  border-top-left-radius:2;
  border-top-right-radius:2;
}
</style>
@endsection
@section('js_load')
@component('komponen.js.datatables')
@endcomponent
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="{{asset('mofi/assets/js/system/user.js')}}"></script>
@endsection