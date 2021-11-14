# Time-Clock

## Setting up

### Installing PHP (taken mostly from CSE 477 course page)

You first need to install 32-bit VS 2015 redistributables if you don't have them already: https://www.microsoft.com/en-us/download/details.aspx?id=48145

Download PHP 7.0.33, the version used on the MSU servers: https://windows.php.net/downloads/releases/archives/php-7.0.33-Win32-VC14-x86.zip

Extract the files from the zip download into `C:\php` otherwise it will not work!

In the php folder after installing, copy `php.ini-production` and paste as `php.ini`

Edit the `php.ini` file and remove the semicolon from this line: `;extension=php_openssl.dll`. Also remove the semicolon from `;extension=php_mbstring.dll`. Save and exit the file.

Open a cmd prompt and do this: `cd c:\php` then run `php  --ini`. It should look like...

```cmd
c:\php>php --ini
Configuration File (php.ini) Path: C:\WINDOWS
Loaded Configuration File:         C:\php\php.ini
Scan for additional .ini files in: (none)
Additional .ini files parsed:      (none)
```

To add php to your path, open the windows environment variables editor. Under path, click edit, then add `c:\php`.

### Install NodeJS

Download and install the latest nodejs LTS version: https://nodejs.org/en/

### Install Composer

Make sure you've already installed php by running `php --ini` from the command line. 
After that, you should install the command-line based composer: https://getcomposer.org/doc/00-intro.md#installation-windows
Once installed, you should be able to run `composer --version` to verify.

#### Mac/Linux

You will have to install composer locally in the project directory. You can do this by going into the directory of your project and running `php -r "readfile('https://getcomposer.org/installer');" | php`


## notes

`php composer.phar install`

exclude node_modules from deployment

enable webpack:
`npm init`
defaults are okay

```
npm install webpack --save-dev
npm install webpack-cli --save-dev
```

to compile new JS,
```
npm run build:dev
``` 
or
```
npm run build:prod
``` 