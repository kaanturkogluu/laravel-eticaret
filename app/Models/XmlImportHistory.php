<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class XmlImportHistory extends Model
{
    protected $fillable = [
        'filename',
        'file_path',
        'file_size',
        'status',
        'imported_count',
        'updated_count',
        'skipped_count',
        'error_count',
        'total_processed',
        'started_at',
        'completed_at',
        'error_message'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'file_size' => 'integer',
        'imported_count' => 'integer',
        'updated_count' => 'integer',
        'skipped_count' => 'integer',
        'error_count' => 'integer',
        'total_processed' => 'integer'
    ];

    /**
     * İşlem süresini hesapla
     */
    public function getDurationAttribute()
    {
        if ($this->started_at && $this->completed_at) {
            return $this->started_at->diffInSeconds($this->completed_at);
        }
        return null;
    }

    /**
     * İşlem süresini formatla
     */
    public function getFormattedDurationAttribute()
    {
        $duration = $this->duration;
        if ($duration === null) {
            return 'Bilinmiyor';
        }

        if ($duration < 60) {
            return $duration . ' saniye';
        } elseif ($duration < 3600) {
            return round($duration / 60, 1) . ' dakika';
        } else {
            return round($duration / 3600, 1) . ' saat';
        }
    }

    /**
     * Dosya boyutunu formatla
     */
    public function getFormattedFileSizeAttribute()
    {
        if ($this->file_size < 1024) {
            return $this->file_size . ' B';
        } elseif ($this->file_size < 1024 * 1024) {
            return round($this->file_size / 1024, 1) . ' KB';
        } else {
            return round($this->file_size / (1024 * 1024), 1) . ' MB';
        }
    }

    /**
     * Başarı oranını hesapla
     */
    public function getSuccessRateAttribute()
    {
        if ($this->total_processed == 0) {
            return 0;
        }
        
        $successful = $this->imported_count + $this->updated_count;
        return round(($successful / $this->total_processed) * 100, 1);
    }

    /**
     * Durum badge rengi
     */
    public function getStatusBadgeClassAttribute()
    {
        switch ($this->status) {
            case 'completed':
                return 'bg-success';
            case 'failed':
                return 'bg-danger';
            case 'processing':
                return 'bg-warning';
            case 'pending':
                return 'bg-secondary';
            default:
                return 'bg-secondary';
        }
    }

    /**
     * Durum metni
     */
    public function getStatusTextAttribute()
    {
        switch ($this->status) {
            case 'completed':
                return 'Tamamlandı';
            case 'failed':
                return 'Başarısız';
            case 'processing':
                return 'İşleniyor';
            case 'pending':
                return 'Bekliyor';
            default:
                return 'Bilinmiyor';
        }
    }

    /**
     * Scope: Başarılı işlemler
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: Başarısız işlemler
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope: Son N gün
     */
    public function scopeLastDays($query, $days = 7)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Scope: Bugün
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', Carbon::today());
    }
}
