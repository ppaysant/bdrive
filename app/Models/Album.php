<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Album extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'summary',
        'pages',
        'cover',
        'isbn',
        'comment',
        'read',
        'serie_id',
        'serie_issue',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'serie_id' => 'integer',
        'serie_issue' => 'integer',
    ];

    public function serie(): BelongsTo
    {
        return $this->belongsTo(Serie::class);
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class)->withPivot('role');
    }

    public function publishers(): BelongsToMany
    {
        return $this->belongsToMany(Publisher::class)->withPivot('published_date');
    }
}
