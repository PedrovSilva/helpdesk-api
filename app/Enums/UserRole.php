<?php

namespace App\Enums;

enum UserRole: string
{
    case COSTUMER = 'costumer';
    case AGENT = 'agent';
    case ADMIN = 'admin';

}


