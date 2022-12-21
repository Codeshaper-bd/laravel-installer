
# Laravel Web Installer | A Web Installer [Package](https://packagist.org/packages/rachidlaasri/laravel-installer)

[![Total Downloads](https://poser.pugx.org/rachidlaasri/laravel-installer/d/total.svg)](https://packagist.org/packages/rachidlaasri/laravel-installer)
[![Latest Stable Version](https://poser.pugx.org/rachidlaasri/laravel-installer/v/stable.svg)](https://packagist.org/packages/rachidlaasri/laravel-installer)
[![License](https://poser.pugx.org/rachidlaasri/laravel-installer/license.svg)](https://packagist.org/packages/rachidlaasri/laravel-installer)

- [About](#about)
- [Requirements](#requirements)
- [Installation](#installation)
- [Screenshots](#screenshots)
- [License](#license)

## About

Do you want your clients to be able to install a Laravel project just like they do with WordPress or any other CMS?
This Laravel package allows users who don't use Composer, SSH etc to install your application just by following the setup wizard.
The current features are :

- Check For Server Requirements.
- Check For Folders Permissions.
- Ability to set database information.
	- .env text editor
	- .env form wizard
- Migrate The Database.
- Seed The Tables.

## Requirements

* [Laravel 5.5+]

## Installation

- Add in `composer.json`
```json
"require": {
	"rachidlaasri/laravel-installer": "dev-master",
},
```
- Add vcs url for this package to your `composer.json` file:

```json
"repositories": {
        "rachidlaasri/laravel-installer": {
            "type": "vcs",
            "url": "git@github.com:Codeshaper-bd/laravel-installer.git"
        }
    },
```
- Run `composer update rachidlaasri/laravel-installer` to install the package.
- Delete `config\installer.php` folder if exists.
- Delete `public\installer` folder if exists.
- Delete `resources\views\vendor\installer` folder if exists.
- Delete `resources\lang` folder if exists.
- Run `php artisan vendor:publish --provider="RachidLaasri\LaravelInstaller\Providers\LaravelInstallerServiceProvider"` to publish the assets.
- Wrap all your routes in `['is_verified', 'need_to_install']` middleware. For example:

```php
Route::group(['middleware' => ['is_verified', 'need_to_install']], function () {
	// `/` route needs for redirecting to `install` route
	Route::get('/', function () {
		return view('welcome');
	});
	// Your routes here
	[...]
});
```

- Run `php artisan optimize:clear`.

## Screenshots

###### Installer
![Laravel web installer | Step 1](https://s3-us-west-2.amazonaws.com/github-project-images/laravel-installer/install/1-welcome.jpg)
![Laravel web installer | Step 2](https://s3-us-west-2.amazonaws.com/github-project-images/laravel-installer/install/2-requirements.jpg)
![Laravel web installer | Step 3](https://s3-us-west-2.amazonaws.com/github-project-images/laravel-installer/install/3-permissions.jpg)
![Laravel web installer | Step 4 Menu](https://s3-us-west-2.amazonaws.com/github-project-images/laravel-installer/install/4-environment.jpg)
![Laravel web installer | Step 4 Classic](https://s3-us-west-2.amazonaws.com/github-project-images/laravel-installer/install/4a-environment-classic.jpg)
![Laravel web installer | Step 4 Wizard 1](https://s3-us-west-2.amazonaws.com/github-project-images/laravel-installer/install/4b-environment-wizard-1.jpg)
![Laravel web installer | Step 4 Wizard 2](https://s3-us-west-2.amazonaws.com/github-project-images/laravel-installer/install/4b-environment-wizard-2.jpg)
![Laravel web installer | Step 4 Wizard 3](https://s3-us-west-2.amazonaws.com/github-project-images/laravel-installer/install/4b-environment-wizard-3.jpg)
![Laravel web installer | Step 5](https://s3-us-west-2.amazonaws.com/github-project-images/laravel-installer/install/5-final.jpg)

###### Updater
![Laravel web updater | Step 1](https://s3-us-west-2.amazonaws.com/github-project-images/laravel-installer/update/1-welcome.jpg)
![Laravel web updater | Step 2](https://s3-us-west-2.amazonaws.com/github-project-images/laravel-installer/update/2-updates.jpg)
![Laravel web updater | Step 3](https://s3-us-west-2.amazonaws.com/github-project-images/laravel-installer/update/3-finished.jpg)

## License

Laravel Web Installer is licensed under the MIT license. Enjoy!


