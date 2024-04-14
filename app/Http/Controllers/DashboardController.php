<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;


use Illuminate\Http\Request;
use App\Models\Post; // Assurez-vous d'importer le modèle Post si ce n'est pas déjà fait

class DashboardController extends Controller
{
    public function index()
{
    $posts = Post::latest()->get();
    $user = Auth::user(); // Récupérez l'utilisateur authentifié

    return view('dashboard', compact('posts', 'user'));
}
}
