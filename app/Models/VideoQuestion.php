<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class VideoQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_id',
        'order',
        'time_in_seconds',
        'question',
        'options',
        'correct_option',
        'points',
        'explanation',
        'required_to_continue'
    ];

    protected $casts = [
        'time_in_seconds' => 'integer',
        'correct_option' => 'integer',
        'points' => 'integer',
        'required_to_continue' => 'boolean',
    ];

    /**
     * Aksesor untuk options - FIXED VERSION
     */
    public function getOptionsAttribute($value)
    {
        // Log untuk debugging
        Log::info('VideoQuestion getOptions called', [
            'id' => $this->id,
            'value_type' => gettype($value),
            'value_sample' => is_string($value) ? substr($value, 0, 100) : 'N/A'
        ]);
        
        // Jika value adalah null atau kosong
        if (empty($value)) {
            Log::warning('Empty options value', ['id' => $this->id]);
            return ['', '', '', ''];
        }
        
        // Jika sudah array (dari casting atau eloquent)
        if (is_array($value)) {
            Log::info('Options is already array', ['id' => $this->id, 'count' => count($value)]);
            return $this->ensureFourOptions($value);
        }
        
        // Jika string, coba decode sebagai JSON
        if (is_string($value)) {
            try {
                $decoded = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
                
                if (is_array($decoded)) {
                    Log::info('Successfully decoded JSON options', [
                        'id' => $this->id, 
                        'count' => count($decoded)
                    ]);
                    return $this->ensureFourOptions($decoded);
                }
            } catch (\JsonException $e) {
                Log::error('JSON decode failed', [
                    'id' => $this->id,
                    'error' => $e->getMessage(),
                    'value' => substr($value, 0, 200)
                ]);
            }
            
            // Coba lagi dengan sanitized string
            $sanitized = $this->sanitizeJsonString($value);
            if ($sanitized) {
                try {
                    $decoded = json_decode($sanitized, true, 512, JSON_THROW_ON_ERROR);
                    if (is_array($decoded)) {
                        Log::info('Successfully decoded sanitized JSON', ['id' => $this->id]);
                        return $this->ensureFourOptions($decoded);
                    }
                } catch (\JsonException $e) {
                    // Do nothing, continue to fallback
                }
            }
        }
        
        // Fallback: return default empty array
        Log::warning('Using fallback options', ['id' => $this->id]);
        return ['', '', '', ''];
    }

    /**
     * Mutator untuk options - FIXED VERSION
     */
    public function setOptionsAttribute($value)
    {
        Log::info('VideoQuestion setOptions called', [
            'id' => $this->id ?? 'new',
            'value_type' => gettype($value),
            'value_sample' => is_array($value) ? json_encode(array_slice($value, 0, 2)) : substr($value, 0, 100)
        ]);
        
        // Jika value adalah array
        if (is_array($value)) {
            $cleaned = $this->cleanOptionsArray($value);
            
            try {
                $json = json_encode($cleaned, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
                $this->attributes['options'] = $json;
                Log::info('Options set as JSON', [
                    'id' => $this->id ?? 'new',
                    'json_length' => strlen($json)
                ]);
                return;
            } catch (\JsonException $e) {
                Log::error('Failed to encode options to JSON', [
                    'id' => $this->id ?? 'new',
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Jika value adalah string JSON
        if (is_string($value) && !empty($value)) {
            try {
                // Validasi bahwa ini adalah JSON valid
                json_decode($value, true, 512, JSON_THROW_ON_ERROR);
                $this->attributes['options'] = $value;
                Log::info('Options set as JSON string', [
                    'id' => $this->id ?? 'new',
                    'string_length' => strlen($value)
                ]);
                return;
            } catch (\JsonException $e) {
                // Bukan JSON valid, lanjut ke fallback
            }
        }
        
        // Fallback: set empty JSON array
        $this->attributes['options'] = json_encode(['', '', '', '']);
        Log::warning('Using fallback for options', ['id' => $this->id ?? 'new']);
    }

    /**
     * Helper: Pastikan selalu ada 4 pilihan
     */
    private function ensureFourOptions(array $options): array
    {
        // Pastikan array
        if (!is_array($options)) {
            return ['', '', '', ''];
        }
        
        // Pastikan 4 elemen
        while (count($options) < 4) {
            $options[] = '';
        }
        
        // Potong jika lebih dari 4
        if (count($options) > 4) {
            $options = array_slice($options, 0, 4);
        }
        
        // Bersihkan setiap elemen
        return array_map(function($item) {
            if ($item === null) return '';
            if (is_array($item)) return json_encode($item);
            return trim((string)$item);
        }, $options);
    }

    /**
     * Helper: Bersihkan array options
     */
    private function cleanOptionsArray(array $options): array
    {
        // Pastikan 4 elemen
        $options = $this->ensureFourOptions($options);
        
        // Trim dan konversi ke string
        return array_map(function($item) {
            if ($item === null) return '';
            if (is_bool($item)) return $item ? 'true' : 'false';
            return trim((string)$item);
        }, $options);
    }

    /**
     * Helper: Sanitize JSON string
     */
    private function sanitizeJsonString(string $string): ?string
    {
        // Hapus karakter kontrol kecuali tab, newline
        $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $string);
        
        // Ganti single quote dengan double quote jika perlu
        $string = str_replace("'", '"', $string);
        
        // Hapus trailing commas
        $string = preg_replace('/,\s*}/', '}', $string);
        $string = preg_replace('/,\s*]/', ']', $string);
        
        return $string;
    }

    /**
     * Relasi
     */
    public function material()
    {
        return $this->belongsTo(Materials::class, 'material_id');
    }

    /**
     * Format waktu
     */
    public function getTimeFormattedAttribute(): string
    {
        $minutes = floor($this->time_in_seconds / 60);
        $seconds = $this->time_in_seconds % 60;
        return sprintf('%02d:%02d', $minutes, $seconds);
    }
    
    /**
     * Get options for form (method khusus untuk form)
     */
    public function getOptionsForForm(): array
    {
        $options = $this->options;
        
        if (!is_array($options)) {
            return ['', '', '', ''];
        }
        
        // Pastikan 4 elemen
        while (count($options) < 4) {
            $options[] = '';
        }
        
        return $options;
    }
}