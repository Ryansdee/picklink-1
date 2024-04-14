@extends('layouts.app')

@section('content')
    <div class="container" style="margin-top: 8rem;">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            @if ($user->profile_image)
                                <img src="data:image/jpeg;base64,{{ base64_encode($user->profile_image) }}" alt="Photo de profil" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <!-- Affichez une image par défaut si l'utilisateur n'a pas de photo de profil -->
                                <img src="{{ asset('default-profile-image.jpg') }}" alt="Photo de profil par défaut" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                            @endif
                            <h4 class="ms-2">{{ $user->name }}</h4>
                        </div>
                        <button  type="button" class="btn bg-primary btn-outline-primary text-white" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="fa-solid fa-gear"></i> Settings
                        </button>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        <!-- Informations sur les posts, followers et followings -->
                        <div class="row mb-4 text-center">
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
                        
                        <!-- Ajoutez ici d'autres informations sur le profil -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour le formulaire de modification du profil -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Modifier le profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Formulaire de modification du profil -->
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
    @csrf

    <div class="form-group mb-3">
        <label for="username" class="form-label">Nom d'utilisateur</label>
        <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username', $user->name) }}" required autocomplete="username" autofocus>
        @error('username')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group mb-3">
        <label for="password" class="form-label">Mot de passe</label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="new-password">
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group mb-3">
        <label for="password_confirmation" class="form-label">Confirmation du mot de passe</label>
        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" autocomplete="new-password">
    </div>

    <div class="form-group mb-3">
        <label for="profile_image" class="form-label">Photo de profil</label>
        <input id="profile_image" type="file" class="form-control @error('profile_image') is-invalid @enderror" name="profile_image">
        @error('profile_image')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </div>
</form>

                </div>
            </div>
        </div>
    </div>
@endsection
