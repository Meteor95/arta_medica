<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Komponen\{TingkatKesadaran, TandaVital, KondisiFisik};

class PemeriksaanFisikController extends Controller
{
    private function getData($req, $title, $breadcrumb) {
        return [
            'title' => $title,
            'breadcrumb' => $breadcrumb,
            'user_details' => $req->attributes->get('user_details'),
        ];
    }
    public function tingkat_kesadaran(Request $req){
        $data = $this->getData($req, 'Tingkat Kesadaran', [
            'Beranda' => route('admin.beranda'),
            'Tingkat Kesadaran' => route('admin.pemeriksaan_fisik.tingkat_kesadaran'),
        ]);
        $data['tingkat_kesadaran'] = TingkatKesadaran::where('status', 1)->get();
        return view('paneladmin.pemeriksaan_fisik.tingkat_kesadaran', ['data' => $data]);
    }
    public function tanda_vital(Request $req){
        $data = $this->getData($req, 'Tanda Vital dan Gizi', [
            'Beranda' => route('admin.beranda'),
            'Tanda Vital' => route('admin.pemeriksaan_fisik.tanda_vital'),
        ]);
        $data['tanda_vital'] = TandaVital::where('status', 1)->get();
        return view('paneladmin.pemeriksaan_fisik.tanda_vital', ['data' => $data]);
    }
    public function penglihatan(Request $req){
        $data = $this->getData($req, 'Penglihatan', [
            'Beranda' => route('admin.beranda'),
            'Penglihatan' => route('admin.pemeriksaan_fisik.penglihatan'),
        ]);
        return view('paneladmin.pemeriksaan_fisik.penglihatan', ['data' => $data]);
    }
    public function kondisi_fisik(Request $req, $lokasi_fisik){
        $data = $this->getData($req, 'Kondisi Fisik '.ucwords($lokasi_fisik), [
            'Beranda' => route('admin.beranda'),
            'Kondisi Fisik '.ucwords($lokasi_fisik) => route('admin.pemeriksaan_fisik.kondisi_fisik', ['lokasi_fisik' => strtolower($lokasi_fisik)]),
        ]);
        $data['kondisi_fisik'] = KondisiFisik::where('status', 1)->where('nama_atribut_fisik', ucwords(str_replace("_", " & ", $lokasi_fisik)))->get();
        $data['lokasi_fisik'] = ucwords($lokasi_fisik);
        return view('paneladmin.pemeriksaan_fisik.kondisi_fisik.'.strtolower($lokasi_fisik), ['data' => $data]);
    }
}

