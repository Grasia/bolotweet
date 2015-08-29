#!/bin/bash
#
#   @author Alvaro Ortego <alvorteg@ucm.es>
#

# Almacenamos la ruta actual
path=`pwd`

printf "Este script necesita ejecutar algunos comandos como SuperUsuario.\n"
read -p "Presiona [Enter] para empezar el BackUp..."

# Paramos Apache para evitar copias corruptas.
sudo -p "Introduce tu contraseña: " service apache2 stop > /dev/null 2>&1
printf "Parando Apache..."
if [ $? -eq 0 ]; then
printf "Listo!\n"
else
printf "Fallo. Abortando...\n"
exit 1
fi

timeStamp=$(date '+%F')
ext=".tar.gz"

# Creamos el nombre del contenedor final
nameFolder="backup_"$timeStamp$ext

# Creamos el nombre del contenedor de los archivos de la web.
nameFile="web_files.tar.gz";

# Creamos el nombre del archivo con la BBDD.
nameSql="bbdd.sql";

# Creamos la copia de la carpeta /var/www/ entera.

cd /var/

printf "Creando Backup de /var/www/..."
tar -Pzcf ~/$nameFile --exclude='.git' www/
if [ $? -eq 0 ]; then
printf "Listo!\n"
else
printf "Fallo. Abortando...\n"
exit 1
fi

printf "Creando Backup de la Base de Datos...\n"
mysqldump -u adminbtdb -p > ~/$nameSql
if [ $? -eq 0 ]; then
printf "Listo!\n"
else
printf "Fallo. Abortando...\n"
exit 1
fi

cd ~

printf "Creando Fichero Final..."
tar -Pzcf ~/$nameFolder {"$nameFile","$nameSql"}
if [ $? -eq 0 ]; then
printf "Listo!\n"
else
printf "Fallo. Abortando...\n"
exit 1
fi

printf "Borrando archivos temporales..."
rm ~/$nameFile ~/$nameSql
if [ $? -eq 0 ]; then
printf "Listo!\n"
else
printf "Fallo. Borre los ficheros manualmente en home.\n"
fi;

# Rearrancamos los servicios parados
printf "Iniciando Apache...\n"
sudo service apache2 start > /dev/null 2>&1
if [ $? -eq 0 ]; then
printf "Listo!\n"
else
printf "Fallo. Intente iniciar Apache manualmente.\n"
fi;

cd $path

printf "BackUp terminado\n\n"
read -p "Presiona [Enter] para terminar..."


