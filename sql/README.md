# SQL scripts

## Fix phone numbers

Run from root folder to fix phone numbers and see statistics:

```
./vendor/bin/drush -l default sql:query --file=sql/phone-number-stats.sql
./vendor/bin/drush -l default sql:query --file=sql/phone-number-fix.sql
./vendor/bin/drush -l default sql:query --file=sql/phone-number-stats.sql
```

## Fix usernames

For more details see: https://trello.com/c/R4phKSv9/60-correct-numeric-username

Run from root folder to fix phone numbers and see statistics:

```
./vendor/bin/drush -l default sql:query --file=sql/username-stats.sql
./vendor/bin/drush -l default sql:query --file=sql/username-fix.sql
./vendor/bin/drush -l default sql:query --file=sql/username-stats.sql
```
