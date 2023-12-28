<?php

namespace Controllers;

use Model\Propiedad;
use Model\Vendedores;
use MVC\Router;
use Intervention\Image\ImageManagerStatic as Image;

class PropiedadController
{
    public static function index(Router $router)
    {
        $propiedades = Propiedad::all();
        $vendedores = Vendedores::all();
        $resultado = $_GET['resultado'] ?? null;

        $router->render('propiedades/admin', [
            'propiedades' => $propiedades,
            'resultado' => $resultado,
            'vendedores' => $vendedores
        ]);
    }
    public static function crear(Router $router)
    {

        $propiedad = new Propiedad;
        $vendedores = Vendedores::all();
        //Arreglo con mensaje de errores
        $errores = Propiedad::getErrores();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //Crea una nueva instancia
            $propiedad = new Propiedad($_POST['propiedad']);

            /** Subida de archivos **/
            //Generar  nombre unico para cada imagen
            $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";

            //Setear la imagen
            //Realiza un resize a la imagen con inervetion
            if ($_FILES['propiedad']['tmp_name']['imagen']) {
                $image =  Image::make($_FILES['propiedad']['tmp_name']['imagen'])->fit(800, 600);
                $propiedad->setImagen($nombreImagen);
            }

            //Validar
            $errores = $propiedad->validar();

            //Revisar que el arreglo de errores este vacio
            if (empty($errores)) {
                //Almacenar la imagen
                if ($_FILES['propiedad']['tmp_name']['imagen']) {
                    $image->save(CARPETA_IMAGENES . $nombreImagen);
                }

                //Guarda en la base de datos
                $propiedad->guardar();
            }
        }

        $router->render('propiedades/crear', [
            'propiedad' => $propiedad,
            'vendedores' => $vendedores,
            'errores' => $errores
        ]);
    }
    public static function actualizar(Router $router)
    {
        $id = validarORedireccionar('/admin');
        $propiedad = Propiedad::find($id);
        $vendedores = Vendedores::all();
        $errores = Propiedad::getErrores();

        //Ejecutar el codigo despues de que el usuario envia el formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            //Asignar  los atributos
            $args = $_POST['propiedad'];

            $propiedad->sincronizar($args);

            //Validacion
            $errores = $propiedad->validar();

            /** Subida de archivos **/
            //Generar  nombre unico para cada imagen
            $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";

            //Setear la imagen
            //Realiza un resize a la imagen con inervetion
            if ($_FILES['propiedad']['tmp_name']['imagen']) {
                $image =  Image::make($_FILES['propiedad']['tmp_name']['imagen'])->fit(800, 600);
                $propiedad->setImagen($nombreImagen);
            }

            //Revisar que el arreglo de errores este vacio
            if (empty($errores)) {
                //Almacenar la imagen
                if ($_FILES['propiedad']['tmp_name']['imagen']) {
                    $image->save(CARPETA_IMAGENES . $nombreImagen);
                }

                $propiedad->guardar();
            }
        }


        $router->render('propiedades/actualizar', [
            'propiedad' => $propiedad,
            'vendedores' => $vendedores,
            'errores' => $errores
        ]);
    }

    public static function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $id = filter_var($id, FILTER_VALIDATE_INT);

            if ($id) {
                $tipo = $_POST['tipo'];
                if (validarTipoContenido($tipo)) {
                    $propiedad = Propiedad::find($id);
                    $propiedad->eliminar();
                }
            }
        }
    }
}
