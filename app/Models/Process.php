<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Process extends Model
{
    use HasFactory;

    /**
     * Eager load processStatuses
     */
    protected $with = ['processStatuses'];

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
    
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function processStatuses(): HasMany
    {
        return $this->hasMany(ProcessStatus::class);
    }

    public function scopeBelongsToClient($query, $client)
    {
        return $query->where('client_id', $client->id);
    }

    public function updateStatuses(array $statuses): void 
    {
        $this->processStatuses()->delete();
        $this->processStatuses()->saveMany(collect($statuses)->map(function ($statusName) {
            return new ProcessStatus(['name' => $statusName]);
        }));
    }
}
