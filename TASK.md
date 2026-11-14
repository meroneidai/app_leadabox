# TASK.md — Leadabox Business OS

Working plan derived from `PROJECT.md`. Status lives here; architecture notes live in `IMPLEMENTATION_STATUS.md`.

Legend: `[x]` done · `[~]` partial · `[ ]` not started

---

## The business flow being built

```
Lead → Customer → Quotation → Approval → Project ─┬→ Invoice → Payment
                                                  └→ Subscription → Renewal → Recurring invoice
```

Everything below exists to make that one line work end to end.

---

## Phase 0 — Audit & architecture `[x]`

- [x] Inspect existing project, identify conflicts
- [x] Architecture, ER design, permissions model, roadmap
- [x] Risk list and package justification

## Phase 1 — Foundation `[x]`

- [x] MySQL + Redis, queues on Redis
- [x] Git repository with baseline commit
- [x] Roles & permissions (Admin / Manager / Sales / Finance / Support)
- [x] Staff authentication, first-run registration lock, deactivation rules
- [x] Company settings (org nr, VAT, F-skatt, bankgiro, document numbering)
- [x] Localization EN/SV
- [x] Design system + app shell (sidebar, dark mode, toasts)
- [x] Dashboard foundation with real metrics
- [ ] Swedish validation messages (`lang/sv/validation.php`) — deferred

## Phase 2 — CRM `[~]` ← **current**

- [x] Customers (companies): org number, VAT, address, payment terms
- [x] Contacts: the people who receive emails, invoices and project updates
- [x] Customer profile page with contacts, projects and financial summary placeholder
- [x] Link projects to customers
- [x] Search, filter and pagination
- [ ] Leads and lead → customer conversion
- [ ] Notes and activity timeline

## Phase 3 — Catalog `[x]`

Needed before quotations: a quotation line references a priced service.

- [x] Services with price, cost, margin and unit
- [x] Categories (Web, Mobile, AI, Hosting & Infrastructure, Marketing)
- [x] Per-service VAT rate, inheriting the company default when empty
- [x] Billing type: one-time vs recurring
- [x] Renewal interval: monthly / quarterly / 6 months / yearly
- [x] Expiry tracking flag for domains, SSL and licences
- [x] Deactivate instead of delete so historical lines stay valid
- [ ] Products (physical/licensed goods) — only if you actually resell any

## Phase 4 — Quotations `[ ]`

- [ ] Quotation header (customer, contact, valid-until, status)
- [ ] Line items with quantity, unit price, discount, per-line VAT
- [ ] Totals: net, VAT per rate, gross — `decimal` only, never float
- [ ] Sequential numbering from company settings
- [ ] PDF generation (dompdf) with company legal details
- [ ] Email delivery to the contact
- [ ] Public approval/rejection link for the customer
- [ ] Change requests
- [ ] Convert accepted quotation → project and/or invoice and/or subscription

## Phase 5 — Finance `[~]`

- [x] Invoices with sequential, gap-free numbering
- [x] Issued invoices become immutable (corrections go through credit notes)
- [x] Credit notes that mirror the original with negative amounts
- [x] Payments, including partial payments, with automatic status changes
- [x] Overdue detection with days-overdue and an outstanding/overdue summary
- [x] Swedish OCR reference (Luhn check digit) and Bankgiro details on the PDF
- [x] Quotation → invoice conversion
- [x] Customer-facing invoice link
- [ ] Automated reminder levels for overdue invoices
- [ ] Payment provider webhooks (idempotency column already in place)

## Phase 6 — Operations `[~]`

- [x] Projects with status, due date, progress
- [x] Tasks with priority, assignee, due date, completion
- [x] Project team members
- [x] Projects belong to a customer
- [ ] Milestones
- [ ] Customer-visible updates vs internal notes

## Phase 7 — Recurring services `[ ]`

This is the "hosting, domains, SSL renewals" engine.

- [ ] Subscriptions: service, customer, interval, price, next renewal date
- [ ] Automatic recurring invoice generation (scheduler + queue)
- [ ] Renewal reminders to customer and admin
- [ ] Domain expiry tracking
- [ ] Hosting plan tracking
- [ ] SSL expiry tracking
- [ ] Idempotent, retry-safe jobs — never double-charge

## Phase 8 — Customer portal `[~]`

Token links are live; accounts come later.

- [x] Quotation link: view, accept, decline, comment — no login
- [x] Project link: live progress, step-by-step tasks, comment — no login
- [x] Invoice link: view and payment details — no login
- [x] Thank-you email on acceptance with next steps
- [x] Every customer action emails the team
- [x] Internal notes never appear on customer-facing pages
- [ ] Contact logins with a password (dashboard across all their documents)
- [ ] Online payment from the invoice link

## Phase 9 — Support & automation `[ ]`

- [ ] Support tickets
- [ ] Email templates
- [ ] Notification preferences per contact
- [ ] Scheduled jobs and automation rules
- [ ] Activity log / audit trail

## Phase 10 — Reporting & integrations `[ ]`

- [ ] Revenue, sales, customer and subscription reports
- [ ] Stripe
- [ ] Swish via an approved provider
- [ ] Webhook validation, never trust a browser redirect
- [ ] CSV/Excel export

## Phase 11 — Production readiness `[ ]`

- [ ] Security audit, rate limiting, 2FA
- [ ] Performance and query optimisation
- [ ] Backups and deployment
- [ ] Full documentation set

---

## Defaults currently applied

These were chosen as professional defaults and are all editable in the UI:

| Service | Billing | Interval | Expiry tracked |
|---|---|---|---|
| Web Development, Web Design, Mobile App, AI Integration | One-time | — | No |
| Hosting (Basic/Business) | Recurring | Yearly | No |
| Domain registration, SSL certificate | Recurring | Yearly | Yes |
| AI Agent, Maintenance, SEO retainer | Recurring | Monthly | No |

VAT defaults to 25% from company settings; any service or customer can override it, and
the reverse-charge flag forces 0% for VAT-registered customers abroad.

## Needs a decision from you

1. Confirm or correct the billing intervals in the table above.
2. Is 25% right for every service, or do any qualify for 6%/12%?
3. Should the system add **Arabic** (needs RTL) alongside English and Swedish?
4. Payment methods to support first: Bankgiro only, or Stripe and Swish too?

## Needs business or legal verification

- Swedish invoice numbering must be sequential and gap-free; issued invoices must not be editable.
- F-skatt and VAT registration text printed on invoices.
- Reverse charge rules for customers outside Sweden.
- Retention periods before any GDPR deletion.
