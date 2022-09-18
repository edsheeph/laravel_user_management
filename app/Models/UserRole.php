<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use HasFactory;

    protected $table = 'user_roles';
    public $timestamps = true;
    public $incrementing = true;

    CONST SYSTEM_ADMINISTRATOR = 1;
    CONST ADMINISTRATOR = 2;
    CONST REGISTRAR = 3;
    CONST ACADEMIC = 4;
    CONST ACCOUNTING = 5;
    CONST CASHIER = 6;
    CONST FACULTY = 7;
    CONST STUDENT = 8;
    const APPLICANT = 9;
    const GUEST = 10;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'desscription',
        'visible',
    ];
    protected $primaryKey = 'id';
}
