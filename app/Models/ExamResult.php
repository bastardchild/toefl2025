<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    // Define table name if different from the default
    protected $table = 'exam_results';

    // Define fillable fields
    protected $fillable = ['user_id', 'exam_id', 'listening_score', 'writing_score', 'reading_score', 'toefl_score'];
}