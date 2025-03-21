<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Perusahaan, PaketMCU};
use App\Models\Komponen\{Poli,Antrian};
use App\Models\Masterdata\{Jasalayanan, DepartemenPerusahaan, MemberMCU, DaftarBank};
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
class MasterdataController extends Controller
{
    /* Komponen */
    public function getpoli(Request $request)
    {
        try {
            $poli = Poli::all();
            $dynamicAttributes = [  
                'data' => $poli,
            ];
            return ResponseHelper::data(__('common.data_ready', ['namadata' => 'Daftar Poli']), $dynamicAttributes);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
    /* Master Data Perusahaan */
    public function getperusahaan(Request $request)
    {
        try {
            $perHalaman = (int) $request->length > 0 ? (int) $request->length : 1;
            $nomorHalaman = (int) $request->start / $perHalaman;
            $offset = $nomorHalaman * $perHalaman; 
            $datatabel = Perusahaan::listPerusahaan($request, $perHalaman, $offset);
            $jumlahdata = $datatabel['total'];
            $dynamicAttributes = [
                'data' => $datatabel['data'],
                'recordsFiltered' => $jumlahdata,
                'pages' => [
                    'limit' => $perHalaman,
                    'offset' => $offset,
                ],
            ];
            return ResponseHelper::data(__('common.data_ready', ['namadata' => 'Informasi Perusahaan']), $dynamicAttributes);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
    public function saveperusahaan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_code' => 'required|string',
            'company_name' => 'required|string',
            'alamat' => 'required|string',
        ]);
        if ($validator->fails()) {
            $dynamicAttributes = ['errors' => $validator->errors()];
            return ResponseHelper::error_validation(__('auth.eds_required_data'), $dynamicAttributes);
        }
        try {
            Perusahaan::create(
                [
                    'company_code' => $request->company_code,
                    'company_name' => $request->company_name,
                    'alamat' => $request->alamat,
                    'keterangan' => $request->keterangan,
                ]
            );
            $dynamicAttributes = [  
                'message' => 'Informasi Perusahaan berhasil disimpan',
            ];
            return ResponseHelper::data(
                __('common.data_saved', ['namadata' => 'Informasi perusahaan dengan nama ' . $request->company_name . ' berhasil disimpan. Silahkan']), 
                $dynamicAttributes
            );
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
    public function deleteperusahaan(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                $dynamicAttributes = ['errors' => $validator->errors()];
                return ResponseHelper::error_validation(__('auth.eds_required_data'), $dynamicAttributes);
            }
            Perusahaan::where('id', $request->id)->delete();
            return ResponseHelper::success_delete("Informasi Perusahaan dengan nama " . $request->nama . " berhasil dihapus beserta seluruh data yang terkait dengan perusahaan ini.");
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
    public function editperusahaan(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
                'company_code' => 'required|string',
                'company_name' => 'required|string',
                'alamat' => 'required|string',
            ]);
            if ($validator->fails()) {
                $dynamicAttributes = ['errors' => $validator->errors()];
                return ResponseHelper::error_validation(__('auth.eds_required_data'), $dynamicAttributes);
            }
            Perusahaan::where('id', $request->id)->update([
                'company_code' => $request->company_code,
                'company_name' => $request->company_name,
                'alamat' => $request->alamat,
                'keterangan' => $request->keterangan,
            ]);
            return ResponseHelper::success("Informasi Perusahaan dengan nama " . $request->company_name . " berhasil diubah.");
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
    /* Master Data Paket MCU */
    public function getpaketmcu(Request $req)
    {
        try {
            $perHalaman = (int) $req->length > 0 ? (int) $req->length : 0;
            $nomorHalaman = (int) $req->start / $perHalaman;
            $offset = $nomorHalaman * $perHalaman; 
            $datatabel = PaketMCU::listPaketMcu($req, $perHalaman, $offset);
            $jumlahdata = $datatabel['total'];
            $dynamicAttributes = [
                'data' => $datatabel['data'],
                'recordsFiltered' => $jumlahdata,
            ];
            return ResponseHelper::data(__('common.data_ready', ['namadata' => 'Informasi paket tersedia di MCU '.config('app.name')]), $dynamicAttributes);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
    public function getpaketmcu_non_dt(Request $req)
    {
        try {
            $paket = PaketMCU::where('id', '>', 1)->get();
            $dynamicAttributes = [
                'data' => $paket,
            ];
            return ResponseHelper::data('Informasi paket MCU tersedia', $dynamicAttributes);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
    public function savepaketmcu(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'kode_paket' => 'required|string',
                'nama_paket' => 'required|string',
                'harga_paket' => 'required|integer',
                'keterangan' => 'required|string',
                'selected_items' => 'required|array',
            ]);
            if ($validator->fails()) {
                $dynamicAttributes = ['errors' => $validator->errors()];
                return ResponseHelper::error_validation(__('auth.eds_required_data'), $dynamicAttributes);
            }
            PaketMCU::create([
                'kode_paket' => $request->kode_paket,
                'nama_paket' => $request->nama_paket,
                'harga_paket' => $request->harga_paket,
                'keterangan' => $request->keterangan,
                'akses_tindakan' => json_encode($request->selected_items),
            ]);
            return ResponseHelper::success("Informasi paket MCU berhasil disimpan. Silahkan tentukan pada perusahaan mana paket MCU ini akan digunakan.");
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
    public function deletepaketmcu(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
                'nama_paket' => 'required|string',
            ]);
            if ($validator->fails()) {
                $dynamicAttributes = ['errors' => $validator->errors()];
                return ResponseHelper::error_validation(__('auth.eds_required_data'), $dynamicAttributes);
            }
            PaketMCU::where('id', $request->id)->delete();
            return ResponseHelper::success_delete("Informasi paket MCU dengan nama " . $request->nama_paket . " berhasil dihapus beserta seluruh data yang terkait dengan paket MCU ini secara visual di sistem.");
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
    public function editpaketmcu(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
                'selected_items' => 'required|array',
            ]);
            if ($validator->fails()) {
                $dynamicAttributes = ['errors' => $validator->errors()];
                return ResponseHelper::error_validation(__('auth.eds_required_data'), $dynamicAttributes);
            }
            PaketMCU::where('id', $request->id)->update([
                'kode_paket' => $request->kode_paket,
                'nama_paket' => $request->nama_paket,
                'harga_paket' => $request->harga_paket,
                'keterangan' => $request->keterangan,
                'akses_tindakan' => json_encode($request->selected_items),
            ]);
            return ResponseHelper::success("Informasi dari paket MCU " . $request->nama_paket . " berhasil diubah.");
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
    /* Master Data Jasa Pelayanan */
    public function getjasa(Request $req)
    {
        try {
            $perHalaman = (int) $req->length > 0 ? (int) $req->length : 0;
            $nomorHalaman = (int) $req->start / $perHalaman;
            $offset = $nomorHalaman * $perHalaman; 
            $datatabel = Jasalayanan::listJasaPelayanan($req, $perHalaman, $offset);
            $jumlahdata = $datatabel['total'];
            $dynamicAttributes = [
                'data' => $datatabel['data'],
                'recordsFiltered' => $jumlahdata,
            ];
            return ResponseHelper::data(__('common.data_ready', ['namadata' => 'Informasi fee atau nominal jasa pelayanan tersedia di MCU '.config('app.name')]), $dynamicAttributes);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
    public function getjasa_laboratorium(Request $req)
    {
        try {
            $jasa = Jasalayanan::where('kategori_layanan', $req->grup_item)->get();
            $dynamicAttributes = [
                'data' => $jasa,
                'recordsFiltered' => $jasa->count(),
            ];
            return ResponseHelper::data('Informasi jasa laboratorium tersedia', $dynamicAttributes);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
    public function savejasa(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'kode_jasa_pelayanan' => 'required|string',
                'nama_jasa_pelayanan' => 'required|string',
                'nominal_layanan' => 'required|integer',
                'kategori_layanan' => 'required|string',
            ]);
            if ($validator->fails()) {
                $dynamicAttributes = ['errors' => $validator->errors()];
                return ResponseHelper::error_validation(__('auth.eds_required_data'), $dynamicAttributes);
            }
            Jasalayanan::create([
                'kode_jasa_pelayanan' => $request->kode_jasa_pelayanan,
                'nama_jasa_pelayanan' => $request->nama_jasa_pelayanan,
                'nominal_layanan' => $request->nominal_layanan,
                'kategori_layanan' => $request->kategori_layanan,
            ]);
            return ResponseHelper::success("Informasi jasa pelayanan berhasil disimpan. Silahkan tentukan pada perusahaan mana jasa pelayanan ini akan digunakan.");
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }   
    public function deletejasa(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                $dynamicAttributes = ['errors' => $validator->errors()];
                return ResponseHelper::error_validation(__('auth.eds_required_data'), $dynamicAttributes);
            }
            Jasalayanan::where('id', $request->id)->delete();
            return ResponseHelper::success_delete("Informasi jasa pelayanan dengan kode " . $request->kode_jasa_pelayanan . " berhasil dihapus beserta seluruh data yang terkait dengan jasa pelayanan ini secara visual di sistem.");
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }   
    public function editjasa(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                $dynamicAttributes = ['errors' => $validator->errors()];
                return ResponseHelper::error_validation(__('auth.eds_required_data'), $dynamicAttributes);
            }
            Jasalayanan::where('id', $request->id)->update([
                'kode_jasa_pelayanan' => $request->kode_jasa_pelayanan,
                'nama_jasa_pelayanan' => $request->nama_jasa_pelayanan,
                'nominal_layanan' => $request->nominal_layanan,
                'kategori_layanan' => $request->kategori_layanan,
            ]);
            return ResponseHelper::success("Informasi jasa pelayanan dengan kode " . $request->kode_jasa_pelayanan . " berhasil diubah.");
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
    /* Master Data Departemen Peserta */
    public function getdepartemenpeserta(Request $req)
    {
        try {
            $perHalaman = (int) $req->length > 0 ? (int) $req->length : 1;
            $nomorHalaman = (int) $req->start / $perHalaman;
            $offset = $nomorHalaman * $perHalaman; 
            $datatabel = DepartemenPerusahaan::listGetDepartemen($req, $perHalaman, $offset);
            $jumlahdata = $datatabel['total'];
            $dynamicAttributes = [
                'data' => $datatabel['data'],
                'recordsFiltered' => $jumlahdata,
            ];
            return ResponseHelper::data(__('common.data_ready', ['namadata' => 'Informasi departemen peserta']), $dynamicAttributes);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
    public function savedepartemenpeserta(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'kode_departemen' => 'required|string',
                'nama_departemen' => 'required|string',
                'keterangan' => 'required|string',
            ]);
            if ($validator->fails()) {
                $dynamicAttributes = ['errors' => $validator->errors()];
                return ResponseHelper::error_validation(__('auth.eds_required_data'), $dynamicAttributes);
            }
            DepartemenPerusahaan::create([
                'kode_departemen' => $request->kode_departemen,
                'nama_departemen' => $request->nama_departemen,
                'keterangan' => $request->keterangan,
            ]);
            return ResponseHelper::success("Informasi departemen peserta berhasil disimpan. Silahkan tentukan pada perusahaan mana departemen peserta ini akan digunakan.");
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
    public function editdepartemenpeserta(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                $dynamicAttributes = ['errors' => $validator->errors()];
                return ResponseHelper::error_validation(__('auth.eds_required_data'), $dynamicAttributes);
            }
            DepartemenPerusahaan::where('id', $request->id)->update([
                'kode_departemen' => $request->kode_departemen,
                'nama_departemen' => $request->nama_departemen,
                'keterangan' => $request->keterangan,
            ]);
            return ResponseHelper::success("Informasi departemen peserta dengan kode " . $request->kode_departemen . " berhasil diubah.");
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }   
    public function deletedepartemenpeserta(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                $dynamicAttributes = ['errors' => $validator->errors()];
                return ResponseHelper::error_validation(__('auth.eds_required_data'), $dynamicAttributes);
            }
            DepartemenPerusahaan::where('id', $request->id)->delete();
            return ResponseHelper::success_delete("Informasi departemen peserta dengan kode " . $request->kode_departemen . " berhasil dihapus beserta seluruh data yang terkait dengan departemen peserta ini secara visual di sistem.");
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
    /* Master Data Member MCU */
    public function getmembermcu(Request $req)
    {
        try {
            $perHalaman = (int) $req->length > 0 ? (int) $req->length : 1;
            $nomorHalaman = (int) $req->start / $perHalaman;
            $offset = $nomorHalaman * $perHalaman;
            $data = MemberMcu::listMemberMcu($req, $perHalaman, $offset);
            $jumlahdata = $data['total'];
            $dynamicAttributes = [
                'data' => $data['data'],
                'recordsFiltered' => $jumlahdata,
            ];
            return ResponseHelper::data(__('common.data_ready', ['namadata' => 'Daftar Member MCU']), $dynamicAttributes);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
    public function savemembermcu(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nomor_identitas' => 'required|string',
                'nama_peserta' => 'required|string',
                'tempat_lahir' => 'required|string',
                'tanggal_lahir' => 'required|date',
                'tipe_identitas' => 'required|string',
                'jenis_kelamin' => 'required|string',
                'alamat' => 'required|string',
                'status_kawin' => 'required|string',
                'no_telepon' => 'required|string',
            ]);
            if ($validator->fails()) {
                $dynamicAttributes = ['errors' => $validator->errors()];
                return ResponseHelper::error_validation(__('auth.eds_required_data'), $dynamicAttributes);
            }
            MemberMcu::create([
                'nomor_identitas' => $request->nomor_identitas,
                'nama_peserta' => $request->nama_peserta,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => Carbon::parse($request->tanggal_lahir)->format('Y-m-d'),
                'tipe_identitas' => $request->tipe_identitas,
                'jenis_kelamin' => $request->jenis_kelamin,
                'alamat' => $request->alamat,
                'status_kawin' => $request->status_kawin,
                'no_telepon' => $request->no_telepon,
                'email' => $request->email,
            ]);
            return ResponseHelper::success("Informasi member MCU berhasil disimpan. Silahkan lanjutkan transaksi jikala membutuhkan rekam medis MCU.");
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }   
    }
    public function deletemembermcu(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                $dynamicAttributes = ['errors' => $validator->errors()];
                return ResponseHelper::error_validation(__('auth.eds_required_data'), $dynamicAttributes);
            }
            MemberMcu::where('id', $request->id)->delete();
            return ResponseHelper::success_delete("Informasi member MCU dengan nama " . $request->nama_peserta . " berhasil dihapus beserta seluruh data yang terkait dengan member MCU ini secara visual di sistem.");
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }   
    }   
    public function editmembermcu(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
                'nomor_identitas' => 'required|string',
                'nama_peserta' => 'required|string',
                'tempat_lahir' => 'required|string',
                'tanggal_lahir' => 'required|date',
                'tipe_identitas' => 'required|string',
                'jenis_kelamin' => 'required|string',
                'alamat' => 'required|string',
                'status_kawin' => 'required|string',
                'no_telepon' => 'required|string',
            ]);
            if ($validator->fails()) {
                $dynamicAttributes = ['errors' => $validator->errors()];
                return ResponseHelper::error_validation(__('auth.eds_required_data'), $dynamicAttributes);
            }
            MemberMcu::where('id', $request->id)->update([
                'nomor_identitas' => $request->nomor_identitas,
                'nama_peserta' => $request->nama_peserta,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => Carbon::parse($request->tanggal_lahir)->format('Y-m-d'),
                'tipe_identitas' => $request->tipe_identitas,
                'jenis_kelamin' => $request->jenis_kelamin,
                'alamat' => $request->alamat,
                'status_kawin' => $request->status_kawin,
                'no_telepon' => $request->no_telepon,
                'email' => $request->email,
            ]);
            return ResponseHelper::success("Informasi member MCU dengan nama " . $request->nama_peserta . " berhasil diubah.");
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
    public function getbank(Request $req){
        try {
            $perHalaman = (int) $req->length > 0 ? (int) $req->length : 1;
            $nomorHalaman = (int) $req->start / $perHalaman;
            $offset = $nomorHalaman * $perHalaman;
            $data = DaftarBank::listBank($req, $perHalaman, $offset);
            $jumlahdata = $data['total'];
            $dynamicAttributes = [
                'data' => $data['data'],
                'recordsFiltered' => $jumlahdata,
            ];
            return ResponseHelper::data(__('common.data_ready', ['namadata' => 'Daftar Bank Penerima']), $dynamicAttributes);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
    public function savebank(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'kodebank' => 'required|string',
                'namabank' => 'required|string',
            ]);
            if ($validator->fails()) {
                $dynamicAttributes = ['errors' => $validator->errors()];
                return ResponseHelper::error_validation(__('auth.eds_required_data'), $dynamicAttributes);
            }
            DaftarBank::create([
                'kode_bank' => $request->kodebank,
                'nama_bank' => $request->namabank,
                'keterangan' => $request->keteranganbank,
            ]);
            return ResponseHelper::success("Informasi bank penerima berhasil disimpan. Silahkan lanjutkan transaksi jikalau membutuhkan rekam medis MCU.");
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
    public function deletebank(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'idbank' => 'required|integer',
            ]);
            if ($validator->fails()) {
                $dynamicAttributes = ['errors' => $validator->errors()];
                return ResponseHelper::error_validation(__('auth.eds_required_data'), $dynamicAttributes);
            }
            DaftarBank::where('id', $request->idbank)->delete();
            return ResponseHelper::success_delete("Informasi bank penerima dengan nama " . $request->namabank . " berhasil dihapus beserta seluruh data yang terkait dengan bank penerima ini secara visual di sistem.");
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
    public function editbank(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'idbank' => 'required|integer',
                'kodebank' => 'required|string',
                'namabank' => 'required|string',
            ]);
            if ($validator->fails()) {
                $dynamicAttributes = ['errors' => $validator->errors()];
                return ResponseHelper::error_validation(__('auth.eds_required_data'), $dynamicAttributes);
            }
            DaftarBank::where('id', $request->idbank)->update([
                'kode_bank' => $request->kodebank,
                'nama_bank' => $request->namabank,
                'keterangan' => $request->keteranganbank,
            ]);
            return ResponseHelper::success("Informasi bank penerima dengan nama " . $request->namabank . " berhasil diubah.");
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
    public function daftarantrian_get(Request $request){
        try {
            $perHalaman = (int) $request->length > 0 ? (int) $request->length : 1;
            $nomorHalaman = (int) $request->start / $perHalaman;
            $offset = $nomorHalaman * $perHalaman; 
            $datatabel = Antrian::daftarantrian($request, $perHalaman, $offset);
            $dynamicAttributes = [
                'data' => $datatabel['data'],
                'recordsFiltered' => $datatabel['total'],
                'pages' => [
                    'limit' => $perHalaman,
                    'offset' => $offset,
                ],
            ];
            return ResponseHelper::data(__('common.data_ready', ['namadata' => 'Informasi Antrian Pengguna']), $dynamicAttributes);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
    public function daftarantrian_beranda(Request $request){
        try {
            $perHalaman = (int) $request->length > 0 ? (int) $request->length : 1;
            $nomorHalaman = (int) $request->start / $perHalaman;
            $offset = $nomorHalaman * $perHalaman; 
            $datatabel = Antrian::daftarantrian_beranda($request, $perHalaman, $offset);
            $dynamicAttributes = [
                'data' => $datatabel['data'],
                'recordsFiltered' => $datatabel['total'],
                'pages' => [
                    'limit' => $perHalaman,
                    'offset' => $offset,
                ],
            ];
            return ResponseHelper::data(__('common.data_ready', ['namadata' => 'Informasi Antrian Pengguna']), $dynamicAttributes);
        }catch (\Throwable $th) {
            return ResponseHelper::error($th); 
        }
    }
    public function daftarantrian(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'id_pendaftaran' => 'required',
                'jenis_kategori' => 'required',
                'nama_peserta' => 'required',
            ]);
            if ($validator->fails()) {
                $dynamicAttributes = ['errors' => $validator->errors()];
                return ResponseHelper::error_validation(__('auth.eds_required_data'), $dynamicAttributes);
            }
            $data = [
                'id_pendaftaran' => $request->id_pendaftaran,
                'jenis_kategori' => $request->jenis_kategori,
                'waktu_masuk' => Carbon::now(),
                'status' => 0,
                'keterangan' => '',
                
            ];
            $kategori = str_replace('_', ' ', $request->jenis_kategori);
            $nama_antrian_sudah_ada = Antrian::where('id_pendaftaran', $request->id_pendaftaran)
            ->where('status','<>', 1)
            ->first();
            if ($nama_antrian_sudah_ada) {
                return ResponseHelper::data_conflict("Antrian atas nama " . ucwords($request->nama_peserta) . " sudah ada pada lokasi " . ucwords(str_replace('_', ' ', $nama_antrian_sudah_ada->jenis_kategori))." dan status masih belum selesai. Silahkan koordinasikan kepada kategori yang bersangkutan.");
            }
            Antrian::create($data);
            return ResponseHelper::success("Antrian atas nama " . ucwords($request->nama_peserta) . " berhasil ditambahkan pada lokasi ".ucwords($kategori));
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }    
    public function statusantrian(Request $request){
        try {
            $id_pendaftaran = $request->id_pendaftaran;
            $jenis_kategori = $request->jenis_kategori;
            $nama_peserta = $request->nama_peserta;
            $status = $request->status;
            $jenis_kategori_sekarang = $request->jenis_kategori_sekarang;
            $keterangan = $request->keterangan;
            $waktu_selesai = '';
            if ($status == 1) {
                $waktu_selesai = Carbon::now();
            }
            if ($status == 2) {
                $waktu_selesai = null;
            }
            $data = [
                'id_pendaftaran' => $id_pendaftaran,
                'jenis_kategori' => $jenis_kategori_sekarang,
                'waktu_selesai' => $waktu_selesai,
                'status' => $status,
                'keterangan' => $keterangan,
            ];
            if ($request->status == 0) {
                if ($request->status_saat_ini == 1) {
                    return ResponseHelper::data_conflict("Antrian atas nama " . ucwords($request->nama_peserta) . " sudah selesai pada lokasi ".ucwords($request->jenis_kategori)." sudah selesai jadi tidak bisa dihapus, silahkan ubah ke <strong>PROSES</strong> dahulu");
                }
                Antrian::where('id_pendaftaran', $request->id_pendaftaran)->where('jenis_kategori', $jenis_kategori_sekarang)->delete();
            }else{
                Antrian::where('id_pendaftaran', $request->id_pendaftaran)->where('jenis_kategori', $jenis_kategori_sekarang)->update($data);
            }
            $status_text = '';
            switch ($request->status) {
                case '0':
                    $status_text = 'Hapus Antrian';
                    break;
                case '1':
                    $status_text = 'Tindakan Selesai';
                    break;
                case '2':
                    $status_text = 'Tindakan Diproses';
                    break;
            }
            return ResponseHelper::success("Status antrian atas nama " . ucwords($request->nama_peserta) . " berhasil diperbarui pada lokasi ".ucwords($request->jenis_kategori)." dengan status ".$status);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
}
