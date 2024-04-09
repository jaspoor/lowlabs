<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProcessRecord extends Model
{
    use HasFactory;
    
    /**
     * @var string
     */
    protected $table = 'process_records';

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

    public function processRecordTagValues(): HasMany
    {
        return $this->hasMany(ProcessRecordTagValue::class);
    }

    public function processRecordValues(): HasMany
    {
        return $this->hasMany(ProcessRecordValue::class);
    }

    public function delete(): bool|null
    {
        $this->processRecordValues()->delete();
        $this->processRecordTagValues()->delete();

        return parent::delete();
    }

    public function addValue(string $value): void
    {        
        $recordValue = new ProcessRecordValue;
        $recordValue->value = $value;
        $this->processRecordValues()->save($recordValue);
    }
    
    public function updateTags(?array $tags): void
    {
        $this->processRecordTagValues()->delete();
        $this->processRecordTagValues()->saveMany(collect($tags)->map(function ($tagValue, $tagName) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);            
            $tagValue = new ProcessRecordTagValue(['value' => $tagValue]);
            $tagValue->tag_id = $tag->id;
            return $tagValue;
        }));
    }

    public function getTagsAssocArray(): array
    {
        return $this->processRecordTagValues->pluck('value', 'tag.name')->toArray();
    }

    public function getValuesArray(): array {
        return $this->processRecordValues->pluck('value')->toArray();
    }

    public function scopeBelongsToUser($query, $user)
    {
        return $query->where($this->table . '.user_id', $user->id);
    }

    public function scopeBelongsToProcess($query, $process)
    {
        return $query->where($this->table . '.process_id', $process->id);
    }
}