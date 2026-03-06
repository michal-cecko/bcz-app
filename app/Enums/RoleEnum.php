<?php

namespace App\Enums;

enum RoleEnum: string
{
    case SuperAdmin = 'SUPERADMIN';
    case Admin = 'ADMIN';
    case Coach = 'COACH';
    case Athlete = 'ATHLETE';
    case Editor = 'EDITOR';
    case Judge = 'JUDGE';
    case Customer = 'CUSTOMER';
}
