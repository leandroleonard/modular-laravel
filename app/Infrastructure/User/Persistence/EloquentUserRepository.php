<?php

namespace App\Infrastructure\User\Persistence;
use App\Domain\User\Entities\User;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\HashedPassword;
use App\Domain\User\ValueObjects\Role;
use App\Domain\User\Contracts\UserRepositoryInterface;
use App\Infrastructure\User\Persistence\Models\UserModel;
use Carbon\Carbon;
use Illuminate\Support\Str;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function save(User $user): User
    {
        $id = $user->getId() ?? Str::uuid()->toString();
        $roles = array_map(fn(Role $r) => $r->value, $user->getRoles());

        UserModel::updateOrCreate(
            ['id' => $id],
            [
                'name' => $user->getName(),
                'email' => $user->getEmail()->value,
                'password' => $user->getPassword()->value,
                'roles' => $roles,
                'created_at' => Carbon::instance($user->getCreatedAt())->format('Y-m-d H:i:s'),
            ]
        );

        return $user;
    }

    public function findByEmail(Email $email): ?User
    {
        $model = UserModel::where('email', $email->value)->first();

        if ($model === null)
            return null;

        return $this->toDomain($model);
    }

    public function findById(string $id): ?User
    {
        $model = UserModel::where('id', $id)->first();

        if ($model === null)
            return null;

        return $this->toDomain($model);
    }

    public function findAll(?array $filters = []): array
    {
        $query = UserModel::query();

        $dateFields = ['created_at', 'sent_at', 'read_at'];
        foreach ($dateFields as $field) {
            if (!empty($filters[$field])) {
                $dateFilter = $filters[$field];

                if (is_array($dateFilter)) {
                    foreach ($dateFilter as $operator => $value) {
                        $operator = strtoupper($operator);
                        if (in_array($operator, ['GTE', 'LTE', 'GT', 'LT', 'EQ'])) {
                            $query->where($field, $operator, $value);
                        }
                    }
                } else if (is_string($dateFilter)) {
                    $query->whereDate($field, $dateFilter);
                }
            }
        }

        if (!empty($filters['date_range'])) {
            $dateRange = $filters['date_range'];
            $field = $dateRange['field'] ?? 'created_at';

            if (isset($dateRange['from'])) {
                $query->where($field, '>=', $dateRange['from']);
            }

            if (isset($dateRange['to'])) {
                $query->where($field, '<=', $dateRange['to']);
            }
        }

        if (!empty($filters['search'])) {
            $query->where('name', 'LIKE', '%' . $filters['search'] . '%')
            ->orWhere('email', 'LIKE', '%' . $filters['search'] . '%');
        }
        

        if (!empty($filters['order_by'])) {
            $direction = $filters['order_direction'] ?? 'asc';
            $query->orderBy($filters['order_by'], $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        if (!empty($filters['limit'])) {
            $query->limit($filters['limit']);
        } else {
            $query->limit(10);
        }

        if (!empty($filters['offset'])) {
            $query->offset($filters['offset']);
        }

        $models = $query->get();

        return $models->map(function (UserModel $model) {
            return $this->toDomain($model);
        })->toArray();
    }

    private function toDomain(UserModel $model): User
    {
        $rawRoles = $model->roles ?? ['user'];
        $roles = array_map(fn(string $r) => Role::from($r), $rawRoles);

        return new User(
            email: new Email($model->email),
            password: new HashedPassword($model->password),
            name: $model->name,
            roles: $roles,
            id: (string) $model->id,
            createdAt: Carbon::createFromFormat('Y-m-d H:i:s', $model->created_at),
        );
    }
}