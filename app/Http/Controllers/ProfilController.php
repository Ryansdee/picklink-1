<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProfilController extends Controller
{
    public function showProfile()
{
    // Récupérer l'ID de l'utilisateur connecté
    $userId = Auth::id();

    // Récupérer l'utilisateur connecté avec ses publications
    $user = User::with('posts')->find($userId);

    // Vérifier si l'utilisateur existe
    if (!$user) {
        // Gérer le cas où l'utilisateur n'est pas trouvé
        // Par exemple, rediriger vers une page d'erreur avec un message d'erreur
        return redirect()->route('error')->with('error', 'Utilisateur non trouvé.');
    }

    // Passer les données à la vue
    return view('profile.profile', compact('user'));
}

public function update(Request $request)
{
    // Lire le fichier de mots interdits
    $forbiddenWords = file(storage_path('app/forbidden_words.txt'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Récupérer l'utilisateur connecté
    $user = Auth::user();

    // Valider les données du formulaire
    $request->validate([
        'username' => 'required|string|max:255',
        'password' => 'nullable|string|min:6|confirmed',
        'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // Vérifier si le nom d'utilisateur contient des mots interdits
    foreach ($forbiddenWords as $word) {
        if (stripos($request->username, $word) !== false) {
            // Si un mot interdit est trouvé, renvoyer une erreur
            return back()->withErrors(['username' => 'Le nom d\'utilisateur contient un ou plusieurs mots banni ! Veuillez le changer.']);
        }
    }

    // Vérifier si le mot de passe contient des mots interdits
    if ($request->password) {
        foreach ($forbiddenWords as $word) {
            if (stripos($request->password, $word) !== false) {
                // Si un mot interdit est trouvé, renvoyer une erreur
                return back()->withErrors(['password' => 'Le mot de passe contient des mots interdits.']);
            }
        }
    }

    // Mettre à jour le nom d'utilisateur
    $user->name = $request->username;

    // Mettre à jour le mot de passe s'il est fourni
    if ($request->password) {
        $user->password = bcrypt($request->password);
    }

    // Enregistrer les modifications de l'utilisateur
    $user->save();

    // Rediriger l'utilisateur vers la page de profil avec un message de succès
    return redirect()->route('profile')->with('success', 'Profil mis à jour avec succès.');
}

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
        $user = User::select('profile_image')->where('name', $username)->first();
    
        if (!$user || !$user->profile_image) {
            abort(404);
        }
    
        return response()->make($user->profile_image, 200, [
            'Content-Type' => 'image/jpeg', // Définissez le type de contenu approprié
            'Content-Disposition' => 'inline', // Afficher l'image directement dans le navigateur
        ]);
    }
}
