# UPGRADE ACELLE MAIL

This is the guideline for upgrading Acelle Mail source:

1. Backup the old .env file in the source
2. Delete the entire source and upload the new one (please DELETE all existing files and re-upload, rather than 'replace')
3. Restore the old .env file to the new source folder
4. Create an empty file named `installed` at `storage/installed` (to tell Acelle Mail that this is an upgrade and not to go through the installation wizard again)
5. Run the following commands on your server (SSH access required)
````
    cd /path/to/acellemail/source/
    php artisan config:cache
    php artisan migrate
````
6. Check and update your cronjob settings if needed: Admin View > Setting > Cron Jobs

That's it! You are now on the latest version of Acelle Mail.
