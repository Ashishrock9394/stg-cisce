<?php

namespace App\Observers;

use App\Models\TermMaster;
use Illuminate\Support\Facades\Cache;

class TermObserver
{
    /**
     * Handle the TermMaster "created" event.
     *
     * @param  \App\Models\TermMaster  $termMaster
     * @return void
     */
    public function created(TermMaster $termMaster)
    {
        Cache::forget("school_terms_{$termMaster->school_id}");
        Cache::forget("school_current_term_{$termMaster->school_id}");

    }

    /**
     * Handle the TermMaster "updated" event.
     *
     * @param  \App\Models\TermMaster  $termMaster
     * @return void
     */
    public function updated(TermMaster $termMaster)
    {
        Cache::forget("school_terms_{$termMaster->school_id}");
        Cache::forget("school_current_term_{$termMaster->school_id}");
    }

    /**
     * Handle the TermMaster "deleted" event.
     *
     * @param  \App\Models\TermMaster  $termMaster
     * @return void
     */
    public function deleted(TermMaster $termMaster)
    {
        Cache::forget("school_terms_{$termMaster->school_id}");
        Cache::forget("school_current_term_{$termMaster->school_id}");
    }

    /**
     * Handle the TermMaster "restored" event.
     *
     * @param  \App\Models\TermMaster  $termMaster
     * @return void
     */
    public function restored(TermMaster $termMaster)
    {
        //
    }

    /**
     * Handle the TermMaster "force deleted" event.
     *
     * @param  \App\Models\TermMaster  $termMaster
     * @return void
     */
    public function forceDeleted(TermMaster $termMaster)
    {
        //
    }
}
