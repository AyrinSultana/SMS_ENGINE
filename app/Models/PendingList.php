<?php

namespace App\Models;

use App\Enums\SmsStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PendingList extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pending_list';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'template_id',
        'message',
        'original_filename',
        'status',
        'timestamp',
        'authorizer'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => SmsStatus::class,
        'timestamp' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the queue items for this pending SMS
     *
     * @return HasMany
     */
    public function smsQueue(): HasMany
    {
        return $this->hasMany(SmsQueue::class, 'refid', 'id');
    }

    /**
     * Get the template associated with this pending list entry
     *
     * @return BelongsTo
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class, 'template_id', 'id');
    }

    /**
     * Scope a query to only include pending records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', SmsStatus::PENDING);
    }

    /**
     * Scope a query to only include approved records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->where('status', SmsStatus::APPROVED);
    }

    /**
     * Scope a query to only include sent records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSent($query)
    {
        return $query->where('status', SmsStatus::SENT);
    }

    /**
     * Get the status label.
     *
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        return $this->status->label();
    }

    /**
     * Check if the record has a file.
     *
     * @return bool
     */
    public function getHasFileAttribute()
    {
        return !empty($this->file_path);
    }

    /**
     * Get the template name through relationship.
     *
     * @return string|null
     */
    public function getTemplateNameAttribute()
    {
        return $this->template ? $this->template->name : null;
    }

    /**
     * Get the message details through relationship.
     *
     * @return string|null
     */
    public function getMsgDetailsAttribute()
    {
        return $this->message;
    }
}
