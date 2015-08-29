#!/bin/bash
#
#   @author Alvaro Ortego <alvorteg@ucm.es>
#


# Almacenamos la ruta actual
path=`pwd`

if [ -z "$1" ];  then
printf "No se ha indicado el nombre del fichero de BackUp\n"
printf "Abortando...\n"
exit 1
fi

printf "Este script necesita ejecutar algunos comandos como SuperUsuario.\n"
read -p "Presiona [Enter] para empezar la restauración..."

# Paramos Apache para evitar copias corruptas.
sudo -p "Introduce tu contraseña: " service apache2 stop > /dev/null 2>&1
printf "Parando Apache..."
if [ $? -eq 0 ]; then
printf "Listo!\n"
else
printf "Fallo. Abortando...\n"
exit 1
fi

# Nombre del contenedor de los archivos de la web.
nameFile="web_files.tar.gz";

# Nombre del archivo con la BBDD.
nameSql="bbdd.sql";

# Nos movemos donde está el archivo de BackUp
cd ~

# Extraemos los archivos.

if [ -e "$1" ]; then
printf "Extrayendo fichero BackUp..."
tar -xzf $1
if [ $? -eq 0 ]; then
printf "Listo!\n"
else
printf "Fallo. Abortando...\n"
exit 1
fi
else
printf "Fichero de BackUp inexistente.\n"
printf "Abortando...\n"
exit 1
fi

if [ -e "$nameFile" ]; then
printf "Extrayendo fichero secundario..."
tar -xzf $nameFile
if [ $? -eq 0 ]; then
printf "Listo!\n"
else
printf "Fallo. Abortando...\n"
exit 1
fi
else
printf "Error con el archivo de BackUp.\n"
printf "Revisa que no esté corrupto\n"
printf "Abortando...\n"
exit 1
fi

# Borramos la carpeta /var/www/ si es que tiene contenido.
printf "Borrando contenido de carpeta /var/www/..."
sudo rm -r /var/www/* > /dev/null 2>&1
if [ $? -eq 0 ]; then
printf "Listo!\n"
else
printf "Fallo. Ya estaba vacío.\n"
fi
# Copiamos el nuevo contenido
printf "Copiando nuevo contenido a carpeta /var/www/..."
sudo cp -R www/* /var/www/
if [ $? -eq 0 ]; then
printf "Listo!\n"
else
printf "Fallo. Abortando...\n"
exit 1
fi


# Borramos la database por si existe.
printf "Borrando database antigua...\n"
mysqladmin -uadminbtdb -p -f drop bolotweetdb 2>/dev/null

if [ $? -eq 0 ]; then
printf "Listo!\n"
else
printf "Fallo. Ya estaba borrada.\n"
fi;

# Creamos la nueva database.
printf "Creando database nueva..."
mysqladmin -u "uadminbtdb" --password create bolotweetdb

if [ $? -eq 0 ]; then
printf "Listo!\n"
else
printf "Fallo. Abortando...\n"
exit 1;
fi;


# Restauramos el BackUp de la BBDD
printf "Restaurando Backup de la Base de Datos...\n"
mysql -u uadminbtdb -p < $nameSql

if [ $? -eq 0 ]; then
printf "Listo!\n"
else
printf "Fallo. Abortando...\n"
exit 1;
fi;

printf "Borrando archivos temporales..."
rm -R www/ bbdd.sql web_files.tar.gz
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

printf "Restauración terminada\n\n"
read -p "Presiona [Enter] para terminar..."

