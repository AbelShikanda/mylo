<?php

namespace App\Policies;

use App\Models\Identity\Writer;
use App\Models\Identity\Administrator;

class WriterPolicy
{
    public function view(Administrator $admin, Writer $writer): bool
    {
        return $admin->hasPermission('writers.view');
    }

    public function suspend(Administrator $admin, Writer $writer): bool
    {
        return $admin->hasPermission('writers.suspend');
    }

    public function reinstate(Administrator $admin, Writer $writer): bool
    {
        return $admin->hasPermission('writers.reinstate');
    }
}
