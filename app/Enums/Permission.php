<?php

namespace App\Enums;

enum Permission: string
{
    case ViewDashboard = 'view dashboard';

    case ViewCustomers = 'view customers';
    case ManageCustomers = 'manage customers';

    case ViewServices = 'view services';
    case ManageServices = 'manage services';

    case ViewQuotations = 'view quotations';
    case ManageQuotations = 'manage quotations';

    case ViewInvoices = 'view invoices';
    case ManageInvoices = 'manage invoices';

    case ViewProjects = 'view projects';
    case ManageProjects = 'manage projects';

    case ViewSubscriptions = 'view subscriptions';
    case ManageSubscriptions = 'manage subscriptions';

    case ViewReports = 'view reports';

    case ViewTeam = 'view team';
    case ManageTeam = 'manage team';

    case ManageSettings = 'manage settings';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
