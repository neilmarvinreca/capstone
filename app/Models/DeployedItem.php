<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Models\Activity;

class DeployedItem extends Model
{
    use HasFactory, LogsActivity, CausesActivity, SoftDeletes;

    protected $table = 'deployed_items';
    protected $primaryKey = 'deployedID';
    public $incrementing = true;
    protected $keyType = 'int';
    
    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'deployedID';
    }
    
    protected $fillable = [
        // Remove deployedID from fillable since it's auto-incrementing
        // 'deployedID',
        // CamelCase columns
        'itemName',
        'itemDescription',
        'dateAcquired',
        'itemCategory',
        'qrCode',
        'qr_code_image',
        'departmentID',
        'dateDeployed',
        // snake_case variants (some databases use these)
        'item_name',
        'item_description',
        'date_acquired',
        'item_category',
        'qr_code',
        'qr_code_image',
        'department_id',
        'date_deployed',
        // shared fields
        'cost',
        'status',
        'remarks',
        'quantity',
        'condition',
        'supply_id',
        'purpose',
        'deployed_by',
    ];

    protected $casts = [
        'dateAcquired' => 'datetime',
        'date_acquired' => 'datetime',
        'dateDeployed' => 'date',
        'date_deployed' => 'date',
        'cost' => 'decimal:2'
    ];
    
    /**
     * Get the supply that this deployed item belongs to.
     */
    public function supply()
    {
        return $this->belongsTo(Supply::class, 'supply_id', 'itemID');
    }

    /**
     * Get the user who deployed this item.
     */
    public function deployedBy()
    {
        return $this->belongsTo(User::class, 'deployed_by', 'id');
    }

    /**
     * Get the deployment request associated with this deployed item (if any).
     */
    public function deploymentRequest()
    {
        return $this->belongsTo(DeploymentRequest::class, 'deployment_request_id', 'id');
    }

    /**
     * Get all activities for the deployed item.
     */
    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('deployed_items')
            ->logOnly([
                'itemName', 
                'status', 
                'departmentID',
                'dateDeployed',
                'quantity',
                'condition',
                'supply_id',
                'purpose',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Accessors to bridge camelCase vs snake_case columns
     */
    protected function itemName(): Attribute
    {
        return Attribute::get(fn ($value, $attributes) => $value ?? ($attributes['item_name'] ?? null));
    }

    protected function itemDescription(): Attribute
    {
        return Attribute::get(fn ($value, $attributes) => $value ?? ($attributes['item_description'] ?? null));
    }

    protected function itemCategory(): Attribute
    {
        return Attribute::get(fn ($value, $attributes) => $value ?? ($attributes['item_category'] ?? null));
    }

    protected function dateAcquired(): Attribute
    {
        return Attribute::get(function ($value, $attributes) {
            $raw = $value ?? ($attributes['date_acquired'] ?? null);
            if (!$raw) {
                return null;
            }
            return $raw instanceof \DateTimeInterface ? $raw : Carbon::parse($raw);
        });
    }

    protected function dateDeployed(): Attribute
    {
        return Attribute::get(function ($value, $attributes) {
            $raw = $value ?? ($attributes['date_deployed'] ?? null);
            if (!$raw) {
                return null;
            }
            return $raw instanceof \DateTimeInterface ? $raw : Carbon::parse($raw);
        });
    }

    protected function qrCode(): Attribute
    {
        return Attribute::get(fn ($value, $attributes) => $value ?? ($attributes['qr_code'] ?? null));
    }

    /**
     * Relationship that adapts to camelCase or snake_case foreign keys
     */
    public function department()
    {
        // The deployed_items table uses 'departmentID' as the foreign key
        // The departments table uses 'departmentID' as the primary key
        return $this->belongsTo(Department::class, 'departmentID', 'departmentID');
    }

    /**
     * Get the deployment notifications for this item.
     */
    public function notifications()
    {
        return $this->hasMany(\App\Models\DeploymentNotification::class, 'deployed_item_id', 'deployedID');
    }

    /**
     * Get the QR code image URL
     */
    public function getQrCodeImageUrlAttribute()
    {
        if ($this->qr_code_image) {
            return \App\Services\QRCodeService::getUrl($this->qr_code_image);
        }
        return null;
    }

    /**
     * Generate and store QR code image
     */
    public function generateQrCodeImage()
    {
        if (!$this->qrCode) {
            return false;
        }

        try {
            $qrCodePath = \App\Services\QRCodeService::generateForDeployedItem($this);
            $this->update(['qr_code_image' => $qrCodePath]);
            return $qrCodePath;
        } catch (\Exception $e) {
            \Log::error('Failed to generate QR code image for deployed item: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete QR code image when item is deleted
     */
    protected static function booted()
    {
        static::deleting(function ($deployedItem) {
            if ($deployedItem->qr_code_image) {
                \App\Services\QRCodeService::delete($deployedItem->qr_code_image);
            }
        });
    }
}