<?php

namespace App\Models;

use Database\Factories\AskQuestionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use LogicException;

#[Fillable(['user_id', 'name', 'email', 'phone', 'subject', 'sawal'])]
class AskQuestion extends Model
{
    /** @use HasFactory<AskQuestionFactory> */
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_ANSWERED = 'answered';

    public const STATUS_CLOSED = 'closed';

    /** @var array<string, string> */
    public const STATUS_LABELS = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_ANSWERED => 'Answered',
        self::STATUS_CLOSED => 'Closed',
    ];

    /** @var array<string, string> */
    public const USER_STATUS_LABELS = [
        self::STATUS_PENDING => 'جواب کا منتظر',
        self::STATUS_ANSWERED => 'جواب موصول ہو گیا',
        self::STATUS_CLOSED => 'مکمل / بند',
    ];

    protected $attributes = ['status' => self::STATUS_PENDING];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return array<int, string> */
    public static function statuses(): array
    {
        return array_keys(self::STATUS_LABELS);
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function userStatusLabel(): string
    {
        return self::USER_STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function save(array $options = []): bool
    {
        if ($this->exists) {
            return parent::save($options);
        }

        return DB::transaction(fn (): bool => parent::save($options));
    }

    protected static function booted(): void
    {
        static::creating(function (self $question): void {
            $question->question_no = 'ASK-TEMP-'.Str::uuid();
        });

        static::created(function (self $question): void {
            $question->forceFill(['question_no' => 'ASK-'.str_pad((string) $question->getKey(), 6, '0', STR_PAD_LEFT)])
                ->saveQuietly();
        });

        static::updating(function (self $question): void {
            if ($question->isDirty('question_no')) {
                throw new LogicException('Question numbers cannot be changed.');
            }
        });
    }
}
