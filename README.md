# alianzaamor

Distribute food to people in Arequipa City
Domain site: https://alianzadeamoraqp.org/
Username and password: (Request in slack channel to @david Jeyachandran)

## Communication

* [GitHub](https://github.com/davidjeyachandran/alianzaamor/)
* [Trello](https://trello.com/b/8QaalXQV/alianza-de-amor) (for all dev tasks)
* [Slack](https://drupalappforfood.slack.com) (for communicating with dev team members use please #alianza-de-amor slack channel)

## Contribution

THANK YOU so much for contributing to this project:

- Clone this repository
- Pick a task in Trello
- Create your own branch for your task
- Export configuration, commit any changes to `vendor`, `core` and `web` folders as Pantheon doesn't support composer workflow.
- Send a PR and someone can review (ping any person edutrul, heilop, david, etc). 

Again THANK YOU so much for contributing to this project.

## Local Development

### How to setup local environment

To run locally you may use lando or MAMP:

* Create host: `alianzadeamoraqp.local`
* Create database: `drupal8_ada`

```
git clone git@github.com:davidjeyachandran/alianzaamor.git    
cd alianzaamor
composer install
cp web/sites/default/default.settings.local.php web/sites/default/settings.local.php
cp web/sites/default.services.yml web/sites/services.local.yml
```

Database: We use backup and migrate contrib module to generate backups so please generate a backup in https://alianzadeamoraqp.org/ and download it. Once you install drupal site then restore the database. Save DB backup file to `_db` folder and run;

```
composer drush:restore-db
composer drush:uli-local
```

### Configuration export/import

Configuration is located in `web/sites/default/config/`.

* To export configuration run `composer drush:cex`
* To import configuration run `composer drush:cim`

When committing to codebase make sure you commit only relevant configuration changes.

### Modules and themes

To add (upgrade or remove) new module or theme (e.g. bootstrap4) run composer command.

```
composer require drupal/bootstrap4
# composer update drupal/bootstrap4 --with-dependencies
# composer remove drupal/bootstrap4
```

Make sure you commit `vendor`, `core` and `web` folders as Pantheon doesn't support composer workflow.

To remove git dependencies run 

```
(find ./vendor -type d -name '.git' | xargs rm -rf)
(find ./web -type d -name '.git' | xargs rm -rf)
```

### Theming and CSS

* Custom CSS was moved to `web/modules/custom/aa_core/css/style.css`

When you push please pull down (if you have access) else ask a member to do pull of your changes. If you are doing a lot of pushes then we may consider giving you access to server.

## Hacks
- Hide block title on `/mi-entrega` because the title is on views header.

### Links

* [GitHub](https://github.com/davidjeyachandran/alianzaamor/)
* [Trello](https://trello.com/b/8QaalXQV/alianza-de-amor (for all dev tasks))

## Pantheon

Run the following command on the release.

```
#############
# Development
terminus drush alianzadeamoraqp.dev status
terminus drush alianzadeamoraqp.dev -- config:import -y
terminus drush alianzadeamoraqp.dev updb
terminus drush alianzadeamoraqp.dev cache:rebuild
terminus drush alianzadeamoraqp.dev user:login

# Continious import 
terminus drush alianzadeamoraqp.dev pm:enable aa_content
terminus drush alianzadeamoraqp.dev migrate:status
terminus drush alianzadeamoraqp.dev migrate:import migrate_aa_user_csv

#############
# Stage
terminus drush alianzadeamoraqp.test status
terminus drush alianzadeamoraqp.test -- config:import -y
terminus drush alianzadeamoraqp.test updb
terminus drush alianzadeamoraqp.test cache:rebuild
terminus drush alianzadeamoraqp.test user:login

#############
# Production
terminus drush alianzadeamoraqp.live status
terminus drush alianzadeamoraqp.live -- config:import -y
terminus drush alianzadeamoraqp.live updb
terminus drush alianzadeamoraqp.live cache:rebuild
terminus drush alianzadeamoraqp.live user:login

# Continious import 
terminus drush alianzadeamoraqp.live pm:enable aa_content
terminus drush alianzadeamoraqp.live migrate:status
terminus drush alianzadeamoraqp.live migrate:import migrate_aa_user_csv

# SQL scripts.
echo "$(cat sql/phone-number-stats.sql)" | terminus drush alianzadeamoraqp.live sql:cli
echo "$(cat sql/phone-number-fix.sql)" | terminus drush alianzadeamoraqp.live sql:cli

(terminus drush alianzadeamoraqp.live) | grep backup
```

Deployment

```
# Stage deployment
terminus env:deploy alianzadeamoraqp.test --note="Stage release." --cc
# Prod deployment
terminus env:deploy alianzadeamoraqp.live --note="Prod release." --cc
```