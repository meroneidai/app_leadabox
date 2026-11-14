# Implementation status — Leadabox Business OS

Tracks progress against the phases defined in `PROJECT.md`.

| Phase | Scope | Status |
|-------|-------|--------|
| 0 | Audit, architecture, roadmap | Done |
| 1 | Foundation: config, auth, users, roles, company settings, localization, design system, dashboard | Done |
| 2 | CRM: leads, customers, contacts, notes, activity, conversion | Not started |
| 3 | Catalog: services, products, categories, pricing, VAT, billing intervals | Not started |
| 4 | Quotations: line items, VAT, PDF, email, approval, conversion | Not started |
| 5 | Finance: invoices, numbering, credit notes, payments, overdue | Not started |
| 6 | Operations: projects, milestones, tasks, updates | Partial — projects and tasks exist, not yet linked to customers |
| 7 | Recurring services: subscriptions, renewals, domains, hosting, SSL | Not started |
| 8 | Customer portal | Not started |
| 9 | Support and automation | Not started |
| 10 | Reporting and integrations | Not started |
| 11 | Production readiness | Not started |

## Phase 1 — delivered

**Infrastructure**
- MySQL (`leadabox`) replaces SQLite; Redis backs cache and queues via `predis`.
- Tests run against a real MySQL database (`leadabox_testing`) for behavioural fidelity.
- Git repository initialised.

**Access control**
- Roles: Admin, Manager, Sales, Finance, Support (`spatie/laravel-permission`).
- 16 granular permissions mapped per role in `App\Enums\Role::permissions()`.
- Route-level `can:` middleware plus `UserPolicy`, `CompanyPolicy`, `ProjectPolicy`, `TaskPolicy`.
- Registration is first-run only; later staff are invited from the team module.
- Deactivated accounts cannot authenticate; accounts are deactivated rather than deleted.
- The last active administrator cannot be demoted or deactivated.

**Company settings**
- Legal identity, org number, VAT number, F-skatt flag, address, contact details.
- Financial defaults: currency (SEK), default VAT rate, payment terms.
- Document numbering for quotations and invoices; counters may only move forward.
- Payment details: bankgiro, IBAN, BIC, bank name, invoice footer.
- No Leadabox details are hardcoded in business logic.

**Localization**
- English and Swedish for navigation, auth, dashboard, team, settings, profile, projects.
- Locale resolves from the user record, then session, then config.
- Laravel's built-in Swedish validation messages are not yet published.

**Design system**
- Tailwind 4 tokens with a navy palette, class-based dark mode, and motion that respects `prefers-reduced-motion`.
- Components: button, card, input, select, textarea, label, input-error, badge, avatar, dropdown, dropdown-item, nav-link, page-header, stat, empty-state, toast.
- App shell with collapsible sidebar (persisted), responsive topbar, language selector, theme toggle, profile menu.
- Navigation only links to modules that exist.

**Tests** — 36 passing.

## Known gaps

- Global search, notifications and charts are deliberately absent until they have real data.
- Projects are not yet linked to customers; that arrives with Phase 2.
- `ExampleTest` placeholders from the Laravel skeleton remain.

## Local setup

```sh
brew services start mysql redis
composer install && npm install
php artisan migrate:fresh --seed
npm run dev
```

Seeded sign-ins (password `password`): `admin@leadabox.se`, `sales@leadabox.se`, `finance@leadabox.se`, `support@leadabox.se`.
