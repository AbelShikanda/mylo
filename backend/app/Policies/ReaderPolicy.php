<?php

namespace App\Policies;

use App\Models\Identity\Reader;
use App\Models\Identity\Administrator;

class ReaderPolicy
{
    public function view(Administrator $admin, Reader $reader): bool
    {
        return $admin->hasPermission('readers.view');
    }

    public function suspend(Administrator $admin, Reader $reader): bool
    {
        return $admin->hasPermission('readers.suspend');
    }

    public function reinstate(Administrator $admin, Reader $reader): bool
    {
        return $admin->hasPermission('readers.reinstate');
    }
}
