<?php

namespace App\Jobs;

use App\Models\Record;
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
        //$records = Record::whereRaw('created_at <= DATE_SUB(NOW(), INTERVAL retain_days DAY)')->get();
        $records = Record::whereRaw(DB::raw("date('now') > date(created_at, '+' || retain_days || ' days')"))->get();
                
        foreach ($records as $record) {
            $record->delete();
        }
    }
}
