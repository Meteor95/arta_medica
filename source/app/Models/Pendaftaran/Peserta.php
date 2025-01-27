<?php

namespace App\Models\Pendaftaran;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Peserta extends Model
{
    protected $table = 'users_member_temp';
    protected $fillable = [
        'uuid',
        'nomor_identitas',
        'nama_peserta',
        'tempat_lahir',
        'tanggal_lahir',
        'tipe_identitas',
        'jenis_kelamin',
        'alamat',
        'status_kawin',
        'no_telepon',
        'email',
    ];

    public static function listPesertaTabel($req, $perHalaman, $offset)
    {
        $parameterpencarian = $req->parameter_pencarian;
        $tablePrefix = config('database.connections.mysql.prefix');
        $query = DB::table((new self())->getTable())
            ->select('users_member_temp.*')
            ->selectRaw('TIMESTAMPDIFF(YEAR, ' . $tablePrefix . 'users_member_temp.tanggal_lahir, CURDATE()) AS umur, DATE_FORMAT(created_at, "%d-%m-%Y %H:%i:%s") as created_at, DATE_FORMAT(created_at + INTERVAL 7 DAY, "%d-%m-%Y %H:%i:%s") as created_at_delete ');
        if (!empty($parameterpencarian)) {
            $query->where('nomor_identitas', 'LIKE', '%' . $parameterpencarian . '%')
                  ->orWhere('nama_peserta', 'LIKE', '%' . $parameterpencarian . '%');
        }
        $jumlahdata = $query->count();
        $result = $query->take($perHalaman)
            ->skip($offset)
            ->orderBy('nama_peserta', 'ASC')
            ->get();
        return [
            'data' => $result,
            'total' => $jumlahdata
        ];
    }
}
