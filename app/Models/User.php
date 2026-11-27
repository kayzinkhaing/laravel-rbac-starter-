<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    protected string $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
        'two_factor_recovery_codes',
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
            'two_factor_enabled' => 'boolean',
            'two_factor_recovery_codes' => 'array',
        ];
    }

    public function getNameAttribute()
    {
        return trim(implode(' ', array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ])));
    }

    public function setNameAttribute(string $name): void
    {
        [$firstName, $lastName] = array_pad(
            preg_split('/\s+/', trim($name), 2) ?: [],
            2,
            ''
        );

        $this->attributes['first_name'] = $firstName;
        $this->attributes['last_name'] = $lastName;
    }

    public function clientProfiles()
    {
        return $this->hasMany(ClientProfile::class);
    }

    public function organizations()
    {
        return $this->belongsToMany(
            Organization::class,
            'client_profiles'
        );
    }

    public function organizationId(): ?int
    {
        return $this->clientProfile?->organization_id;
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function clientProfile()
    {
        return $this->hasOne(ClientProfile::class);
    }

    public function profileImage()
    {
        return $this->relationLoaded('media')
            ? $this->getRelation('media')->first()
            : $this->media()->first();
    }

    public function addresses()
    {
        return $this->morphMany(\App\Models\Address::class, 'addressable');
    }
}
