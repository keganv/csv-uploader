# CSV Uploader Application



## Install the Application

Installing and getting the application up and running is a breeze. ;)

These directions assume that you have [Composer](https://getcomposer.org/) installed globally. If not, you will need to
run the commands starting with `php composer.phar`.

Run the following commands below in your CLI. You should be in the parent directory of where your application will live.

Typically the directory is `~/Sites/` on a MacOS and `/var/www/` on an Apache or Nginx web server. However, your
configuration may be different.
    
    cd [applications_directory]
    git clone https://github.com/keganv/csv-uploader [your_custom_name]
    cd [your_custom_name]
    composer install

Open the settings.php file in the `src` directory and update with your database credentials.

    php vendor/bin/doctrine orm:schema-tool:update --force
    
* Point your virtual host document root to your new application's `public` directory.
* Ensure `logs` directory is web writeable.

Run this command in the application directory to run the test suite.

	phpunit
	
## Credits

[Slim 3 Framework](https://www.slimframework.com)<br/>
[Composer](https://getcomposer.org/)<br/>
[League\Csv](https://csv.thephpleague.com)<br/>
[Slim Skeleton](https://github.com/slimphp/Slim-Skeleton)<br/>
[Doctrine ORM](https://www.doctrine-project.org/)