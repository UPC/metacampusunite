#!/bin/bash
if [ "$1" = "" ] || [ "$1" = "--help" ] ; then
echo "./ClonaTema.sh <tema_origen> <tema_destino> [ {tema_origen_capital_first} {tema_destino_capital_first} {tema_origen_all_upper} {tema_destino_all_upper} ]"
exit 1
fi
# Copiar el directorio
cp -pr $1 $2
# generar listado de ficheros a modificar
find $2 -type f > /tmp/llistafitxers
# sustituir ficheros
for fitxer in $(cat /tmp/llistafitxers) ; do
# Sustituir textos
if [ "$3" != "" ] && [ "$4" != "" ] && [ "$5" != "" ] && [ "$6" != "" ]; then
    cat $fitxer | sed "s/$1/$2/g" | sed "s/$3/$4/g" | sed "s/$5/$6/g"> $fitxer.2
else
    cat $fitxer | sed "s/$1/$2/g" > $fitxer.2
fi
mv $fitxer.2 $fitxer
done
#sustituir nombre de fichero
grep $1 /tmp/llistafitxers > /tmp/llistafitxers2
for fitxer in $(cat /tmp/llistafitxers2) ; do
# Sustituir texto origen por destino
if [ "$3" != "" ] && [ "$4" != "" ] && [ "$5" != "" ] && [ "$6" != "" ]; then
    noufitxer=`echo $fitxer | sed "s/$1/$2/g" | sed "s/$3/$4/g" | sed "s/$5/$6/g"`
else
    noufitxer=`echo $fitxer | sed "s/$1/$2/g"`
fi
mv $fitxer $noufitxer
done
