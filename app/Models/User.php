<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    public $timestamps = false;

    // Allow these fields to be mass assignable
    protected $fillable = ['name', 'middle_name', 'last_name', 'role_id', 'username', 'password', 'cam_image', 'exam_code', 'reset_required'];
}
