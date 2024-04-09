<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientRecord extends Model
{
    use HasFactory;
    
    /**
     * @var string
     */
    protected $table = 'client_records';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type', 
        'reference',       
        'created_at'
    ];

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $reference;

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function clientRecordTagValues(): HasMany
    {
        return $this->hasMany(ClientRecordTagValue::class);
    }

    public function clientRecordValues(): HasMany
    {
        return $this->hasMany(ClientRecordTagValue::class);
    }

    public function delete(): bool|null
    {
        $this->clientRecordValues()->delete();
        $this->clientRecordTagValues()->delete();

        return parent::delete();
    }

    public function addValue(string $value): void
    {        
        $recordValue = new ClientRecordValue;
        $recordValue->value = $value;
        $this->clientRecordValues()->save($recordValue);
    }
    
    public function updateTags(?array $tags): void
    {
        $this->clientRecordTagValues()->delete();
        $this->clientRecordTagValues()->saveMany(collect($tags)->map(function ($tagValue, $tagName) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);            
            $tagValue = new ClientRecordTagValue(['value' => $tagValue]);
            $tagValue->tag_id = $tag->id;
            return $tagValue;
        }));
    }

    public function getTagsAssocArray(): array
    {
        return $this->clientRecordTagValues->pluck('value', 'tag.name')->toArray();
    }

    public function getValuesArray(): array {
        return $this->clientRecordValues->pluck('value')->toArray();
    }

    public function scopeBelongsToUser($query, $user)
    {
        return $query->where($this->table . '.user_id', $user->id);
    }
}