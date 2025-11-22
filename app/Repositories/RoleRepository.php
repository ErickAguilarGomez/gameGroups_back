<?php
namespace App\Repositories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

class RoleRepository
{
    protected $model;

    public function __construct(Role $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function findById(int $id): ?Role
    {
        return $this->model->find($id);
    }

    public function findByName(string $name): ?Role
    {
        return $this->model->where('name', $name)->first();
    }

    public function create(array $data): Role
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $role = $this->findById($id);
        
        if (!$role) {
            return false;
        }

        return $role->update($data);
    }

    public function delete(int $id): bool
    {
        $role = $this->findById($id);
        
        if (!$role) {
            return false;
        }

        return $role->delete();
    }
}
