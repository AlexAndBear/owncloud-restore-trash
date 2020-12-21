### Required packages
* php
* composer

### Installation
Run `composer install`

### Run 
Run `php restore.php --url=https://owncloud.local --username="admin" --password="admin" --date="2020-12-08"` \
This might take a while, script can be terminated and restarted any time.

**--url** This is the url of your ownCloud realm \
**--username** This is the username of your ownCloud realm, only files for this user will be restored \
**--password** This is the password of your ownCloud realm \
**--date** This is the date/time since when the lost data will be restored (for example 2020-12-08)
