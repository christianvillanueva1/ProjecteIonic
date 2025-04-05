<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Multimedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApiMultimediaController extends Controller
{


    // Crear un contingut multimèdia
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,jpg,png,gif,mp4,mkv,avi|max:10240', // Accepta fitxers fins a 10MB
        ]);

        $file = $request->file('file');
        $path = $file->storeAs('multimedia', Str::random(40) . '.' . $file->getClientOriginalExtension()); // Emmagatzema el fitxer amb un nom aleatori

        $multimedia = Multimedia::create([
            'user_id' => Auth::id(),
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
        ]);

        return response()->json(['message' => 'Multimedia created successfully', 'data' => $multimedia], 201);
    }

    // Veure tots els continguts multimèdia
    public function index()
    {
        $multimedia = Multimedia::with('user')->get();
        return response()->json($multimedia);
    }

    // Veure els continguts multimèdia d'un usuari en concret
    public function showByUser()
    {
        // Obtenir l'usuari autenticat
        $user = Auth::user();

        // Recuperar els fitxers de l'usuari autenticat
        $multimedia = Multimedia::where('user_id', $user->id)->get();

        return response()->json($multimedia);
    }

    // Eliminar un contingut multimèdia (només si és de l'usuari)
    public function destroy($id)
    {
        $multimedia = Multimedia::findOrFail($id);

        if ($multimedia->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Eliminar el fitxer físicament
        Storage::delete($multimedia->file_path);
        $multimedia->delete();

        return response()->json(['message' => 'Multimedia deleted successfully']);
    }
}
