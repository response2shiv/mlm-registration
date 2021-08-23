<?php

namespace App\Observers;

use App\Models\BoomerangInv;

class BoomerangInvObserver
{

    public function creating(BoomerangInv $boomerangInv)
    {
        $boomerangInv->max_available = BoomerangInv::getMaxBuumerangsAllowed();
    }
    /**
     * Handle the boomerang inv "created" event.
     *
     * @param  \App\BoomerangInv  $boomerangInv
     * @return void
     */
    public function created(BoomerangInv $boomerangInv)
    {
        //
    }

    /**
     * Handle the boomerang inv "updated" event.
     *
     * @param  \App\BoomerangInv  $boomerangInv
     * @return void
     */
    public function updated(BoomerangInv $boomerangInv)
    {
        //
    }

    /**
     * Handle the boomerang inv "deleted" event.
     *
     * @param  \App\BoomerangInv  $boomerangInv
     * @return void
     */
    public function deleted(BoomerangInv $boomerangInv)
    {
        //
    }

    /**
     * Handle the boomerang inv "restored" event.
     *
     * @param  \App\BoomerangInv  $boomerangInv
     * @return void
     */
    public function restored(BoomerangInv $boomerangInv)
    {
        //
    }

    /**
     * Handle the boomerang inv "force deleted" event.
     *
     * @param  \App\BoomerangInv  $boomerangInv
     * @return void
     */
    public function forceDeleted(BoomerangInv $boomerangInv)
    {
        //
    }
}
