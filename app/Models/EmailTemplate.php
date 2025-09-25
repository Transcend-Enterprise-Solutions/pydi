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
        'header',
        'greetings',
        'message_body',
        'footer',
        'action_button_text',
        'action_button_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get template by name
     */
    public static function getByName(string $name): ?self
    {
        return static::where('name', $name)->where('is_active', true)->first();
    }

    /**
     * Replace placeholders in message body with actual values
     */
    public function replacePlaceholders(array $data): string
    {
        $messageBody = $this->message_body;
        
        foreach ($data as $key => $value) {
            $messageBody = str_replace('{{' . $key . '}}', $value, $messageBody);
        }
        
        return $messageBody;
    }

    /**
     * Get all available placeholders from the message body
     */
    public function getPlaceholders(): array
    {
        preg_match_all('/\{\{(\w+)\}\}/', $this->message_body, $matches);
        return $matches[1] ?? [];
    }
}
