<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\InternalPpnpn;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Internal;
use App\Models\Kegiatan;
use App\Models\PesertaKegiatan;
use App\Models\User;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $datas = Guru::orderByDesc('id')->get();

        return view('pages.admin.dashboard.index', ['menu' => 'dashboard', 'datas' => $datas] );
    }

    public function getByKegiatan(Request $r)
    {
        // dd($r->all());
        try {
            $peserta = PesertaKegiatan::where('id_kegiatan', $r->kegiatan_id)->where('no_ktp', $r->nik)->first();
            return response()->json([
                'status' => $peserta == null ? false : true,
                'data' => $peserta
            ]);
        } catch (\Exception $th) {
            return response()->json([
                'status' => false,
                'data' => $th
            ]);
        }
    }

    public function getByKegiatanUser(Request $r)
    {
        // dd($r->all());
        try {
            $peserta = PesertaKegiatan::where('id_kegiatan', $r->kegiatan_id)->where('no_ktp', $r->nik)->first();
            return response()->json([
                'status' => $peserta == null ? false : true,
                'data' => $peserta
            ]);
        } catch (\Exception $th) {
            return response()->json([
                'status' => false,
                'data' => $th
            ]);
        }
    }

    public function jadwal()
    {
        $jadwalInternal = Internal::select('kota', 'jenis', 'deskripsi', 'kegiatan', 'tgl_kegiatan', 'tgl_selesai_kegiatan', 'jam_mulai', 'jam_selesai', 'nama')
            ->whereIn('jenis', ['Pendamping Lokakarya', 'Penugasan Pegawai', 'Penugasan PPNPN'])
            ->get()
            ->groupBy('kegiatan');

        $jadwal = $jadwalInternal->map(function ($items, $key) {
            $groupedByJenis = $items->groupBy('jenis');
            $penugasanPegawai = $groupedByJenis->get('Penugasan Pegawai', collect());
            $penugasanPPNPN = $groupedByJenis->get('Penugasan PPNPN', collect());

            return [
                'kegiatan' => $key,
                'deskripsi' => $items->first()->deskripsi,
                'tgl_kegiatan' => $items->first()->tgl_kegiatan,
                'tgl_selesai_kegiatan' => $items->first()->tgl_selesai_kegiatan,
                'jam_mulai' => $items->first()->jam_mulai,
                'jam_selesai' => $items->first()->jam_selesai,
                'penugasan_pegawai' => $penugasanPegawai->pluck('nama')->unique()->toArray(),
                'penugasan_ppnpn' => $penugasanPPNPN->pluck('nama')->unique()->toArray(),
            ];
        })->values();
        // dd($jadwal[46]);
        return response()->json([
            'jadwal' => $jadwal
        ]);
    }




    /**
     * Show the form for creating a new resource.
     */
    public function profile($id)
    {
        $data = Admin::find($id);
        return view('pages.admin.profile.index', ['menu' => 'profile', 'data' => $data]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function profile_update(Request $request)
    {
        $r = $request->all();
        // $update_nik = Pegawai::where('nama_lengkap', $r['name'])->first();
        // $update->nik();


        // dd( $r['id']);
        $admin = Admin::find($r['id']);
        $user = User::find($r['id']);
        if ($r['password'] != null) {
            $r['password'] = bcrypt($r['password']);
            // dump('ubah password');
        } else {
            unset($r['password']);
        }
        // dd(true);

        $admin->update($r);
        $user->update($r);

        return redirect()->route('dashboard')->with('message', 'update profile');
    }

    public function getJadwalByPegawai($nik)
    {
        // Ambil jadwal dari Internal hanya untuk pegawai dengan NIK tertentu
        // Ambil jadwal dari Internal dengan tiga jenis yang disebutkan
        $jadwalInternal = Internal::select('kota', 'jenis', 'deskripsi', 'kegiatan', 'tgl_kegiatan', 'tgl_selesai_kegiatan', 'jam_mulai', 'jam_selesai', 'nama')
            ->whereIn('jenis', ['Pendamping Lokakarya', 'Penugasan Pegawai', 'Penugasan PPNPN'])->where('nik', $nik)
            ->get();

        // Mengembalikan response dalam bentuk JSON
        return response()->json([
            'jadwal' => $jadwalInternal
        ]);
    }
}
