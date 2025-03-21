<?php

namespace App\Models\Poliklinik;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Poliklinik extends Model
{
    protected $table;
    protected $fillable = [
        'pegawai_id',
        'user_id',
        'transaksi_id',
        'judul_laporan',
        'id_kesimpulan',
        'kesimpulan',
        'detail_kesimpulan',
        'catatan_kaki',
        'petugas_id',
    ];
    public function setTableName($table)
    {
        $this->table = $table;
    }
    public function listCitraUnggahanPoliklinik($request, $perHalaman, $offset)
    {
        $jenis_poli = $this->getJenisPoliTable($request->jenis_poli);
        if (!$jenis_poli) {
            throw new \Exception("Jenis poli tidak valid.");
        }
        $tablePrefix = config('database.connections.mysql.prefix');
        $query = $this->buildCitraUnggahanPoliklinikQuery($jenis_poli, $request->parameter_pencarian);
        $jumlahData = $query->groupBy($jenis_poli . '.user_id', $jenis_poli . '.transaksi_id')->count();
        $result = $query->orderBy($jenis_poli . '.created_at', 'DESC')
            ->take($perHalaman)
            ->skip($offset)
            ->get();
        return [
            'data' => $result,
            'total' => $jumlahData,
        ];
    }
    private function getJenisPoliTable($jenis_poli)
    {
        $jenis_poli = strtolower($jenis_poli);
        $jenis_poliTables = [
            'spirometri' => 'mcu_poli_spirometri',
            'ekg' => 'mcu_poli_ekg',
            'threadmill' => 'mcu_poli_threadmill',
            'rontgen_thorax' => 'mcu_poli_rontgen_thorax',
            'rontgen_lumbosacral' => 'mcu_poli_rontgen_lumbosacral',
            'audiometri' => 'mcu_poli_audiometri',
            'usg_ubdomain' => 'mcu_poli_usg_ubdomain',
            'farmingham_score' => 'mcu_poli_farmingham_score',
        ];
        return $jenis_poliTables[$jenis_poli] ?? null;
    }
    private function buildCitraUnggahanPoliklinikQuery($jenis_poli, $parameterPencarian)
    {
        $tablePrefix = config('database.connections.mysql.prefix');
        $query = DB::table($jenis_poli)
            ->leftJoin('mcu_poli_citra', 'mcu_poli_citra.id_trx_poli', '=', $jenis_poli . '.id')
            ->join('users_member', 'users_member.id', '=', $jenis_poli . '.user_id')
            ->join('mcu_transaksi_peserta', 'mcu_transaksi_peserta.id', '=', $jenis_poli . '.transaksi_id')
            ->join('company', 'company.id', '=', 'mcu_transaksi_peserta.perusahaan_id')
            ->join('departemen_peserta', 'departemen_peserta.id', '=', 'mcu_transaksi_peserta.departemen_id')
            ->select(
                'company.*',
                'departemen_peserta.*',
                $jenis_poli . '.*',
                $jenis_poli . '.id as id_' . $jenis_poli,
                'users_member.*',
                'users_member.nama_peserta',
                'mcu_transaksi_peserta.*',
                'mcu_poli_citra.nama_file',
                'mcu_poli_citra.id_trx_poli'
            )
            ->selectRaw(
                'COUNT(' . $tablePrefix . $jenis_poli . '.id) as jumlah_citra, ' .
                'DATE_FORMAT(' . $tablePrefix . $jenis_poli . '.created_at, "%d-%m-%Y %H:%i:%s") as tanggal_transaksi_poliklinik, ' .
                'DATE_FORMAT(' . $tablePrefix . 'mcu_transaksi_peserta.created_at, "%d-%m-%Y %H:%i:%s") as tanggal_transaksi_mcu, ' .
                'TIMESTAMPDIFF(YEAR, ' . $tablePrefix . 'users_member.tanggal_lahir, CURDATE()) AS umur'
            );
        if ($jenis_poli != "mcu_poli_farmingham_score") {
            $query->where('mcu_poli_citra.jenis_poli', str_replace("mcu_", "", $jenis_poli));
        }
        if (!empty($parameterPencarian)) {
            $query->where(function ($query) use ($parameterPencarian) {
                $query->where('users_member.nama_peserta', 'LIKE', '%' . $parameterPencarian . '%')
                    ->orWhere('mcu_transaksi_peserta.no_transaksi', 'LIKE', '%' . $parameterPencarian . '%');
            });
        }
        $query->groupBy($jenis_poli . '.user_id', $jenis_poli . '.transaksi_id');
        return $query;
    }
}