# Migration

## User migration

* Migrating content

```
./vendor/bin/drush -l default pm:enable -y aa_content

# Status
./vendor/bin/drush -l default migrate:status

# Initial import 
./vendor/bin/drush -l default migrate:import migrate_aa_user_csv

# Continious import 
./vendor/bin/drush -l default migrate:import migrate_aa_user_csv --update
```
