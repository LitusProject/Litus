# Litus upgrade scripts

This directory contains scripts to upgrade the Litus system, e.g. to update the
database schema in ways Doctrine cannot.

Do not run these scripts directly, use `bin/upgrade.sh` or `bin/update.sh`
instead.

## Adding a new upgrade script

- call the script `YYYYMMDDXX.php` with `XX` the lowest non-negative two digit
number creating a unique filename.
- modify `module/CommonBundle/Resources/config/install/configuration.config.php`.
The final element in that file should be `'last_upgrade'`. Set its value to the
name of the script you just created (without the `.php` suffix).
