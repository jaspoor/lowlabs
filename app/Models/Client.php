<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

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

    public function users(): HasMany {
        return $this->hasMany(User::class);
    }

    public function records(): HasMany {
        return $this->hasMany(Record::class);
    }
}
