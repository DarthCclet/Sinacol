# ConciliacionSTPS
Sistema de gestión de conciliaciones laborales
## Instalación
### Clonar el repositorio en el directorio de trabajo
      $ cd ~/miworkspace/
      $ git clone https://github.com/edgar-orozco/ConciliacionSTPS.git
### Instalar máquina virtual
* Instalar [Vagrant](https://www.vagrantup.com/downloads.html)
* Instalar [VirtualBox](https://www.virtualbox.org/wiki/Downloads)
* Descargar box de Homestead y programa de configuración

      $ vagrant box add laravel/homestead
      $ git clone https://github.com/laravel/homestead.git ~/Homestead
      $ cd Homestead
      
      // Mac / Linux...
      $ bash init.sh

      // Windows...
      $ init.bat
      
Al correr init se va a generar el archivo "Homestead.yaml". Este es el archivo de configuración de la máquina virtual

Editar el mapeo entre el directorio de trabajo (donde residirá el código que vamos a editar localmente) y el 
directorio donde se encontrará el directorio que publicará el servidor web en la máquina virtual (/var/www/conciliacion)

      folders:
        - map: ~/miworkspace/ConciliacionSTPS
          to: /var/www/conciliacion
          type: "nfs"

**Nota:** el campo *type:"nfs"* ayuda a mejorar el desempeño al sincronizar archivos entre la máquina host y la virtual en mac y linux, en windows no hace ninguna diferencia este campo y es mejor omitir la línea type: "nfs"

### Configurar dominio local y directorio public html del servidor web

    sites:
      - map: conciliacion.test
        to: /var/www/conciliacion/public

### Configurar el nombre de la base de datos

    database:
      - conciliacion
 
### Configurar resolución de nombres
El nombre propuesto para el desarrollo local es https://conciliacion.test los nombres como .dev o .local no funcionan correctamente en linux y mac bajo vagrant, si se desea modificar el nombre se debe contemplar lo anterior.

Agregar la ip de la máquina guest al archivo de configuración hosts de la máquina home

      // Mac / Linux
      /etc/hosts
      
      // Windows
      C:\Windows\System32\drivers\etc\hosts.
      
      192.168.10.10  conciliacion.test
 
      

## Paquetes de terceros

* Soft deletes en cascada [cascade-soft-deletes](https://github.com/michaeldyrynda/laravel-cascade-soft-deletes)
* Representación y manejo de estructuras jerárquicas: [Nestedset](https://github.com/lazychaser/laravel-nestedset)
* Auditoría de Entidades [laravel-auditing](https://github.com/owen-it/laravel-auditing)
* Administración de permisos dinámicos [laravel-permission](https://github.com/spatie/laravel-permission)
* Administración de menús dinámicos [laravel-menu](https://github.com/lavary/laravel-menu)
* Oauth 2 Server con Passport. [laravel/passport](https://laravel.com/docs/6.x/passport)

//PAG
