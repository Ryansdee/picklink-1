<?php
// app/Models/Post.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Post extends Model
{
    protected $fillable = [
        'user_id',
        'content',
        'image_data', // Ajoutez le champ image_data
    ];

    public $timestamps = true;
    public function user()
    {
        return $this->belongsTo(User::class);
    }

   
    public function likes()
    {
        return $this->hasMany(Like::class);
    }
    public function likedByUser()
    {
        $user = Auth::user();
        if ($user) {
            return $this->likes()->where('user_id', $user->id)->exists();
        }
        return redirect('/dashboard')->with('#like');

    }
    public function likesCount()
    {
        return $this->likes()->count();
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}