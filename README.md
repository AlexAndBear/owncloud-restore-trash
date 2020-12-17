### Required packages
* php
* composer

### Installation
Run `composer install`

### Configuration
Go to restore.php and change the following variables:
1. **$uri** This is the url of your owncloud realm 
2. **$username** This is the username of your owncloud realm, only files for this user will be restored  
3. **$password** This is the password of your owncloud realm
4. **$restoreDate** This is the date/time since when the lost data will be restored (for example 2020-12-08)  

### Run 
Run `php restore.php`
This might take a while, script can be terminated and restarted any time
