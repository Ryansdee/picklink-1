<?php
// app/Http/Controllers/PostController.php
namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;



class PostController extends Controller
{
    public function index()
    {
        // Vérifie si un utilisateur est authentifié
        if (Auth::check()) {
            $user = Auth::user();

            // Vérifie si l'utilisateur suit quelqu'un
            if ($user->followings()->exists()) {
                // Si oui, récupère les publications des utilisateurs suivis par l'utilisateur
                $posts = Post::whereIn('user_id', $user->followings()->pluck('id'))->get();
            } else {
                // Si non, récupère toutes les publications disponibles
                $posts = Post::latest()->get();
            }

            // Vérifie si aucune publication n'a été trouvée
            if ($posts->isEmpty()) {
                return view('dashboard')->with('posts', collect());
            }

            return view('dashboard', compact('posts'));
        }

        // Si l'utilisateur n'est pas authentifié, redirige-le vers la page de connexion
        return redirect()->route('login');
    }

    public function store(Request $request)
{
    $request->validate([
        'caption' => 'required|string|max:255',
        'image' => 'required|image|max:2048', // max 2MB
    ]);

    // Vérifier si le contenu contient des mots interdits
    if ($this->containsForbiddenWords($request->caption)) {
        return redirect()->route('dashboard')->withErrors(['error' => 'Votre publication contient des mots interdits. Veuillez modifier votre publication.']);
    }

    // Récupérer les données binaires de l'image
    $imageData = file_get_contents($request->file('image'));

    // Enregistrer les données binaires de l'image dans la base de données
    $postId = DB::table('posts')->insertGetId([
        'user_id' => Auth::id(),
        'content' => $request->caption,
        'image_data' => $imageData,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect()->route('dashboard')->with('success', 'Publication créée avec succès.');
}



public function getImage($id)
{
    $post = Post::findOrFail($id);

    return response($post->image_data, 200, [
        'Content-Type' => 'image/jpeg', // Remplacez par le type MIME approprié pour votre image
        'Content-Disposition' => 'inline; filename=image.jpg'
    ]);
}

    public function like(Post $post)
    {
        $user = Auth::user();

        // Vérifie si l'utilisateur a déjà aimé le post
        if ($post->likedByUser()) {
            // Si oui, supprime le like
            $post->likes()->where('user_id', $user->id)->delete();
            return back()->with('success', 'Like retiré avec succès.');
        } else {
            // Sinon, ajoute le like
            $post->likes()->create(['user_id' => $user->id]);
            return back()->with('success', 'Post aimé avec succès.');
        }
    }

    public function comment(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string|max:255',
        ]);

        if ($this->containsForbiddenWords($request->content)) {
            return back()->withErrors(['error' => 'Votre commentaire contient des mots interdits. Veuillez modifier votre commentaire.']);
        }

        $post->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return back();
    }

    public function create()
    {
        return view('create');
    }
    private function containsForbiddenWords($content)
{
    // Récupérer la liste des mots interdits depuis un fichier ou une source de données
    $forbiddenWords = file(storage_path('app/forbidden_words.txt'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Vérifier si le contenu contient des mots interdits
    foreach ($forbiddenWords as $word) {
        if (stripos($content, $word) !== false) {
            return true;
        }
    }

    return false;
}

}
