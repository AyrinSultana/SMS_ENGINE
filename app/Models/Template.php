<?php

namespace App\Models;

use App\Enums\ApprovalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Correct import
use Illuminate\Database\Eloquent\Relations\HasMany;

class Template extends Model
{
    use HasFactory;
       use SoftDeletes; // Enable soft delete

    protected $dates = ['deleted_at']; // Laravel 7.x and below
    // For Laravel 8+: 
    //protected $casts = ['deleted_at' => 'datetime'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'template';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'authorizer',
        'approval_status',
        'file_path'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'approval_status' => ApprovalStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the SMS history records for this template
     *
     * @return HasMany
     */
    public function smsHistories(): HasMany
    {
        return $this->hasMany(SmsHistory::class);
    }

    /**
     * Get the template messages for this template
     *
     * @return HasMany
     */
    public function templateMessages(): HasMany
    {
        return $this->hasMany(TemplateMessage::class);
    }

    /**
     * Get the pending list entries that use this template
     *
     * @return HasMany
     */
    public function pendingLists(): HasMany
    {
        return $this->hasMany(PendingList::class, 'template_id', 'id');
    }

    /**
     * Scope a query to only include approved templates.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->where('approval_status', ApprovalStatus::APPROVED);
    }

    /**
     * Scope a query to only include pending templates.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('approval_status', ApprovalStatus::PENDING);
    }
}
