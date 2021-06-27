# SecurePasswordPHP
Prueba de implementación de almacen seguro de contraseñas usando Hashing, salting y peppering


### Salt: 
Es una cadena generada de forma aleatoria que se añade a la contraseña para generar hashes unicos entre contraseñas,
esta cadena debe ser unica para cada usuario y se guarda en la base de datos junto con los datos del usuario,
de esta forma dos usuarios con la misma contraseña no tendran el mismo hash almacenado en la base de datos.
Esta cadena tambien protege contra ataques cuando se filtran los datos de un usuario en otro servicio puesto que aunque 
la contraseña en ambos servicios sea la misma en los 2 servicios se tendra un salt distinto, con lo que para conseguir 
entrar en la cuenta del usuario hara falta crakear el hash.
- El minimo de longitud de la cadena para que tenga algo de seguridad el salt es de 16 bytes.

### Pepper: 
Cadena de caracteres que se añade a la contraseña junto con la cadena 'salt' para generar 
el hash que se guarda en la base de datos, el pepper no se guarda en la base de datos se 
debe guardar en un fichero en una ubicación segura, tiene como objetivo generar un hash que 
no permita al atacante crackear el hash y conocer la contraseña directamente si tiene 
acceso a la base de datos. Usando esto junto con el salt permite que varios usuarios 
con la misma contraseña tengan distintos ashes y no se puedan hacer ataques usando datos filtrados de otros sitios.
- El minimo de longitud de la cadena para que tenga algo de seguridad el pepper es de 32 bytes.

La funcion genera el Hash en base a un Work Factor de 2 iteracciones para hacer más costoso el ataque al hash.

###  FUENTES 
- https://auth0.com/blog/adding-salt-to-hashing-a-better-way-to-store-passwords/#Generating-a-Good-Random-Salt
- https://www.php.net/manual/en/function.openssl-random-pseudo-bytes.php
- https://cheatsheetseries.owasp.org/cheatsheets/Password_Storage_Cheat_Sheet.html#work-factors
- https://www.php.net/manual/es/function.hash
- https://es.wikipedia.org/wiki/Secure_Hash_Algorithm#SHA-3
