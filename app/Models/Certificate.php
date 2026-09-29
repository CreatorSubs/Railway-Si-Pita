<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by', 'certificate_number', 'recipient_name', 'recipient_identity',
        'institution', 'event_name', 'role', 'issue_date',
        'template_path', 'pos_name_x', 'pos_name_y',
        'pos_number_x', 'pos_number_y', 'pos_qr_x', 'pos_qr_y', 'qr_token',
    ];
}
