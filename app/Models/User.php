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
        'city',
        'state',
        'country',
        'region',
        'zone_id',
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

    /** URL of the profile photo (served privately, cache-busted), or null for the letter avatar. */
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->profile_photo
            ? route('profile.photo', ['user' => $this->route_key]) . '?v=' . ($this->updated_at?->timestamp ?? 1)
            : null;
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

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    /** "Pune, Maharashtra, India" or "-" when nothing is filled. */
    public function getLocationLabelAttribute(): string
    {
        return collect([$this->city, $this->state, $this->country])->filter()->implode(', ') ?: '-';
    }
}
