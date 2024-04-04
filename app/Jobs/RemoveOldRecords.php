<?php

namespace App\Jobs;

use App\Models\ProcessRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class RemoveOldRecords implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Remove records older than their creation date + retain days                
        $sql = ('sqlite' === config('database.default')) 
            ? "date('now') > date(created_at, '+' || retain_days || ' days')"
            : "DATE_ADD(created_at, INTERVAL retain_days DAY) < NOW()";
            
        $records = ProcessRecord::whereRaw(DB::raw($sql))->get();

        foreach ($records as $record) {
            $record->delete();
        }
    }
}
