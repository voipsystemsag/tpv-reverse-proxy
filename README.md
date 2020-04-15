PROXY INVERSO PARA EL API TPV
=============================

Ejemplo de uso de un proxy inverso para la exposición del API de TPV en forma de
*marca blanca*.

Instalación
-----------

1. Descargue el código fuente.
2. Instale la dependencias usando `composer install`
3. Cree una copia llamada *.env* desde el archivo *.env.dist* y configure las
   variables que se encuentran ahí según tu entorno de ejecución.
4. Recuerde añadir a la lista de direcciones IP del proyecto la dirección IP
   desde donde el script del proxy va a ser ejecutado. Si no lo hace las
   llamadas serán rechazadas con un estado HTTP 401.

Ejemplos de uso
---------------

Acceda desde un navegador al archivo proxy y use algún *endpoint* del API TPV.

Ejemplo: suponga que el script proxy está accesible en su ordenador en la ruta
`http://localhost/api/proxy.php`:

Acceda a la lista de los operadores de Venezuela:

```
http://localhost/api/proxy.php/operators?auth=$3cr3t
```

Acceda a la lista de recargas realizadas usando el proyecto asociado:

```
http://localhost/api/proxy.php/topups?auth=$3cr3t
```

Acceda a una recarga específica

```
http://localhost/api/proxy.php/topups/000000-00000...0000000?auth=$3cr3t
```

Recupere los operadores que operan en un determinado país

```
http://localhost/api/proxy.php/operators?auth=$3cr3t&country=XX
```