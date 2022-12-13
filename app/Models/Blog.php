<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'blogs';

    protected $fillable = ['id', 'title', 'content', 'created_at', 'updated_at'];

    protected $hidden = ['id'];

    public function findBlogById($id)
    {
        return Blog::find($id);
    }
}
