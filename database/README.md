# Database migrations (shared verifycert schema)

All five CVS applications share one MySQL database (**verifycert**). Migration files are kept identical in every app under `database/migrations/`.

## Table naming convention

Tables use an **app-prefix-first** pattern: `{app}_{entity}`.

| App | Prefix | Example tables |
|-----|--------|----------------|
| Training | `training_` | `training_certificates`, `training_trainers`, `training_signatories`, `training_activity_logs` |
| Inspection | `inspection_` | `inspection_certificates`, `inspection_activity_logs` |
| Calibration | `calibration_` | `calibration_certificates`, `calibration_activity_logs` |
| Reports | `reports_` | `reports_certificates`, `reports_activity_logs` |
| Certification | `certification_` | `certification_certificates`, `certification_clients`, `certification_standards`, `certification_accreditation_bodies`, `certification_audit_reports`, `certification_activity_logs` |

Certification foreign-key columns follow the same prefix: `certification_client_id`, `certification_standard_id`, `certification_accreditation_body_id`, `certification_certificate_id`.

Named indexes use the table name as prefix (e.g. `training_certificates_status_index`, `certification_certificates_approved_at_index`).

## RBAC and departments

Shared tables: `departments`, `user_app_permissions`. Users have `is_super_admin` and `department_id`.

Run RBAC migrations once on an existing database:

```bash
php artisan migrate --path=database/migrations/2026_08_29_000070_create_departments_table.php --force
php artisan migrate --path=database/migrations/2026_08_29_000071_add_rbac_to_users_table.php --force
```

Promote a Super Admin (from any app):

```bash
php artisan cvs:promote-super-admin user@example.com
```

Existing users receive `full` access on all five apps when migration `000071` runs. New self-registrations receive `view` on the app where they registered until a Super Admin grants more access.

User lifecycle migrations (run once after RBAC):

```bash
php artisan migrate --path=database/migrations/2026_08_29_000072_drop_department_from_users_table.php --force
php artisan migrate --path=database/migrations/2026_08_29_000073_add_user_lifecycle_columns.php --force
```

Migration `000072` drops the legacy `users.department` string column (use `department_id` + `departments` instead). Migration `000073` adds `is_active` and `password_must_change`, and backfills existing users as active and email-verified.

### Environment toggles

Set `CVS_APP_KEY` in each app's `.env` if needed (`training`, `inspection`, `calibration`, `reports`, `certification`). Defaults match each codebase's `config/cvs.php`.

Self-registration is disabled by default on all apps:

```env
CVS_REGISTRATION_ENABLED=false
```

Set `CVS_REGISTRATION_ENABLED=true` on a specific app to show the register link and enable `/register` on that app only.

---

Run migrations once from **any** app against an **empty** verifycert database:

```bash
php artisan migrate
```

You only need to run this a single time; do not run `migrate` separately from each app unless you are intentionally reconciling a new environment.

## Existing production databases

For databases that still use the legacy suffix-style table names (`certificates_training`, `ba_clients`, etc.), run the rename migrations **once** from any app:

```bash
php artisan migrate --path=database/migrations/2026_08_29_000060_rename_tables_app_prefix_first.php --force
php artisan migrate --path=database/migrations/2026_08_29_000061_rename_certification_columns.php --force
```

Back up the database first. Deploy all five apps together after these migrations complete — application code expects the new table and column names.

**Do not** run a full `php artisan migrate` on populated production databases unless you have reconciled pending entries in the `migrations` table with your DBA.
