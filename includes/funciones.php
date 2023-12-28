
<?php

define('TEMPLATES_URL',  __DIR__ . '/templates');
define('FUNCIONES_URL', __DIR__ . 'funciones.php');
define('CARPETA_IMAGENES', $_SERVER['DOCUMENT_ROOT'] . '/imagenes/');


function incluirTemplate(string $nombre, bool $inicio = false)
{
    include TEMPLATES_URL . "/$nombre.php";
}

function estaAutenticado()
{
    session_start();

    $auth = $_SESSION['login'];
    if (!$auth) {
        header('Location: /');
    }
}

function debuguear($variable)
{
    echo '<pre>';
    var_dump($variable);
    echo '</pre>';
    exit;
}
function s($html): string
{
    $s = htmlspecialchars($html);
    return $s;
}

function validarTipoContenido($tipo)
{
    $tipos = ['vendedor', 'propiedad'];

    return in_array($tipo, $tipos);
}

function  validarORedireccionar(string $url)
{
    //Validar la URL que sea un ID  válido
    $id = $_GET['id'];
    $id = filter_var($id, FILTER_VALIDATE_INT);

    if (!$id) {
        header("Location: $url");
    }
    return $id;
}
