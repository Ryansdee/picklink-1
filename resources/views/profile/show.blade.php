@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center" style="margin-top: 7rem">
            <div class="col-md-8">
                <div class="card bg-dark" style="border-bottom: 0.1rem solid rgba(76, 76, 76, 0.99); border-top: none; border-right: none; border-left: none;">
                    <div class="card-body bg-dark text-white d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                            @if ($user->profile_image)
                                <img src="data:image/jpeg;base64,{{ base64_encode($user->profile_image) }}" alt="Photo de profil" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <!-- Affichez une image par défaut si l'utilisateur n'a pas de photo de profil -->
                                <img src="{{ asset('default-profile-image.jpg') }}" alt="Photo de profil par défaut" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                            @endif
                            <h4 class="ms-2">{{ $user->name }}</h4>
                        </div>
                        <button id="settings" type="button" class="btn bg-primary btn-outline-primary text-white" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="fa-solid fa-gear"></i> Settings
                        </button>
                    </div>
                    <div class="row mb-4 text-center text-white">
                        <!-- Informations sur les posts -->
                        <div class="col">
                            <p class="fw-bold">{{ $user->posts->count() }} Posts</p>
                        </div>

                        <!-- Informations sur les followers -->
                        <div class="col">
                            <p class="fw-bold">{{ $user->followers->count() }} Followers</p>
                        </div>

                        <!-- Informations sur les followings -->
                        <div class="col">
                            <p class="fw-bold">{{ $user->followings->count() }} Following</p>
                        </div>
                    </div>
                </div>

                <!-- Section pour afficher les publications de l'utilisateur -->
                <div class="card bg-dark mt-3" style="border: none;">
                    <div class="card-body">
                        <div class="row">
                            @if(isset($posts) && $posts->count() > 0)
                                @foreach ($posts as $post)
                                    <div class="col-md-4 mb-3">
                                        <div class="card h-100 bg-dark text-white" style="border: 0.1rem solid rgba(76, 76, 76, 0.6);"> <!-- Ajoutez la classe h-100 ici -->
                                            <div class="card-body">
                                                <!-- Afficher l'image du post -->
                                                <img src="{{ route('post.image', $post->id) }}" class="img-fluid mx-auto" alt="Image de la publication">
                                                <!-- Ajoutez d'autres détails de publication ici si nécessaire -->
                                            </div>
                                            <div class="card-footer">
                                                <!-- Afficher le nom de l'utilisateur et le contenu du post -->
                                                <h3>{{ $user->name }}</h3>
                                                <h3>{{ $post->content }}</h3>
                                                <p>{{ $post->likesCount() }} Likes</p>

                                                <!-- Formulaire pour liker le post -->
                                                <form action="{{ route('posts.like', $post) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" name="like_submit" style="color: red; cursor: pointer;">
                                                        <!-- Affichage de l'icône de like -->
                                                        @if ($post->likedByUser())
                                                            <i class="fas fa-heart"></i>
                                                        @else
                                                            <i class="far fa-heart"></i>
                                                        @endif
                                                    </button>
                                                </form>

                                                <!-- Bouton pour ouvrir le modal -->
                                                <button type="button" class="btn btn-primary bg-primary" data-bs-toggle="modal" data-bs-target="#postModal{{ $post->id }}">
                                                    Commenter
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal pour les commentaires -->
                                    <div class="modal fade" id="postModal{{ $post->id }}" tabindex="-1" aria-labelledby="postModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="postModalLabel">Commentaires pour : {{ $post->content }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- Afficher l'image du post dans le modal -->
                                                    <img src="{{ route('post.image', $post->id) }}" alt="Image de la publication">
                                                    <!-- Contenu du post -->
                                                    <p>{{ $post->content }}</p>
                                                    <!-- Nombre de likes -->
                                                    <p>{{ $post->likesCount() }} Likes</p>
                                                    <!-- Affichage des likes avec icônes Font Awesome -->
                                                    <div>
                                                        <form action="{{ route('posts.like', $post) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" name="like_submit" style="color: red; cursor: pointer;">
                                                                <!-- Affichage de l'icône de like -->
                                                                @if ($post->likedByUser())
                                                                    <i class="fas fa-heart"></i>
                                                                @else
                                                                    <i class="far fa-heart"></i>
                                                                @endif
                                                            </button>
                                                        </form>
                                                    </div>
                                                    <!-- Affichage des commentaires -->
                                                    @foreach ($post->comments as $comment)
                                                        <p>{{ $comment->user->name }} : {{ $comment->content }}</p>
                                                    @endforeach
                                                    <!-- Formulaire de commentaire -->
                                                    <form action="{{ route('posts.comment', $post) }}" method="POST">
                                                        @csrf
                                                        <textarea name="content" class="form-control" required></textarea>
                                                        <button type="submit" class="btn btn-primary bg-primary mt-2">Commenter</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="col-md-12 text-white">Aucune publication disponible.</p>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- Fin de la section des publications -->

                <!-- Modal pour modifier le profil -->
                <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editProfileModalLabel">Modifier le profil</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Formulaire pour modifier le profil -->
                                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <!-- Champ pour le nom -->
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nom</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
                                    </div>
                                    <!-- Champ pour l'email -->
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                                    </div>
                                    <!-- Champ pour le mot de passe -->
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Mot de passe</label>
                                        <input type="password" class="form-control" id="password" name="password">
                                    </div>
                                    <!-- Champ pour la confirmation du mot de passe -->
                                    <div class="mb-3">
                                        <label for="password_confirmation" class="form-label">Confirmation du mot de passe</label>
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                    </div>
                                    <!-- Champ pour la photo de profil -->
                                    <div class="mb-3">
                                        <label for="profile_image" class="form-label">Photo de profil</label>
                                        <input type="file" class="form-control" id="profile_image" name="profile_image">
                                    </div>
                                    <button type="submit" class="btn bg-primary btn-primary text-white">Save modifications</button>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn bg-secondary btn-secondary text-white" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
