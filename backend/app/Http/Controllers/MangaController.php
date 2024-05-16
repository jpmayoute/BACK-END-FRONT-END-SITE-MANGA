<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Manga;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MangaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        ///on veut récupérer la liste des mangas en DB et l'a retourner sous forme de JSON
        $mangas = Manga::all()->load('category');
        //dd($mangas);
        return $mangas;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //faire une manip de validation en amont afin de vérifier que l'extraction des valeurs sont ok
        $validator = Validator::make($request->all(), [
            "name"  => ["required", "max:255", "string"],
            "date_existence" => ["required", "date"],
            "author"  => ["required", "max:255", "string"],
            "category_id" => ["nullable", "integer", "exists:categories,id"],
        ]);

        if ($validator->fails()) {
            // Si on est là, c'est qu'une règle de validation à bloqué
            return response($validator->errors(), 422);
        }

        // On crée une nouvelle instance, puis on lui définit la propriété title
        $manga = new Manga();
        $manga->name  = $request->input("name");
        $manga->date_existence = $request->input("date_existence") ?? 0;
        $manga->author  = $request->input("author");
        $manga->category_id = $request->input("category_id");

        // On sauvegarde, puis on gère la réponse avec le code HTTP qui convient
        // 201 : Created
        // 500 : Internal Server Error
        if ($manga->save()) {
            return response()->json($manga->load("category"), 201);
        }

        return response(null, 500);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //on veut récupérer un mangas particulier selon son ID sous forme de JSON
        return Manga::findOrFail($id)->load("category");
        //dd($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
         //faire une manip de validation en amont afin de vérifier que l'extraction des valeurs sont ok - utiliser le Rule si on veut regrouper la methode PUT et PATCH
         $validator = Validator::make($request->all(), [
            "name"  => [Rule::when($request->isMethod('patch'), "sometimes"), "required", "max:255", "string"],
            "date_existence" => [Rule::when($request->isMethod('patch'), "sometimes"), "required", "date"],
            "author" => [Rule::when($request->isMethod('patch'), "sometimes"), "required", "max:255", "string"],
            "category_id" => [Rule::when($request->isMethod('patch'), "sometimes"), "integer", "exists:categories,id"],
        ]);

        if ($validator->fails()) {
            // Si on est là, c'est qu'une règle de validation à bloqué
            return response($validator->errors(), 422);
        }

        // On recherche avec l'id
        $manga = Manga::findOrFail($id);

        $manga->fill($request->only(["name", "date_existence", "author", "category_id"])); //LE RAJOUTER SUR LE MODEL NE PAS OUBLIER

        // On sauvegarde, puis on gère la réponse avec le code HTTP qui convient
        // 500 : Internal Server Error
        if ($manga->save()) {
            return $manga;
        }

        return response(null, 500);


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // On recherche avec l'id
        $manga = Manga::findOrFail($id);

        // On supprime puis on gère la réponse avec le code HTTP qui convient
        // 500 : Internal Server Error
        if ($manga->delete()) {
            return response(null, 204);
        }

        return response(null, 500);
    }
}
