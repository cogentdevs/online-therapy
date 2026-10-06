<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Concerns\HasAuditOwnership;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'phone', 'profile_image', 'password', 'is_active', 'permission_mode'])]
#[Hidden(['password', 'remember_token', 'activation_token', 'consumed_activation_token_hash'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasAuditOwnership, HasFactory, HasRoles, Notifiable;

    /** @var array<string, mixed> */
    protected $attributes = [
        'is_active' => true,
        'permission_mode' => 'role',
    ];

    public function frontendProfileImageUrl(): string
    {
        $filename = is_string($this->profile_image) && basename($this->profile_image) === $this->profile_image
            ? $this->profile_image
            : 'user-avatar.png';
        $relativePath = 'images/frontend-images/users/'.$filename;

        if (! is_file(public_path($relativePath))) {
            $relativePath = 'images/frontend-images/users/user-avatar.png';
        }

        return asset($relativePath);
    }

    public function hasCustomFrontendProfileImage(): bool
    {
        return is_string($this->profile_image)
            && $this->profile_image !== 'user-avatar.png'
            && basename($this->profile_image) === $this->profile_image
            && is_file(public_path('images/frontend-images/users/'.$this->profile_image));
    }

    public function parentAdmin(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_admin_id');
    }

    public function childAdmins(): HasMany
    {
        return $this->hasMany(self::class, 'parent_admin_id');
    }

    public function twoFactorSetting(): HasOne
    {
        return $this->hasOne(UserTwoFactorSetting::class);
    }

    public function twoFactorChallenges(): HasMany
    {
        return $this->hasMany(UserTwoFactorChallenge::class);
    }

    public function userSubscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function adRequests(): HasMany
    {
        return $this->hasMany(AdRequest::class);
    }

    public function askQuestions(): HasMany
    {
        return $this->hasMany(AskQuestion::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function contentVisits(): HasMany
    {
        return $this->hasMany(UserContentVisit::class);
    }

    public function siteVisits(): HasMany
    {
        return $this->hasMany(SiteVisit::class);
    }

    public function isActiveForAdmin(): bool
    {
        if (! array_key_exists('is_active', $this->getAttributes())) {
            return true;
        }

        return (bool) $this->getAttribute('is_active');
    }

    public function canAccessAdmin(): bool
    {
        return $this->isActiveForAdmin()
            && ($this->hasRole('super-admin') || $this->can((string) config('admin_modules.access_permission')));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'activation_token_expires_at' => 'datetime',
            'is_active' => 'boolean',
            'permission_mode' => 'string',
            'password' => 'hashed',
        ];
    }
}
