<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TagValue extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'value',
    ];

    /**
     * @var string
     */
    protected $value;

    public function tag(): BelongsTo {
        return $this->belongsTo(Tag::class);
    }

    public function record(): BelongsTo {
        return $this->belongsTo(Record::class);
    }
}
