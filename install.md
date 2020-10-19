# Memoria técnica de instalación del sistema de Conciliación y subsistemas de notificaciones

Octubre 16 al 19 del 2020

Nos referimos como servidor de aplicación al servidor 10.68.8.5 y como servidor de base de datos al servidor 10.68.8.6

Para ambos servidores:

### Se actualizó a la última versión los paquetes del sistema operativo
    sudo apt-get update

### Se generó y configuró el lenguaje español de México UTF8
    sudo locale-gen es_MX.UTF-8
    sudo update-locale
    sudo cat > /etc/default/locale <<FiNLoc
        LANG="es_MX.UTF-8"
    FiNLoc
    sudo update-locale LANGUAGE=es_MX.UTF-8

### Se instaló y configuró cliente de servidor de tiempo, protocolo NTP y configuración de zona horaria CDMX
    sudo apt-get -y install ntp
    echo "America/Mexico_City" | sudo tee /etc/timezone
    sudo ln -sf /usr/share/zoneinfo/America/Mexico_City /etc/localtime
    sudo dpkg-reconfigure --frontend noninteractive tzdata

### En servidor de base de datos se realizó lo siguiente:

### Se instaló PostgreSQL server, client y librerías contrib, así como PostGis

    sudo apt -y install postgresql-12 postgresql-client-12 postgresql-contrib
    sudo apt-get -y install postgis postgresql-postgis-scripts
    sudo apt-get install gdal-bin
    sudo apt-get install libgdal-dev
    sudo systemctl restart postgresql

### Creación de Base de datos de conciliacion, usuario y permisos en servidor 10.68.8.6
    sudo -u postgres createuser concilusr
    sudo -u postgres createdb conciliacion
    sudo -u postgres psql -c "alter user concilusr with encrypted password 'concilusrpwd'"
    sudo -u postgres psql -c "grant all privileges on database conciliacion to concilusr"
    sudo -u postgres psql -c "GRANT pg_read_server_files TO concilusr"
    sudo -u postgres psql -c "GRANT pg_write_server_files TO concilusr"

### Creación de Base de datos de CP geográfico, usuario y permisos
    sudo -u postgres createuser geousr
    sudo -u postgres createdb geocps
    sudo -u postgres psql -c "alter user geousr with encrypted password 'geopax.susu'"
    sudo -u postgres psql -c "grant SELECT on geocps to geousr;"

### Creación de Base de datos de sistemas de notificaciones (una por centro), usuario y permisos
    sudo -u postgres createuser notificaciones
    sudo -u postgres psql -c "alter user notificaciones with encrypted password 'notific4cci0nes!'"
    sudo -u postgres psql -c "grant all privileges on database signo15 to notificaciones"
    #ALTER ROLE notificaciones SUPERUSER;
    #ALTER ROLE notificaciones NOSUPERUSER;

### Se creó base de datos del sistema Signo (Sistema de Gestión de Notificaciones) para instancia edomex
    sudo -u postgres createdb signo15
    sudo -u postgres psql -c "grant SELECT on signo15 to notificaciones;"

### Se configuró el pg_hba.conf y postgresql.conf
#### pg_hba.conf
    host    all             all             10.68.8.5/24            md5
### #postgresql.conf
    listen_addresses = '*'

### Se instalaron servidor no SQL redis
    sudo apt-get install redis-server

### En servidor de aplicación se realizó lo siguiente

### Instalación de php 7.4, 5.6 apache2 y librerías
    sudo add-apt-repository ppa:ondrej/php
    sudo add-apt-repository ppa:ondrej/apache2
    sudo apt install libapache2-mod-fcgid
    sudo apt-get -y install php7.4 libapache2-mod-php7.4 php7.4-xml php7.4-curl php7.4-imagick php7.4-pgsql php7.4-zip php7.4-common php7.4-bcmath openssl php7.4-json php7.4-mbstring php7.4-intl
    sudo apt-get -y install php5.6 libapache2-mod-php5.6 php5.6-fpm php5.6-xml php5.6-curl php5.6-imagick php5.6-pgsql php5.6-zip php5.6-common php5.6-bcmath openssl php5.6-json php5.6-mbstring php5.6-intl
    sudo update-alternatives --set php /usr/bin/php7.4

### Instalación de composer
    sudo php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    sudo HASH="$(wget -q -O - https://composer.github.io/installer.sig)"
    sudo php -r "if (hash_file('SHA384', 'composer-setup.php') === '$HASH') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
    sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer

### Habilitación de módulo rewrite de apache
    sudo a2enmod rewrite

### Se habilita proxy cgi y fcgid para tener ambas versiones de php corriendo
    sudo a2enmod actions alias proxy_fcgi fcgid

### Se generó llave en el servidor para compartir con el repositorio en github
    ssh-keygen -t rsa -b 4096 -C "edgar.orozco.juarez@gmail.com"
    cat ~/.ssh/id_rsa.pub

Se Agregó el contenido de la llave pública al servidor GITHUB donde está el repositorio de código

### Se clonó en el directorio web el código del sistema de conciliación
    cd /tmp/
    git clone git@github.com:edgar-orozco/ConciliacionSTPS.git conciliacion
    sudo mv conciliacion /var/www/
    sudo chown 4d4zdesc0 -R /var/www/conciliacion
    cd /var/www/conciliacion

### Se instalaron todas las librerías y dependencias con composer
    composer install

### Se modificaron permisos de storage y bootstrap/cache
    sudo chgrp -R 4d4zdesc0:www-data storage bootstrap/cache
    sudo chmod -R ug+rwx storage bootstrap/cache
    sudo find /var/www/conciliacion -type f -exec chmod 644 {} \;
    sudo find /var/www/conciliacion -type d -exec chmod 775 {} \;
    sudo chown -R 4d4zdesc0:www-data storage bootstrap/cache
    sudo chmod -R ug+rwx storage bootstrap/cache

### Se generó llave de aplicación y ejecutó migraciones
    php artisan key:generate
    php artisan migrate
    php artisan passport:keys
    php artisan web-tinker:install

### Se instaló imagemagick para procesamiento de imágenes de identificaciones y documentos, procesamiento de PDF, etc.
    sudo apt -y install imagemagick

### Se generó el link storage/app/public con el dir public expuesto a web
    php artisan storage:link

### Se configuró el directorio de archivos estáticos en /storage
    mkdir /storage/conciliacion
    mv app /storage/conciliacion/
    ln -s /storage/conciliacion/app /var/www/conciliacion/storage

### Se configuraron variables para producción del php.ini
* https://raw.githubusercontent.com/php/php-src/master/php.ini-production
* Se modifica límite de memoria de procesos a 512MB
* Se modifica límite de post de 20MB
* Se modifica límite de upload file a 20MB

### Se Hizo global la aplicación wkhtmltopdf que genera los documentos PDF emitidos por el sistema
    cd /var/www/conciliacion/vendor/bin
    sudo cp wkhtmltopdf-amd64 /usr/local/bin/wkhtmltopdf
    sudo cp wkhtmltoimage-amd64 /usr/local/bin/wkhtmltoimage
    sudo chmod +x /usr/local/bin/wkhtmltopdf
    sudo chmod +x /usr/local/bin/wkhtmltoimage

### Se copiaron los assets, videos, imágenes y scripts de terceros
    cd /var/www/conciliacion/public/
    rsync -av root@conciliacion.lxl.mx:/var/www/capconciliacion/public/js .
    
    cd /var/www/conciliacion/public/assets
    rsync -av root@conciliacion.lxl.mx:/var/www/capconciliacion/public/assets/css .
    
    cd /var/www/conciliacion/public/assets/img
    rsync -av root@conciliacion.lxl.mx:/var/www/capconciliacion/public/assets/img/theme .
    rsync -av root@conciliacion.lxl.mx:/var/www/capconciliacion/public/assets/img/version .
    rsync -av root@conciliacion.lxl.mx:/var/www/capconciliacion/public/assets/img/logo .
    rsync -av root@conciliacion.lxl.mx:/var/www/capconciliacion/public/assets/img/asesoria .
    
    cd /var/www/conciliacion/public/assets
    rsync -av root@conciliacion.lxl.mx:/var/www/capconciliacion/public/assets/plugins .
    
    cd /var/www/conciliacion/public/assets/js/demo
    rsync -av root@conciliacion.lxl.mx:/var/www/capconciliacion/public/assets/js/demo/form-slider-switcher.demo.js .
    rsync -av root@conciliacion.lxl.mx:/var/www/capconciliacion/public/assets/js/demo/form-wysiwyg.demo.js .
    rsync -av root@conciliacion.lxl.mx:/var/www/capconciliacion/public/assets/js/demo/form-multiple-upload.demo.js .
    rsync -av root@conciliacion.lxl.mx:/var/www/capconciliacion/public/assets/js/demo/timeline.demo.js .
    
    cd /usr/local/share/fonts/
    rsync -av root@conciliacion.lxl.mx:/usr/local/share/fonts/Montserrat.tgz .

### Se realizó la misma operación para subsistema de notificaciones
### Se configuró una entrada vhost para cada sitio

### Se eliminó el reporte de firma de servidor web
    vim /etc/apache2/conf-enabled/security.conf
    ServerTokens Prod
    ServerSignature Off
    
### Se configuraron los protocolos que se sirven para cuando se configuren los certificados SSL
    sudo vim /etc/apache2/mods-available/ssl.conf
    SSLProtocol all -SSLv2 -SSLv3



