<?php
    /* 
        ------------------------------------------------------ FUENTES ------------------------------------------------------
        https://auth0.com/blog/adding-salt-to-hashing-a-better-way-to-store-passwords/#Generating-a-Good-Random-Salt
        https://www.php.net/manual/en/function.openssl-random-pseudo-bytes.php
        https://cheatsheetseries.owasp.org/cheatsheets/Password_Storage_Cheat_Sheet.html#work-factors
        https://www.php.net/manual/es/function.hash
        https://es.wikipedia.org/wiki/Secure_Hash_Algorithm#SHA-3
        ---------------------------------------------------------------------------------------------------------------------
    */

    /*
        Salt: es una cadena generada de forma aleatoria que se añade a la contraseña para generar hashes unicos entre contraseñas,
        esta cadena debe ser unica para cada usuario y se guarda en la base de datos junto con los datos del usuario,
        de esta forma dos usuarios con la misma contraseña no tendran el mismo hash almacenado en la base de datos.
        Esta cadena tambien protege contra ataques cuando se filtran los datos de un usuario en otro servicio puesto que aunque 
        la contraseña en ambos servicios sea la misma en los 2 servicios se tendra un salt distinto, con lo que para conseguir 
        entrar en la cuenta del usuario hara falta crakear el hash.
        - El minimo de longitud de la cadena para que tenga algo de seguridad el salt es de 16 bytes.

        función generarSalt: función que genera una cadena de 16 bytes usando una función criptográficamente segura.
        RETORNO:
        > cadena que formara el Salt que se añadira a la contraseña para generar el hash
    */
    function generarSalt(){
        $modoSeguro = true;
        return openssl_random_pseudo_bytes(16, $modoSeguro);
    }

    /*
        función generarHash: función que calcula el hash correspondiente a una contraseña indicada, en el parámetro 
        se debe indicar la contraseña con el salt y el pepper añadido.
        La funcion genera el Hash en base a un Work Factor de 2 iteracciones para hacer más costoso el ataque al hash.
        RETORNO:
        > $hash2: varaible que guarda el hash que se genera del hash de la contraseña con el salt y el pepper, 
        es la que se va a guardar como hash en la base de datos
    */
    function generarHash( $password ){
        $hash1 = hash('sha3-512', $password, false);
        $hash2 = hash('sha3-512', $hash1, false);

        return $hash2;
    }

    /*
        Pepper: cadena de caracteres que se añade a la contraseña junto con la cadena 'salt' para generar 
        el hash que se guarda en la base de datos, el pepper no se guarda en la base de datos se 
        debe guardar en un fichero en una ubicación segura, tiene como objetivo generar un hash que 
        no permita al atacante crackear el hash y conocer la contraseña directamente si tiene 
        acceso a la base de datos. Usando esto junto con el salt permite que varios usuarios 
        con la misma contraseña tengan distintos ashes y no se puedan hacer ataques usando datos filtrados de otros sitios.
        - El minimo de longitud de la cadena para que tenga algo de seguridad el pepper es de 32 bytes.

        función obtenerPepper: función que lee del fichero el pepper para añadir a la contraseña al generar el hash.
        RETORNO:
        > una cadena que contiene el pepper que se va a añadir a la constraseña para generar el hash
    */
    function obtenerPepper(){
        return file_get_contents('pepper.txt');
    }

    if(isset($_POST['pass']) && !empty($_POST['pass']) ){
        $hashSHA256= hash('sha256', $_POST['pass'], false);
        $hashSHA512= hash('sha512', $_POST['pass'], false);
        $hashSHA512a= hash('sha3-512', $_POST['pass'], false);

        $salt= generarSalt();
        $salted = $salt.$_POST['pass'];
        $saltedHashSHA256= hash('sha256', $salted, false);
        $saltedHashSHA512= hash('sha512', $salted, false);
        $saltedHashSHA512a= hash('sha3-512', $salted, false);

        $hash = generarHash($salt.$_POST['pass'].obtenerPepper());
    }

?>

<form method="POST" action="">
    <label for="pass">Indica el texto para generar el hash:</label><br>
    <input type="text" id="pass" name="pass"><br>
    <input type="submit" value="Calcular">

</form>


<?php

    /*echo '<hr>';
    echo 'Pepper generado: "'.generarSalt().generarSalt().'"';*/
    
    if(isset($_POST['pass']) && isset($_POST['pass']) ){
        echo '<hr>';
        echo 'Texto indicado: "'.$_POST['pass'].'"';
        echo '<br>';
        echo 'Hash calculado con SHA256   =  '.$hashSHA256;
        echo '<br>';
        echo 'Hash calculado con SHA512   =  '.$hashSHA512;
        echo '<br>';
        echo 'Hash calculado con SHA3-512 ='.$hashSHA512a;

        echo '<hr>';
        echo 'Salt generado : "'.$salt.'", texto con el salt "'.$salted.'"';
        echo '<br>';
        echo 'Hash calculado con SHA256   =  '.$saltedHashSHA256;
        echo '<br>';
        echo 'Hash calculado con SHA512   =  '.$saltedHashSHA512;
        echo '<br>';
        echo 'Hash calculado con SHA3-512 ='.$saltedHashSHA512a;


        echo '<hr>';
        echo '<strong> ---- A GUARDAR EN LA BASE DE DATOS ---- </strong>';
        echo '<br>';
        echo 'Salt > '.$salt;
        echo '<br>';
        echo 'Hash > '.$hash;
    }

?>