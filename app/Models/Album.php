<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    protected static function boot(): void
    {
        parent::boot();

        static::updating(function($model) {
            if ($model->isDirty('cover') and ($model->getOriginal('cover') !== null)) {
                Storage::disk('public')->delete($model->getOriginal('cover'));

                if (!is_null($model->cover)) {
                    $extension = Str::afterLast($model->cover, '.');
                    $newFilename = 'cover_' . $model->id . '.' . $extension;
                    Storage::disk('public')->move($model->cover, 'covers/' . $newFilename);

                    $model->cover = 'covers/' . $newFilename;
                }
            }
        });

        static::created(function ($model) {
            if (!is_null($model->cover)) {
                $extension = Str::afterLast($model->cover, '.');
                $newFilename = 'cover_' . $model->id . '.' . $extension;
                Storage::disk('public')->move($model->cover, 'covers/' . $newFilename);

                $model->cover = 'covers/' . $newFilename;
                $model->save();
            }
        });
    }
}
