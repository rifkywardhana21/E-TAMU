<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VisitorController extends Controller
{
    public function create()
    {
        // tampilkan form buku tamu
        return view('visitor.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'institution' => 'nullable|string',
            'gender' => 'required|in:pria,wanita',
            'purpose' => 'required|string',
            'visit_date' => 'required|date',
            'appointment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'photo' => 'nullable'
        ]);

        $appointmentFile = null;
        if ($request->hasFile('appointment_proof')) {
            $appointmentFile = $request->file('appointment_proof')->store('proofs','public');
        }

        $photoPath = null;
        if ($request->photo) {
            $image = str_replace('data:image/png;base64,', '', $request->photo);
            $image = str_replace(' ', '+', $image);
            $photoName = 'webcam_'.time().'.png';
            Storage::disk('public')->put('photos/'.$photoName, base64_decode($image));
            $photoPath = 'photos/'.$photoName;
        }

        Visitor::create([
            'phone' => $request->phone,
            'institution' => $request->institution,
            'gender' => $request->gender,
            'purpose' => $request->purpose,
            'visit_date' => $request->visit_date,
            'appointment_proof' => $appointmentFile,
            'photo' => $photoPath
        ]);

        return redirect()->back()->with('success','Data buku tamu tersimpan!');
    }

}
