<?php

namespace App\Http\Controllers;

use App\Models\Character;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CharacterController extends Controller
{
    public function index()
    {
        return view('characters.index');
    }

    public function fetchCharacters()
    {
        $response = Http::get('https://rickandmortyapi.com/api/character');
        $characters = $response->json()['results'];
        
        return response()->json($characters);
    }

    public function store()
    {
        $response = Http::get('https://rickandmortyapi.com/api/character');
        $characters = $response->json()['results'];

        foreach ($characters as $character) {
            Character::updateOrCreate(
                ['api_id' => $character['id']],
                [
                    'name' => $character['name'],
                    'status' => $character['status'],
                    'species' => $character['species'],
                    'type' => $character['type'],
                    'gender' => $character['gender'],
                    'origin_name' => $character['origin']['name'],
                    'origin_url' => $character['origin']['url'],
                    'image' => $character['image'],
                ]
            );
        }

        return response()->json(['message' => 'Personajes guardados exitosamente']);
    }

    public function getStoredCharacters()
    {
        $characters = Character::all();
        return response()->json($characters);
    }

    public function update(Request $request, Character $character)
    {
        $character->update($request->all());
        return response()->json(['message' => 'Personaje actualizado exitosamente']);
    }
}
