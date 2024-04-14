<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        $results = User::where('name', 'like', '%' . $query . '%')->get();
        return view('search_results', ['results' => $results]);
    }

    public function showByName($Id)
    {
        // Convertir le nom de l'utilisateur en un format approprié pour la recherche
        $formattedUserName = Str::slug($Id, '-');

        // Récupérer l'utilisateur en utilisant le nom
        $user = User::where('id', $formattedUserName)->firstOrFail();
        
        // Récupérer les publications de l'utilisateur en utilisant la relation 'posts'
        $posts = $user->posts()->latest()->get();

        // Passer les données à la vue
        return view('profile.show', compact('user', 'posts'));
    }
    public function getProfileImage($username)
    {
        $user = User::select('profile_image_blob')->where('name', $username)->first();
    
        if (!$user || !$user->profile_image_blob) {
            abort(404);
        }
    
        return Response::make($user->profile_image_blob, 200, [
            'Content-Type' => 'image/jpeg', // Définissez le type de contenu approprié
            'Content-Disposition' => 'inline', // Afficher l'image directement dans le navigateur
        ]);
    }
    public function update(Request $request)
{
    // Récupérer l'utilisateur connecté
    $user = Auth::user();

    // Valider les données du formulaire de mise à jour du profil
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
        'password' => 'nullable|string|min:6|confirmed',
        'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // Mettre à jour le nom et l'email de l'utilisateur
    $user->name = $request->name;
    $user->email = $request->email;

    // Mettre à jour le mot de passe s'il est fourni
    if ($request->password) {
        $user->password = bcrypt($request->password);
    }

    // Mettre à jour la photo de profil si elle est fournie
    if ($request->hasFile('profile_image')) {
        $profileImage = $request->file('profile_image');
        $imageData = file_get_contents($profileImage->getRealPath());
        $user->profile_image = $imageData;
    }

    // Enregistrer les modifications de l'utilisateur
    $user->save();

    // Rediriger l'utilisateur vers la page de profil avec un message de succès
    return redirect()->route('profile')->with('success', 'Profil mis à jour avec succès.');
}

    
}