COMPOSER INSTALL :

	$ composer install


VHOST :

	$ sudo nano /etc/apache2/sites-available/scrumbe



        <VirtualHost *:80>
                ServerAdmin webmaster@localhost
                ServerName scrumbe
                ServerAlias www.scrumbe

                DocumentRoot /var/www/scrumbe/web
                <Directory /var/www/scrumbe/web>
                        Options Indexes FollowSymLinks MultiViews
                        AllowOverride All
                        Order allow,deny
                        allow from allsu
                </Directory>

                ScriptAlias /cgi-bin/ /usr/lib/cgi-bin/

                ErrorLog ${APACHE_LOG_DIR}/error.log

                # Possible values include: debug, info, notice, warn, error, crit,
                # alert, emerg.
                LogLevel warn

                CustomLog ${APACHE_LOG_DIR}/access.log combined
        </VirtualHost>

___

	$ sudo a2ensite scrumbe
	$ sudo service apache2 reload



Host à rajouter (Windows ou Mac + Vagrant) :

    127.0.0.1       scrumbe
