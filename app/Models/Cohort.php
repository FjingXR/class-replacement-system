<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cohort extends Model
{
    protected $fillable = ['programme_id', 'current_year', 'semester', 'tutorial_group', 'academic_year', 'intake'];

    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
}
