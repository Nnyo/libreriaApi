<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $table = "books";

    protected $fillable = [
        "id",
        "isbn",
        "title",
        "description",
        "publish_date",
        "category_id",
        "editorial_id"
    ];

    public $timestamps = false;

    public function authors(){
        return $this->belongsToMany(
            Author::class,
            'authors_books',
            'books_id',
            'authors_id'
        );
    }

    public function category(){
        return $this->hasOne(Category::class, 'id','category_id');
    }

    public function editorial(){
        return $this->hasOne(Editorial::class,'id','editorial_id');
    }


}
