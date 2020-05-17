# SQL scripts

## Fix phone numbers

Run from root folder to fix phone numbers and see statistics:

```
./vendor/bin/drush -l default sql:query --file=sql/phone-number-stats.sql
./vendor/bin/drush -l default sql:query --file=sql/phone-number-fix.sql
./vendor/bin/drush -l default sql:query --file=sql/phone-number-stats.sql
```
