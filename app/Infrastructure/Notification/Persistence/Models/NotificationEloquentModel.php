<?php

namespace App\Infrastructure\Notification\Persistence\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationEloquentModel extends Model
{
    use SoftDeletes;
    protected $table = "notification_messages";
    public $incrementing = "false";
    protected $keyType = "string";
    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
    ];
}