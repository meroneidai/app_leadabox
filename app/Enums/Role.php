<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Manager = 'manager';
    case Sales = 'sales';
    case Finance = 'finance';
    case Support = 'support';

    public function label(): string
    {
        return match ($this) {
            self::Admin => __('roles.admin'),
            self::Manager => __('roles.manager'),
            self::Sales => __('roles.sales'),
            self::Finance => __('roles.finance'),
            self::Support => __('roles.support'),
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Admin => __('roles.admin_description'),
            self::Manager => __('roles.manager_description'),
            self::Sales => __('roles.sales_description'),
            self::Finance => __('roles.finance_description'),
            self::Support => __('roles.support_description'),
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Admin => 'bg-navy-50 text-navy-700 ring-navy-600/20 dark:bg-navy-500/10 dark:text-navy-200 dark:ring-navy-400/30',
            self::Manager => 'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-500/10 dark:text-blue-200 dark:ring-blue-400/30',
            self::Sales => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-200 dark:ring-emerald-400/30',
            self::Finance => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-400/30',
            self::Support => 'bg-slate-100 text-slate-700 ring-slate-500/20 dark:bg-slate-500/10 dark:text-slate-200 dark:ring-slate-400/30',
        };
    }

    /**
     * Permissions granted to each role.
     *
     * @return array<int, string>
     */
    public function permissions(): array
    {
        return match ($this) {
            self::Admin => Permission::values(),
            self::Manager => [
                Permission::ViewDashboard->value,
                Permission::ViewCustomers->value, Permission::ManageCustomers->value,
                Permission::ViewServices->value, Permission::ManageServices->value,
                Permission::ViewQuotations->value, Permission::ManageQuotations->value,
                Permission::ViewInvoices->value,
                Permission::ViewProjects->value, Permission::ManageProjects->value,
                Permission::ViewSubscriptions->value, Permission::ManageSubscriptions->value,
                Permission::ViewReports->value,
                Permission::ViewTeam->value,
            ],
            self::Sales => [
                Permission::ViewDashboard->value,
                Permission::ViewCustomers->value, Permission::ManageCustomers->value,
                Permission::ViewServices->value,
                Permission::ViewQuotations->value, Permission::ManageQuotations->value,
                Permission::ViewProjects->value,
                Permission::ViewSubscriptions->value,
            ],
            self::Finance => [
                Permission::ViewDashboard->value,
                Permission::ViewCustomers->value,
                Permission::ViewServices->value, Permission::ManageServices->value,
                Permission::ViewQuotations->value,
                Permission::ViewInvoices->value, Permission::ManageInvoices->value,
                Permission::ViewSubscriptions->value, Permission::ManageSubscriptions->value,
                Permission::ViewReports->value,
            ],
            self::Support => [
                Permission::ViewDashboard->value,
                Permission::ViewCustomers->value,
                Permission::ViewProjects->value, Permission::ManageProjects->value,
                Permission::ViewSubscriptions->value,
            ],
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
