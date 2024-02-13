<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Record extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'data',
        'type'
    ];

    /**
     * @var string
     */
    protected $data;

    /**
     * @var string
     */
    protected $type;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
