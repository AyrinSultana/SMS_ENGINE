<?php

namespace App\Models;

use App\Enums\SmsStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsQueue extends Model
{
    use HasFactory;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'smsqueue';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    'mobile',
    'msg',
    'excel_id',   // previously 'refid'
    'refid',      // new nullable field
    'status',
    'timestamp'
];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => SmsStatus::class,
        'timestamp' => 'datetime',
    ];
    
    /**
     * Get the pending list entry this queue item is associated with
     *
     * @return BelongsTo
     */
    public function pendingList(): BelongsTo
    {
        return $this->belongsTo(PendingList::class, 'excel_id');
    }
    
    /**
     * Scope a query to only include pending items.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', SmsStatus::PENDING);
    }
    
    /**
     * Scope a query to only include sent items.
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
}
