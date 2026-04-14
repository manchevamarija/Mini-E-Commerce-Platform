<?php

namespace App\Domain\IdentityAndAccess\Enums;

enum UserRole: string
{
    case Buyer = 'buyer';
    case Vendor = 'vendor';
    case Admin = 'admin';
}
