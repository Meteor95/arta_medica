<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LingkunganKerjaPeserta extends Model
{
    protected $table = 'mcu_lingkungan_kerja_peserta';
    protected $fillable = [
        'user_id',
        'transaksi_id',
        'id_atribut_lk',
        'nama_atribut_saat_ini',
        'status',
        'nilai_jam_per_hari',
        'nilai_selama_x_tahun',
        'keterangan',
    ];
    public static function listPesertaLingkuanKerjaTabel($request, $perHalaman, $offset){
        $parameterpencarian = $request->parameter_pencarian;
        $tablePrefix = config('database.connections.mysql.prefix');
        $query = DB::table((new self())->getTable())
            ->join('users_member', 'users_member.id', '=', 'mcu_lingkungan_kerja_peserta.user_id')
            ->join('mcu_transaksi_peserta', 'mcu_transaksi_peserta.id', '=', 'mcu_lingkungan_kerja_peserta.transaksi_id')
            ->join('company', 'company.id', '=', 'mcu_transaksi_peserta.perusahaan_id')
            ->join('departemen_peserta', 'departemen_peserta.id', '=', 'mcu_transaksi_peserta.departemen_id')
            ->select('company.*','departemen_peserta.*','mcu_lingkungan_kerja_peserta.*','mcu_lingkungan_kerja_peserta.id as id_lingkungan_kerja_peserta', 'users_member.*', 'users_member.nama_peserta', 'mcu_transaksi_peserta.*')
            ->selectRaw('COUNT(*) as jumlah_data, TIMESTAMPDIFF(YEAR, ' . $tablePrefix . 'users_member.tanggal_lahir, CURDATE()) AS umur, DATE_FORMAT(tanggal_transaksi, "%d-%m-%Y %H:%i:%s") as tanggal_transaksi');
        if (!empty($parameterpencarian)) {
            $query->where('users_member.nama_peserta', 'LIKE', '%' . $parameterpencarian . '%')
                  ->orWhere('mcu_transaksi_peserta.no_transaksi', 'LIKE', '%' . $parameterpencarian . '%');
        }
        $jumlahdata = $query->groupBy('mcu_lingkungan_kerja_peserta.user_id', 'mcu_lingkungan_kerja_peserta.transaksi_id')
            ->get()
            ->count();
        $result = $query->groupBy('mcu_lingkungan_kerja_peserta.user_id', 'mcu_lingkungan_kerja_peserta.transaksi_id')
            ->orderBy('mcu_lingkungan_kerja_peserta.created_at', 'DESC')
            ->take($perHalaman)
            ->skip($offset)
            ->get();
        return [
            'data' => $result,
            'total' => $jumlahdata
        ];
    }
}
