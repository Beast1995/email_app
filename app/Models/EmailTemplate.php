<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject',
        'content',
        'variables',
        'is_active'
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean'
    ];

    public function campaigns()
    {
        return $this->hasMany(EmailCampaign::class, 'template_id');
    }

    public function renderContent($data = [])
    {
        $content = $this->content;
        
        // Replace variables in the template
        foreach ($data as $key => $value) {
            $content = str_replace("{{" . $key . "}}", $value, $content);
        }
        
        return $content;
    }

    public function renderSubject($data = [])
    {
        $subject = $this->subject;
        
        // Replace variables in the subject
        foreach ($data as $key => $value) {
            $subject = str_replace("{{" . $key . "}}", $value, $subject);
        }
        
        return $subject;
    }
} 