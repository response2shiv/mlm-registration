<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoomerangInv extends Model
{
    const MAX_BUUMERANGS_ALLOWED = 200;
    protected $table = 'boomerang_inv';

    protected $fillable = [
        'userid',
        'pending_tot',
        'available_tot'
    ];

    public static function getMaxBuumerangsAllowed()
    {
        return self::MAX_BUUMERANGS_ALLOWED;
    }

    protected $primaryKey = 'id';

    public $timestamps = false;

    public static function addToInventory($userId, $newCount)
    {
        $rec = BoomerangInv::where('userid', $userId)->first();
        if (!empty($rec)) {
            $rec->available_tot = $rec->available_tot + $newCount;
            $rec->save();
        } else {
            $n = new BoomerangInv();
            $n->userid = $userId;
            $n->pending_tot = 0;
            $n->available_tot = $newCount == null ? 0 : $newCount;
            $n->save();
        }
    }
}
