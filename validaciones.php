<?php
function validarContraseña($pass1) {
    // Verificar que la contraseña tenga al menos 8 caracteres
    // y contenga al menos una letra mayúscula, una letra minúscula y un número.
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $pass1);
}
?>