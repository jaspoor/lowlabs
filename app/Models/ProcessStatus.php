<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProcessStatus extends Model
{
    use HasFactory;

    /**
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * @var string
     */
    protected $name;

    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }

    public function records(): HasMany
    {
        return $this->hasMany(Record::class);
    }
}
