<?php 

namespace Controllers;

use MVC\Router;
use Model\Vendedores;

class VendedorController{
    public static function crear(Router $router){
        $vendedor = new Vendedores;
        $errores = Vendedores::getErrores();

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
            //Crear una nueva instancia
            $vendedor = new Vendedores($_POST['vendedor']);
            //Validar que no haya campos vacios
            $errores = $vendedor -> validar();
        
            //No hay errores
            if ( empty($errores) ){
                $vendedor->guardar();
            }
        }

        $router -> render('vendedores/crear',[
            'vendedor' => $vendedor,
            'errores' => $errores
        ]);
    }

    public static function actualizar(Router $router){

        $errores = Vendedores::getErrores();
        $id = validarORedireccionar('/admin');
        //Obtener los datos del vendedor a actualizar
        $vendedor = Vendedores::find($id);
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
            //Asignar los valores
            $args = $_POST['vendedor'];
            //Sincronizar objeto en memoria con lo que escribio el usuario
            $vendedor -> sincronizar($args);
            //Validar los datos
            $errores = $vendedor->validar();
            if ( empty( $errores ) ){
                $vendedor -> guardar();
            }
        }

        $router  -> render('vendedores/actualizar', [
            'errores' => $errores,
            'vendedor' => $vendedor
        ]);
    }
    public static function eliminar(){
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
            //Validar por el id
            $id = $_POST['id'];
            $id = filter_var($id, FILTER_VALIDATE_INT);
            if ( $id ){
                $tipo = $_POST['tipo'];

                if ( validarTipoContenido( $tipo ) ){
                    $vendedor = Vendedores::find($id);

                    $vendedor -> eliminar();
                }
            }

        }
    }

}