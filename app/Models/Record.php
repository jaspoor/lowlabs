<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Record extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'run',
        'type', 
        'reference',       
        'retain_days',
        'created_at'
    ];

    // If you want to use dates casting for the property
    protected $dates = [
        'retain_days',
    ];

    /**
     * @var string
     */
    protected $run;

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

    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }

    public function processStatus(): BelongsTo
    {
        return $this->belongsTo(ProcessStatus::class);
    }

    public function tagValues(): HasMany
    {
        return $this->hasMany(TagValue::class);
    }

    public function recordValues(): HasMany
    {
        return $this->hasMany(RecordValue::class);
    }

    public function delete(): bool|null
    {
        $this->recordValues()->delete();
        $this->tagValues()->delete();

        return parent::delete();
    }

    public function addValue(string $value): void
    {        
        $recordValue = new RecordValue;
        $recordValue->value = $value;
        $this->recordValues()->save($recordValue);
    }
    
    public function updateTags(array $tags): void
    {
        $this->tagValues()->delete();
        $this->tagValues()->saveMany(collect($tags)->map(function ($tagValue, $tagName) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);            
            $tagValue = new TagValue(['value' => $tagValue]);
            $tagValue->tag_id = $tag->id;
            return $tagValue;
        }));
    }

    public function getTagsAssocArray(): array
    {
        return $this->tagValues->pluck('value', 'tag.name')->toArray();
    }

    public function getValuesArray(): array {
        return $this->recordValues->pluck('value')->toArray();
    }

    public function scopeBelongsToUser($query, $user)
    {
        return $query->where('records.user_id', $user->id);
    }

    public function scopeBelongsToProcess($query, $process)
    {
        return $query->where('records.process_id', $process->id);
    }
}
