
# 08 August - laravel 6.18 migrations issues 

This pull request includes the changes for upgrading to Laravel 6.x. Feel free to commit any additional changes to the shift-31043 branch.

Before merging, you need to:
Checkout the shift-31043 branch
Review all pull request comments for additional changes
Update your dependencies for Laravel 6
Run composer update (if the scripts fail, add --no-scripts)
Thoroughly test your application (no tests?)
If you need help with your upgrade, check out the Human Shifts. You may also join the Shifty Coders Slack workspace to level-up your Laravel skills.

### dependency conflicts
- khill/lavacharts `"khill/lavacharts": "3.0.*"`
- nesbot/carbon


### Shift could not upgrade the following files since they differed from the default Laravel version

- `app/Http/Kernel.php`
- `resources/lang/en/validation.php`

### Since you are upgrading from an older version of Laravel, Shift defaulted your customized configuration files to improve the automation in your next Shift. This was done in a separate commit so you may easily reference and backfill your customizations once you have upgraded to the latest version.

- config/auth.php
- config/cache.php
- config/database.php
- config/filesystems.php
- config/services.php

### Starting in Laravel 5.2, the Input facade is no longer registered by default. Although the Input facade is still available in Laravel 5, it is removed in Laravel 6. As such, Shift recommends using the Request facade or the injected $request object within Controllers and Middleware.

### Shift detected potential uses of create_function which was deprecated in PHP 7.2. You should review these instances and convert them to a closure.
  
- app/Http/Controllers/jsonController.php

### If you are using a string as your model's primary key, you may set the $keyType property on your model.
```
/**
 * The "type" of the primary key ID.
 *
 * @var string
 */
protected $keyType = 'string';
```

### Previous versions of Laravel would retry jobs indefinitely. 
Beginning with Laravel 6, the php artisan queue:work now tries a job one time by default. If you want to force jobs to be tried indefinitely, you may pass the --tries=0 option.


# laravel 7.23 migration (from 6.18)

### Laravel 7 upgraded to Symfony 5 which passes instances of the Throwable interface instead of Exception instances to core components like the Exceptions\Handler.
Shift automated this change. However, if you receive a Throwable type mismatch error there may be additional references you need to convert.


### The MAIL_DRIVER environment variable was renamed in Laravel 7 to MAIL_MAILER in order to support multiple mail configurations.

### env `SESSION_LIFETIME` for session life time


### Laravel 7 uses a new date format when serializing models. 
The previous format was 2019-12-02 20:01:00. Now, dates are serialized using an ISO-8601 compatible date format of 2019-12-02T20:01:00.283041Z in UTC.
This does not affect how dates are stored. Only how they are serialized when using the toArray or toJson Eloquent methods.
If you need to preserve the previous format, you may override the serializeDate method on your model. 
Review the Date Serialization section of the Upgrade Guide for more details.
https://laravel.com/docs/7.x/upgrade#date-serialization



