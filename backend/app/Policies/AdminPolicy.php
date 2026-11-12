<?php

namespace App\Policies;

use App\Models\Identity\Administrator;

class AdminPolicy
{
    public function manageAdministrators(Administrator $admin): bool
    {
        return $admin->hasPermission('administrators.manage');
    }

    public function manageRoles(Administrator $admin): bool
    {
        return $admin->hasPermission('roles.manage');
    }

    public function viewAuditLogs(Administrator $admin): bool
    {
        return $admin->hasPermission('audit-logs.view');
    }

    public function manageFinance(Administrator $admin): bool
    {
        return $admin->hasPermission('finance.manage');
    }
}
