<?php

namespace Controllers;

use MVC\Router;
use Model\Propiedad;
use PHPMailer\PHPMailer\PHPMailer;

class PaginasController
{
    public static function index(Router $router)
    {
        $inicio = true;
        $propiedades = Propiedad::get(3);
        $router->render('paginas/index', [
            'propiedades' => $propiedades,
            'inicio' => $inicio
        ]);
    }
    public static function nosotros(Router $router)
    {
        $router->render('paginas/nosotros');
    }
    public static function anuncios(Router $router)
    {
        $propiedades = Propiedad::all();

        $router->render('paginas/anuncios', [
            'propiedades' => $propiedades
        ]);
    }
    public static function anuncio(Router $router)
    {
        //Validar la URL que sea un ID  válido
        $id = validarORedireccionar('/anuncios');

        $propiedad = Propiedad::find($id);

        $router->render('paginas/anuncio', [
            'propiedad' => $propiedad
        ]);
    }
    public static function blog(Router $router)
    {
        $router -> render('paginas/blog', [

        ]);
    }
    public static function entrada(Router $router)
    {

        $router -> render('paginas/entrada');
    }
    public static function contacto(Router $router)
    {
        $mensaje = null;
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
            $respuesta = $_POST['contacto'];
            //Crear una instancia de PHPMailer
            $mail = new PHPMailer();
            //Configurar SMTP
            $mail -> isSMTP();
            $mail -> Host = $_ENV['EMAIL_HOST'];
            $mail -> SMTPAuth = true;
            $mail -> Username = $_ENV['EMAIL_USER'];
            $mail -> Password = $_ENV['EMAIL_PASS'];
            $mail -> SMTPSecure = 'tls';
            $mail -> Port = $_ENV['EMAIL_PORT'];

            //Configurar el contenido del email
            $mail -> setFrom('admin@bienesraices.com');
            $mail -> addAddress('admin@bienesraices.com', 'BienesRaices.com');
            $mail -> Subject = 'Tienes un mensaje nuevo';

            //Habilitar HTML
            $mail -> isHTML(true);
            $mail -> CharSet = 'UTF-8';

            //Definir el contenido
            $contenido  = '<html>';
            $contenido .= '<p>Tienes un mensaje nuevo</p>';
            $contenido .= '<p>Nombre: '. $respuesta['nombre'] . '</p>';
            
            //Enviar de forma condicional algunos campos
            if ( $respuesta['contacto'] === 'telefono' ){
                $contenido .= '<p>Eligió ser contactado por Teléfono</p>';
                $contenido .= '<p>Teléfono: '. $respuesta['telefono'] . '</p>';
                $contenido .= '<p>Fecha de Creación: '. $respuesta['fecha'] . '</p>';
                $contenido .= '<p>Hora: '. $respuesta['hora'] . '</p>';
            }else{
                //Es email, entonces agregamos el campo email
                $contenido .= '<p>Eligió ser contactado por Email</p>';
                $contenido .= '<p>Email: '. $respuesta['email'] . '</p>';
            }
            $contenido .= '<p>Mensaje: '. $respuesta['mensaje'] . '</p>';
            $contenido .= '<p>Vende o Compra: '. $respuesta['tipo'] . '</p>';
            $contenido .= '<p>Precio o Presupuesto:  $'. $respuesta['precio'] . '</p>';

            $contenido .= '</html>';

            $mail -> Body = $contenido;


            //Enviar el email
            if ( $mail->send() ){
                $mensaje = 'Mensaje enviado Correctamente';
            }else{
                $mensaje = 'El mensaje no se pudo enviar';
            }
        }

        $router -> render('paginas/contacto', [
            'mensaje' => $mensaje
        ]);
    }
}
