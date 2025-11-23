<?php

namespace App\Services\SocialNetwork;

use App\Repositories\SocialNetwork\SocialNetworkRepository;

class SocialNetworkService
{
    protected $socialNetworkRepository;

    public function __construct(SocialNetworkRepository $socialNetworkRepository)
    {
        $this->socialNetworkRepository = $socialNetworkRepository;
    }

    public function getAll()
    {
        return $this->socialNetworkRepository->getAll();
    }
}
