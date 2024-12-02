<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Models\Pendaftaran\Peserta;
use App\Models\Masterdata\MemberMCU;
use App\Models\Transaksi\{LingkunganKerjaPeserta,RiwayatKecelakaanKerja};
use App\Services\RegistrationMCUServices;
use Illuminate\Support\Facades\Validator;

class PendaftaranController extends Controller
{
    public function getpeserta(Request $request)
    {
        try {
            $perHalaman = (int) $request->length > 0 ? (int) $request->length : 1;
            $nomorHalaman = (int) $request->start / $perHalaman;
            $offset = $nomorHalaman * $perHalaman;
            $data = Peserta::listPesertaTabel($request, $perHalaman, $offset);
            $jumlahdata = $data['total'];
            $dynamicAttributes = [
                'data' => $data['data'],
                'recordsFiltered' => $jumlahdata,
                'pages' => [
                    'limit' => $perHalaman,
                    'offset' => $offset,
                ],
            ];
            return ResponseHelper::data(__('common.data_ready', ['namadata' => 'Informasi Peserta']), $dynamicAttributes);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
    public function deletepeserta(RegistrationMCUServices $registrationMCUServices, Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);
            if ($validator->fails()) {
                $dynamicAttributes = ['errors' => $validator->errors()];
                return ResponseHelper::error_validation(__('auth.eds_required_data'), $dynamicAttributes);
            }
            $data = $request->all();
            $registrationMCUServices->handleTransactionDeletePeserta($data);
            return ResponseHelper::success_delete("Informasi Peserta berhasil dihapus beserta paramter lainnya");
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
    public function getdatapeserta(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nomor_identitas' => 'required',
            ]);
            if ($validator->fails()) {
                $dynamicAttributes = ['errors' => $validator->errors()];
                return ResponseHelper::error_validation(__('auth.eds_required_data'), $dynamicAttributes);
            }
            $data = MemberMCU::where('nomor_identitas', $request->nomor_identitas)->first();
            $dynamicAttributes = [  
                'data' => $data,
                'message_info' => "Peserta dengan Nama : <h4><strong>".(isset($data->nama_peserta) ? $data->nama_peserta : '-')."</strong></h4> telah terdaftar pada sistem MCU. Apakah anda ingin menggunakan data ini untuk melakukan transaksi dan pendaftaran peserta MCU ?",
            ];
            if ($data) {
                return ResponseHelper::data(__('common.data_ready', ['namadata' => 'Informasi Peserta']), $dynamicAttributes);
            } else {
                $data = Peserta::where('nomor_identitas', $request->nomor_identitas)->first();
                $dynamicAttributes = [  
                    'data' => $data,
                    'message_info' => '<h4>Informasi Peserta dengan Nama : <strong>'.$data->nama_peserta.'</strong></h4><span style="color:red">BELUM TERDAFTAR PADA SISTEM MCU</span>. Informasi member ini akan ditambahkan menjadi member di Artha Medica Clinic secara otomatis jika selesai melakukan transaksi MCU',
                ];
                if ($data) {
                    return ResponseHelper::data(__('common.data_ready', ['namadata' => 'Informasi Peserta Temporari']), $dynamicAttributes);
                } else {
                    return ResponseHelper::data_not_found(__('common.data_not_found', ['namadata' => 'Informasi Peserta']));
                }
            }
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
    public function simpanriwayatlingkungankerja(RegistrationMCUServices $registrationMCUServices, Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'informasi_riwayat_lingkungan_kerja' => 'required|array',
            ]);
            if ($validator->fails()) {
                $dynamicAttributes = ['errors' => $validator->errors()];
                return ResponseHelper::error_validation(__('auth.eds_required_data'), $dynamicAttributes);
            }
            $registrationMCUServices->handleTransactionInsertLingkunganKerjaPeserta($request);
            return ResponseHelper::success('Data riwayat lingkungan kerja berhasil disimpan. Silahkan lakukan perubahan dengan cara ubah atau hapus pada tabel dibawah jikalau terdapat kesalahan dalam pengisian data');
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
    public function riwayatlingkungankerja(Request $request){
        try {
            $data = LingkunganKerjaPeserta::join('users_member', 'users_member.id', '=', 'mcu_lingkungan_kerja_peserta.user_id')
                ->where('user_id', $request->user_id)
                ->where('transaksi_id', $request->transaksi_id)
                ->get();
            if ($data->isEmpty()){
                return ResponseHelper::data_not_found(__('common.data_not_found', ['namadata' => 'Riwayat Lingkungan Kerja']));
            }
            $dynamicAttributes = [
                'data' => $data,
            ];
            return ResponseHelper::data(__('common.data_ready', ['namadata' => 'Data Riwayat Lingkungan Kerja'])." jikalau ada perubahan maka data yang lama akan dihapus semua dan digantikan dengan parameter baru", $dynamicAttributes);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
    public function getpasien_riwayatlingkungankerja(Request $request){
        try {
            $perHalaman = (int) $request->length > 0 ? (int) $request->length : 1;
            $nomorHalaman = (int) $request->start / $perHalaman;
            $offset = $nomorHalaman * $perHalaman;
            $data = LingkunganKerjaPeserta::listPesertaLingkuanKerjaTabel($request, $perHalaman, $offset);
            $dynamicAttributes = [
                'data' => $data['data'],
                'recordsFiltered' => $data['total'],
                'pages' => [
                    'limit' => $perHalaman,
                    'offset' => $offset,
                ],
            ];
            return ResponseHelper::data(__('common.data_ready', ['namadata' => 'Informasi Peserta']), $dynamicAttributes);
        }catch(\Throwable $th){
            return ResponseHelper::error($th);
        }
    }
    public function hapusriwayatlingkungankerja(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'transaksi_id' => 'required',
                'nomor_identitas' => 'required',
                'nama_peserta' => 'required',
            ]); 
            if ($validator->fails()) {
                $dynamicAttributes = ['errors' => $validator->errors()];
                return ResponseHelper::error_validation(__('auth.eds_required_data'), $dynamicAttributes);
            }
            LingkunganKerjaPeserta::where('transaksi_id', $request->transaksi_id)
                ->where('user_id', $request->user_id)
                ->delete();
            return ResponseHelper::success('Formulir Bahaya Riwayat Lingkungan Kerja dengan Nomor MCU <strong>'.$request->nomor_identitas.'</strong> atas nama <strong>'.$request->nama_peserta.'</strong> berhasil dihapus. Formulir ini bersifat wajib diisi oleh peserta MCU. Jadi silahkan isi kembali formulir tersebut jikalau dibutuhkan pada laporan MCU');
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
    public function getpasien_riwayatkecelakaankerja(Request $request){
        try {
            $perHalaman = (int) $request->length > 0 ? (int) $request->length : 1;
            $nomorHalaman = (int) $request->start / $perHalaman;
            $offset = $nomorHalaman * $perHalaman;
            $data = RiwayatKecelakaanKerja::listPesertaKecelakaanKerjaTabel($request, $perHalaman, $offset);
            $dynamicAttributes = [
                'data' => $data['data'],
                'recordsFiltered' => $data['total'],
                'pages' => [
                    'limit' => $perHalaman,
                    'offset' => $offset,
                ],
            ];
            return ResponseHelper::data(__('common.data_ready', ['namadata' => 'Informasi Peserta']), $dynamicAttributes);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
    public function simpanriwayatkecelakaankerja(Request $request){
        try {
            $user_id = RiwayatKecelakaanKerja::where('user_id', $request->input('user_id'))
                ->where('transaksi_id', $request->input('id_transaksi'))
                ->first();
            if ($user_id) {
                RiwayatKecelakaanKerja::where('user_id', $user_id->user_id)
                    ->where('transaksi_id', $request->input('id_transaksi'))
                    ->update([
                        'riwayat_kecelakaan_kerja' => $request->input('informasi_riwayat_kecelakaan_kerja'),
                        'updated_at' => now()
                    ]);
            }else{
                RiwayatKecelakaanKerja::create([
                    'user_id' => $request->input('user_id'),
                    'transaksi_id' => $request->input('id_transaksi'),
                    'riwayat_kecelakaan_kerja' => $request->input('informasi_riwayat_kecelakaan_kerja'),
                ]);
            }
            return ResponseHelper::success('Data riwayat kecelakaan kerja berhasil disimpan. Silahkan lakukan perubahan dengan cara ubah atau hapus pada tabel dibawah jikalau terdapat kesalahan dalam pengisian data');
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
    public function riwayatkecelakaankerja(Request $request){
        try {
            $data = RiwayatKecelakaanKerja::where('user_id', $request->user_id)
                ->where('transaksi_id', $request->transaksi_id)
                ->get();
            $dynamicAttributes = [
                'data' => $data,
            ];
            return ResponseHelper::data(__('common.data_ready', ['namadata' => 'Data Riwayat Kecelakaan Kerja'])." jikalau ada perubahan maka data yang lama akan dihapus semua dan digantikan dengan parameter baru", $dynamicAttributes);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
    public function hapusriwayatkecelakaankerja(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'transaksi_id' => 'required',
            ]);
            if ($validator->fails()) {
                $dynamicAttributes = ['errors' => $validator->errors()];
                return ResponseHelper::error_validation(__('auth.eds_required_data'), $dynamicAttributes);
            }
            RiwayatKecelakaanKerja::where('user_id', $request->user_id)
                ->where('transaksi_id', $request->transaksi_id)
                ->delete();
            return ResponseHelper::success('Data riwayat kecelakaan kerja dengan nomor identitas <strong>'.$request->nomor_identitas.'</strong> atas nama <strong>'.$request->nama_peserta.'</strong> berhasil dihapus. Formulir ini bersifat wajib diisi oleh peserta MCU. Jadi silahkan isi kembali formulir tersebut jikalau dibutuhkan pada laporan MCU');
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }   
    }
    public function riwayatkebiasaanhidup(Request $request){
        try {
            $data = RiwayatKebiasaanHidup::join('users_member', 'users_member.id', '=', 'mcu_riwayat_kebiasaan_hidup.user_id')
                ->where('user_id', $request->user_id)
                ->where('transaksi_id', $request->transaksi_id)
                ->get();
            if ($data->isEmpty()){
                return ResponseHelper::data_not_found(__('common.data_not_found', ['namadata' => 'Riwayat Lingkungan Kerja']));
            }
            $dynamicAttributes = [
                'data' => $data,
            ];
            return ResponseHelper::data(__('common.data_ready', ['namadata' => 'Data Riwayat Lingkungan Kerja'])." jikalau ada perubahan maka data yang lama akan dihapus semua dan digantikan dengan parameter baru", $dynamicAttributes);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th);
        }
    }
}
