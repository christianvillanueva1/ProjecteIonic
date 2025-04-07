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
            'file' => 'required|file|max:10240', // 10MB màxim
        ]);

        $file = $request->file('file');
        $path = $file->store('multimedia', 'public'); // Desa el fitxer al directori 'public'

        $multimedia = Multimedia::create([
            'user_id' => Auth::id(),
            'file_type' => $file->getClientMimeType(),
            'file_path' => $path, // Guarda el camí del fitxer relativ al directori public
        ]);

        return response()->json([
            'message' => 'Multimedia created successfully',
            'data' => [
                'id' => $multimedia->id,
                'file_type' => $multimedia->file_type,
                'file_path' => asset('storage/' . $multimedia->file_path), // Envia el camí públic del fitxer
            ]
        ], 201);
    }


    // Veure tots els continguts multimèdia
    public function index()
    {
        $multimedia = Multimedia::with('user')->get();

        // Iterar per cada item i afegir el fitxer en base64
        $multimedia->map(function ($item) {
            // Llegir el fitxer del sistema de fitxers local
            $filePath = storage_path("app/public/{$item->file_path}");

            // Comprovar si el fitxer existeix
            if (file_exists($filePath)) {
                // Llegir el contingut i convertir-lo a base64
                $fileData = file_get_contents($filePath);
                $item->file_path = base64_encode($fileData);
            }
            return $item;
        });

        return response()->json($multimedia);
    }

    public function showByUser()
    {
        // Obtenir l'usuari autenticat
        $user = Auth::user();

        // Recuperar els fitxers de l'usuari autenticat
        $multimedia = Multimedia::where('user_id', $user->id)->get();

        // Iterar per cada item i afegir el fitxer en base64
        $multimedia->map(function ($item) {
            // Llegir el fitxer del sistema de fitxers local
            $filePath = storage_path("app/public/{$item->file_path}");

            // Comprovar si el fitxer existeix
            if (file_exists($filePath)) {
                // Llegir el contingut i convertir-lo a base64
                $fileData = file_get_contents($filePath);
                $item->file_path = base64_encode($fileData);
            }
            return $item;
        });

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
