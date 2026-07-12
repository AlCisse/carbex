<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SsoLoginAttempt extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'sso_configuration_id',
        'user_id',
        'email',
        'name_id',
        'status',
        'error_code',
        'error_message',
        'ip_address',
        'user_agent',
        'saml_attributes',
        'created_at',
    ];

    protected $casts = [
        'saml_attributes' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Status constants.
     */
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';
    public const STATUS_ERROR = 'error';

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($attempt) {
            $attempt->created_at = now();
        });
    }

    /**
     * Get the SSO configuration.
     */
    public function ssoConfiguration(): BelongsTo
    {
        return $this->belongsTo(SsoConfiguration::class);
    }

    /**
     * Get the user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create a success attempt.
     */
    public static function success(
        SsoConfiguration $config,
        User $user,
        array $attributes = []
    ): self {
        return static::create([
            'sso_configuration_id' => $config->id,
            'user_id' => $user->id,
            'email' => $user->email,
            'name_id' => $attributes['name_id'] ?? null,
            'status' => self::STATUS_SUCCESS,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'saml_attributes' => $attributes,
        ]);
    }

    /**
     * Create a failed attempt.
     */
    public static function failed(
        SsoConfiguration $config,
        string $email,
        string $errorCode,
        string $errorMessage,
        array $attributes = []
    ): self {
        return static::create([
            'sso_configuration_id' => $config->id,
            'email' => $email,
            'name_id' => $attributes['name_id'] ?? null,
            'status' => self::STATUS_FAILED,
            'error_code' => $errorCode,
            'error_message' => $errorMessage,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'saml_attributes' => $attributes,
        ]);
    }

    /**
     * Create an error attempt.
     */
    public static function error(
        SsoConfiguration $config,
        string $errorCode,
        string $errorMessage
    ): self {
        return static::create([
            'sso_configuration_id' => $config->id,
            'status' => self::STATUS_ERROR,
            'error_code' => $errorCode,
            'error_message' => $errorMessage,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
