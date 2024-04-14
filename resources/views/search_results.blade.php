@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container" style="margin-top: 6rem;">
    @foreach ($results as $user)
    <a href="{{ route('profile.show', ['name' => $user->id]) }}" style="text-decoration: none; color: inherit;">
        <div class="card mb-3 mx-auto" style="max-width: 540px;">
            <div class="row g-0">
                <div class="col-md-4">
                    @if($user->profile_image)
                    <img src="data:image/jpeg;base64,{{ base64_encode($user->profile_image) }}" alt="Photo de profil" class="rounded-full h-20 w-20">
                    @else
                    <div>Aucune image de profil disponible</div>
                    @endif
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <h5 class="card-title">{{ $user->name }}</h5>
                        <!-- Vous pouvez ajouter d'autres informations de l'utilisateur ici si nécessaire -->
                    </div>
                </div>
            </div>
        </div>
    </a>
    @endforeach
</div>
@endsection
