<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    //
    use HasFactory;

    protected $fillable = ['article_id', 'user_id', 'text'];

    public function article(){
        return $this->belongsTo(Article::class);
    }

        public function user(){
        return $this->belongsTo(Article::class);
    }
}
