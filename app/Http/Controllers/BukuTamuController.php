<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BukuTamuController extends Controller
{
    public function index()
    {
        return view('bukutamu.index'); // nanti bikin view ini
    }

    public function create()
    {
        return view('bukutamu.create');
    }


    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:50',
        'photo' => 'nullable|string', // base64
    ]);

    $photoPath = null;
    if (!empty($request->photo) && str_starts_with($request->photo, 'data:image')) {
        // format data:image/png;base64,AAAA...
        $data = $request->photo;
        $parts = explode(',', $data);
        if (count($parts) === 2) {
            $mimePart = $parts[0]; // data:image/png;base64
            $base64 = $parts[1];
            $ext = 'png';
            if (strpos($mimePart, 'image/jpeg') !== false) $ext = 'jpg';
            // decode
            $binary = base64_decode($base64);

            // buat nama file unik
            $filename = 'photos/photo_'.time().'.'.$ext;

            // simpan ke storage (storage/app/public/photos/...)
            \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $binary);

            $photoPath = $filename;
        }
    }

    // simpan data visitor (sesuaikan kolom)
    \App\Models\Visitor::create([
        'name' => $request->name,
        'phone' => $request->phone,
        'photo' => $photoPath,
        // ... kolom lain jika ada
    ]);

    return redirect()->route('bukutamu.index')->with('success','Tamu berhasil disimpan.');
}

}

