<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use App\Models\Province;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $provinces = Province::all();

            if ($provinces->isEmpty()) {
                return response()->json(['pesan' => 'Tidak ada provinsi ditemukan'], 404);
            }
            
            return response()->json($provinces);
        } catch (\Exception $e) {
            return ['Error' => $e->getMessage()];
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            
            $client = new Client();
            $response = $client->get('https://wilayah.id/api/provinces.json');
            $data = json_decode($response->getBody(), true);

            if (!$data) {
                return response()->json(['pesan' => 'Tidak ada data provinsi ditemukan'], 404);
            }
            
            foreach ($data['data'] as $item) {
                if(Province::where('code', $item['code'])->exists()) {
                    continue;
                }

                Province::create([
                    'code' => $item['code'],
                    'name' => $item['name'],
                ]);
            }

            return response()->json($item, 201);
        } catch (\Exception $e) {
            return response()->json(['pesan' => 'Gagal mengambil data provinsi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'code' => 'required|string|unique:provinces,code',
                'name' => 'required|string|max:255',
            ]);

            $province = Province::create([
                'code' => $request->input('code'),
                'name' => $request->input('name'),
            ]);

            return response()->json($province, 201);
        } catch (\Exception $e) {
            return response()->json(['pesan' => 'Gagal menambah provinsi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $province = Province::find($id);

        if (!$province) {
            return response()->json(['pesan' => 'Provinsi tidak ditemukan'], 404);
        }

        return response()->json($province);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $province = Province::find($id);

            if (!$province) {
                return response()->json(['pesan' => 'Provinsi tidak ditemukan'], 404);
            }

            $request->validate([
                'code' => 'required|string|unique:provinces,code,' . $id,
                'name' => 'required|string|max:255',
            ]);

            $province->update([
                'code' => $request->input('code'),
                'name' => $request->input('name'),
            ]);

            return response()->json($province);
        } catch (\Exception $e) {
            return response()->json(['pesan' => 'Gagal memperbarui provinsi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $province = Province::find($id);

            if (!$province) {
                return response()->json(['pesan' => 'Provinsi tidak ditemukan'], 404);
            }

            $province->delete();

            return response()->json(['pesan' => 'Provinsi berhasil dihapus'], 200);
        } catch (\Exception $e) {
            return response()->json(['pesan' => 'Gagal menghapus provinsi: ' . $e->getMessage()], 500);
        }
    }
}
