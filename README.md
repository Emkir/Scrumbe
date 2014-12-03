COMPOSER INSTALL :

	$ composer install


VHOST :

	$ sudo nano /etc/apache2/sites-available/scrumbe.loc.com



        <VirtualHost *:80>
                ServerAdmin webmaster@localhost
                ServerName scrumbe.loc.com
                ServerAlias www.scrumbe.loc.com

                DocumentRoot /var/www/Scrumbe/web
                <Directory /var/www/Scrumbe/web>
                        Options Indexes FollowSymLinks MultiViews
                        AllowOverride All
                        Order allow,deny
                        allow from all
                </Directory>

                ScriptAlias /cgi-bin/ /usr/lib/cgi-bin/

                ErrorLog ${APACHE_LOG_DIR}/error.log

                # Possible values include: debug, info, notice, warn, error, crit,
                # alert, emerg.
                LogLevel warn

                CustomLog ${APACHE_LOG_DIR}/access.log combined
        </VirtualHost>

___

	$ sudo a2ensite scrumbe.loc.com
	$ sudo service apache2 reload



Host à rajouter (Windows ou Mac + Vagrant) :

    127.0.0.1       scrumbe.loc.com
    
Quelques alias utiles (~/.bashrc):

	alias pac='php app/console'
	alias resetDB='php app/console propel:database:drop --force && php app/console propel:database:create && php app/console propel:build --insert-sql && php app/console propel:fixtures:load'
	alias updateDB='php app/console propel:migration:generate-diff && php app/console propel:migration:migrate'
	alias scrumbe='cd /var/www/Scrumbe'
