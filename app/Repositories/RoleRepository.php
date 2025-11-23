<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class RoleRepository
{
    public function all(): Collection
    {
        return DB::table('roles')->get();
    }

    public function findById(int $id)
    {
        return DB::table('roles')->where('id', $id)->first();
    }

    public function findByName(string $name)
    {
        return DB::table('roles')->where('name', $name)->first();
    }

    public function create(array $data)
    {
        $id = DB::table('roles')->insertGetId($data);
        return $this->findById($id);
    }

    public function update(int $id, array $data): bool
    {
        return DB::table('roles')->where('id', $id)->update($data) > 0;
    }

    public function delete(int $id): bool
    {
        return DB::table('roles')->where('id', $id)->delete() > 0;
    }
}
