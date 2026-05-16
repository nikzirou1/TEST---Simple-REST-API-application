<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model representing a single book in the library.
 *
 * @property int                      $id
 * @property string                   $title
 * @property string                   $publisher
 * @property string                   $author
 * @property string                   $genre
 * @property \Illuminate\Support\Carbon $publication_date
 * @property int                      $word_count
 * @property string                   $price          stored as DECIMAL(10,2)
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Book extends Model
{
    use HasFactory;

    // All fields except the auto-generated id are mass-assignable
    protected $fillable = [
        'title',
        'publisher',
        'author',
        'genre',
        'publication_date',
        'word_count',
        'price',
    ];

    protected $casts = [
        // Dates come back as Carbon instances and serialise to Y-m-d in JSON
        'publication_date' => 'date:Y-m-d',
        'word_count'       => 'integer',
        // Keep price as string so DECIMAL precision is not lost to floats
        'price'            => 'decimal:2',
    ];
}
