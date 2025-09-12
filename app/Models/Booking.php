<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Booking extends Model
{
    use HasFactory;
    protected $primaryKey = 'booking_id';
    public $incrementing  = true;

    protected $fillable = [
        'trainer_id',
        'member_id',
        'start_at',
        'duration_minutes',
        'buffer_before',
        'buffer_after',
        'date',
        'time',
        'description',
        'status',
        'hold_expires_at',
        'cancelled_by',
        'decline_reason',
    ];

    protected $casts = [
        'start_at'         => 'datetime',
        'duration_minutes' => 'integer',
        'buffer_before'    => 'integer',
        'buffer_after'     => 'integer',
        'hold_expires_at'  => 'datetime',
        'date'             => 'date:Y-m-d',
        'time'             => 'string',
    ];

    // Optional: include labels when array/json casting
    protected $appends = ['start_date_label', 'start_time_label'];

    // Relationships
    public function member()
    {return $this->belongsTo(User::class, 'member_id');}
    public function trainer()
    {return $this->belongsTo(User::class, 'trainer_id');}
    public function cancelledBy()
    {return $this->belongsTo(User::class, 'cancelled_by');}

    /* ---------- Labels for blades (prefer legacy date, fallback to start_at) ---------- */
    public function getStartDateLabelAttribute(): string
    {
        // Prioritize the legacy date field since it's more reliable
        if ($this->date) {
            return $this->date instanceof Carbon
            ? $this->date->format('Y-m-d')
            : Carbon::parse($this->date)->format('Y-m-d');
        }
        if ($this->start_at instanceof Carbon) {
            // Convert UTC to local timezone before displaying (use copy to avoid mutation)
            $localTime = $this->start_at->copy()->setTimezone(config('app.timezone'));
            return $localTime->format('Y-m-d');
        }
        return '—';
    }

    public function getStartTimeLabelAttribute(): string
    {
        // Prioritize the legacy time field since it's more reliable
        if ($this->time) {
            $val = (string) $this->time;
            // Handle both "HH:MM" and "HH:MM:SS" explicitly
            if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $val)) {
                // Convert 24-hour to 12-hour format for display
                return Carbon::createFromFormat('H:i:s', $val, config('app.timezone'))->format('H:i');
            }
            if (preg_match('/^\d{2}:\d{2}$/', $val)) {
                return Carbon::createFromFormat('H:i', $val, config('app.timezone'))->format('H:i');
            }
            // Fallback (very rare/legacy)
            return Carbon::parse($val, config('app.timezone'))->format('H:i');
        }

        // Fallback to start_at if time field is empty
        if ($this->start_at instanceof Carbon) {
            // Laravel already converted UTC to app timezone, so just format it
            return $this->start_at->format('H:i');
        }

        return '—';
    }

    /* --------------------------------- Scopes ---------------------------------- */
    public function scopeForMember(Builder $q, int $memberId): Builder
    {
        return $q->where('member_id', $memberId);
    }

    public function scopeForTrainer(Builder $q, int $trainerId): Builder
    {
        return $q->where('trainer_id', $trainerId);
    }

    // Prefer start_at; if NULL, fall back to date/time
    public function scopeOrderByStartDesc(Builder $q): Builder
    {
        return $q->orderByRaw('CASE WHEN start_at IS NULL THEN 1 ELSE 0 END ASC')
            ->orderBy('start_at', 'desc')
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc');
    }

    public function scopeUpcoming(Builder $q): Builder
    {
        return $q->where(function ($w) {
            $w->whereNotNull('start_at')->where('start_at', '>=', now())
                ->orWhere(function ($x) {
                    $x->whereNull('start_at')->whereDate('date', '>=', today());
                });
        });
    }

    public function scopePast(Builder $q): Builder
    {
        return $q->where(function ($w) {
            $w->whereNotNull('start_at')->where('start_at', '<', now())
                ->orWhere(function ($x) {
                    $x->whereNull('start_at')->whereDate('date', '<', today());
                });
        });
    }

    // Convenience: end time in app TZ
    public function getEndAtAttribute(): ?Carbon
    {
        if (! $this->start_at instanceof Carbon) {
            return null;
        }

        return $this->start_at->copy()->addMinutes((int) ($this->duration_minutes ?: 60));
    }
}
