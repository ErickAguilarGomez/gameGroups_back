<?php

namespace App\Repositories\SocialNetwork;

use Illuminate\Support\Facades\DB;

class SocialNetworkRepository
{
    public function getAll()
    {
        return DB::table('social_networks')->get();
    }
}
