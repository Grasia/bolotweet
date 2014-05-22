#!/bin/bash
#
#   @author Alvaro Ortego <alvorteg@ucm.es>
#
# Este script está hecho para convertir los ficheros xlsx o xls con los usuarios
# a formato csv, para poder posteriormente registrarlos.
# 
# Se debe respetar la siguiente estructura:
# 
#   - El archivo tiene que tener la extensión xls o xlsx.
#   - La primera línea debe ser el nombre de la asignatura. 
#   - La segunda línea debe ser el nombre de la columna.
#
# El script se ejecuta de la siguiente forma:
# 	./convertirCSV nombreCSV
#
# Siendo nombreCSV el nombre del fichero con la lista de usuarios.

# Datos
ext=".csv"

# Convertimos el fichero a csv.
libreoffice --headless --convert-to csv $1

# Seleccionamos el nombre del fichero sin extensión.
name=( $(echo $1 | cut -d '.' -f1) )

# Creamos el nombre del fichero creado por libreoffice.
fullname=$name$ext

# Eliminamos las dos primeras líneas (Nombre asignatura y nombre columnas).
sed -i "1,2d" $fullname

# Convertimos a UTF-8 para mantener tildes y otros caracteres

iconv -f ISO-8859-1 -t UTF-8 -o $fullname $fullname
