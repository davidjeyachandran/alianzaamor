# alianzaamor

Distribute food to people in Arequipa City

# How to setup local environment?

    git clone git@github.com:davidjeyachandran/alianzaamor.git    

Domain site: https://alianzadeamoraqp.org/

Username and password: (Request in slack channel to @david Jeyachandran)

Database: We use currently backup and migrate contrib module to generate backups so please generate a backup in https://alianzadeamoraqp.org/ and download it. Once you install drupal site then restore the database.

# Communication

Trello: https://trello.com/b/8QaalXQV/alianza-de-amor (for all dev tasks)

Slack: drupalappforfood.slack.com (for communicating with dev team members use please #alianza-de-amor slack channel)

# Local Development

We highly suggest create a branch (any name we don't have convention now). Send a PR and someone can review(ping any person edutrul, heilop, david, etc). If too hard then push to master but with caution please since we don't have DEV environment.
We are not using site configuration (config ymls), so all changes will be done manually in PRODUCTION (if you could write your changes in trello we'd appreciate).

## Quick start

* Set domain: `alianzadeamoraqp.local`
* Copy `web/sites/default/default.settings.local.php` to 
  `web/sites/default/settings.local.php` and update DB details.

## Modules and themes

To add (upgrade or remove) new module or theme (e.g. bootstrap4) run composer command.

```
composer require drupal/bootstrap4
# composer update drupal/bootstrap4 --with-dependencies
# composer remove drupal/bootstrap4
```

Make sure you commit `vendor`, `core` and `web` folders as Pantheon doesn't support composer workflow.

## Theming and CSS

* Custom CSS was moved to `web/modules/custom/aa_core/css/style.css`

When you push please pull down (if you have access) else ask a member to do pull of your changes. If you are doing a lot of pushes then we may consider giving you access to server.

# Hacks
- Hide block title on `/mi-entrega` because the title is on views header.

Again THANK YOU so much for contributing to this project.

## Links

* [GitHub](https://github.com/davidjeyachandran/alianzaamor/)
* [Trello](https://trello.com/b/8QaalXQV/alianza-de-amor (for all dev tasks))

# Pantheon

```
terminus drush alianzadeamoraqp.dev status
terminus drush alianzadeamoraqp.dev config:import -y
terminus drush alianzadeamoraqp.dev -l default cache:rebuild
```
