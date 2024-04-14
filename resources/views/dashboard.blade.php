@extends('layouts.app')

@section('posts')
<div class="container">
    <div class="row justify-content-center" style="margin-top: 4rem;">
        <div class="col-md-8">
            <div class="card bg-transparent" style="border: none;">

@if($errors->any())
    <div class="alert alert-danger text-center" style="margin-top: 6rem;">
        {{ $errors->first('error') }}
    </div>
@endif
                <div class="card-body bg-dark" style="margin-bottom: 2rem;">
                    @if ($posts->count() > 0)
                        @foreach ($posts as $post)
                            <!-- Card Bootstrap pour chaque post -->
                            <div class="card mb-3  bg-transparent text-white mx-auto" style="border: 0.1rem solid rgba(76, 76, 76, 0.8);">
                                <div class="card-body bg-dark">
                                    <!-- Afficher l'image du post -->
                                    <img src="{{ route('post.image', $post->id) }}" class="img-fluid mx-auto w-60" alt="Image de la publication">
                                    <!-- Contenu du post -->
                                    </div>
                                    <div class="card-footer">
                                    <!-- Nom d'utilisateur clickable -->
                                    <strong><a href="{{ route('profile.show', ['name' => $post->user->id]) }}">{{ $post->user->name }}</a>
</strong>

                                    <p class="card-text fw-bolder fst-italic">{{ $post->content }}</p>
                                    <!-- Affichage du nombre de likes -->
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

                            <!-- Modal pour les commentaires -->
                            <div class="modal fade" id="postModal{{ $post->id }}" tabindex="-1" aria-labelledby="postModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="postModalLabel">Commentaires pour {{ $post->title }}</h5>
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
                        <p>Aucun post trouvé.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
