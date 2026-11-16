<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\Hashidable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use  HasFactory, Notifiable, HasRoles, Hashidable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getchildrens()
    {
        $currentUserId = $this->id;
        return User::whereRaw("JSON_CONTAINS(parent_id, '\"$currentUserId\"')")
            ->get();
    }

    public function getDescendantIds()
    {
        $ids = [$this->id];
        $children = $this->getchildrens();
        foreach ($children as $child) {
            if (!in_array($child->id, $ids)) {
                $ids = array_merge($ids, $child->getDescendantIds());
            }
        }
        return $ids;
    }
}
