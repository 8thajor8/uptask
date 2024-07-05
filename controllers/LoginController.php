<?php

namespace Controllers;

use Model\Usuario;
use MVC\Router;
use Classes\Email;

Class LoginController{

    public static function login(Router $router){
        
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $usuario = new Usuario($_POST);

            $alertas = $usuario->validarLogin();

            if(empty($alertas)){

                $usuario = Usuario::where('email', $usuario->email);

                if(!$usuario || !$usuario->confirmado ){
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                    
                } else{
                    //el usuario existe
                    if(password_verify($_POST['password'], $usuario->password)){
                        
                        //Inicio Sesion
                        session_start();
                        $_SESSION['id'] = $usuario->id; 
                        $_SESSION['nombre'] = $usuario->nombre; 
                        $_SESSION['email'] = $usuario->email; 
                        $_SESSION['login'] = true; 

                        //Redireccionar
                        header('Location: /dashboard');

                    } else{
                        Usuario::setAlerta('error', 'El password es incorrecto');
                    }
                }
            }
        }
        $alertas = Usuario::getAlertas();
        $router->render('auth/login', ['titulo'=>'Iniciar Sesion', 'alertas'=>$alertas]);
        
    }

    public static function logout(){

        session_start();
        $_SESSION = [];
        header('Location: /');
        
    }

    public static function crear(Router $router){

        $usuario = new Usuario;
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $usuario->sincronizar($_POST);
            
            $alertas = $usuario->validarNuevaCuenta();
            
            if(empty($alertas)){
                $existeUsuario = Usuario::where('email', $usuario->email);

                if($existeUsuario){
                    Usuario::setAlerta('error', 'El usuario ya esta registrado');
                    $alertas = Usuario::getAlertas();
                }else{
                    //Hashear el password
                    $usuario->hashPassword();

                    //Eliminar password2
                    unset($usuario->password2);

                    //Generar token
                    $usuario->crearToken();

                    //Crear nuevo usuario
                    $resultado = $usuario->guardar();

                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);

                    $email->enviarConfirmacion();

                    if($resultado){
                        header('Location: /mensaje');
                    }
                }
            }

        }

        $router->render('auth/crear', ['titulo'=>'Crea tu cuenta', 'usuario'=>$usuario, 'alertas'=>$alertas]);
        
    }

    public static function olvide(Router $router){

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();


            if(empty($alertas)){
                //Buscar el usuario
                $usuario = Usuario::where('email', $usuario->email);
                
                if($usuario && $usuario->confirmado){

                    //Generar un nuevo token
                    $usuario->crearToken();
                    unset($usuario->password2);

                    //Actualizar el usuario
                    $usuario->guardar();
                    
                    //Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    //Imprimir la alerta
                    Usuario::setAlerta('exito', 'Hemos enviado instrucciones a tu email');

                } else{
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                    
                }

            }

        }
        $alertas = Usuario::getAlertas();
        $router->render('auth/olvide', ['titulo'=>'Olvide mi Password','alertas'=>$alertas]);
        
    }

    public static function reestablecer(Router $router){
        
        $alertas = [];
        $token =s( $_GET['token']);
        $mostrar = true; 

        if(!$token){
            header('Location: /');
            
        }

        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            Usuario::setAlerta('error','Token no valido');
            $mostrar = false;

        }

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarPassword();

            if(empty($alertas)){

                //Hashear el password
                $usuario->hashPassword();

                //Eliminar password2
                unset($usuario->password2);

                //Eliminar token
                $usuario->token = NULL;

                $resultado = $usuario->guardar();

                if($resultado){
                    header('Location: /');
                }

            }

        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/reestablecer', ['titulo'=>'Reestablecer Password', 'alertas'=>$alertas, 'mostrar'=>$mostrar]);
        
    }

    public static function mensaje(Router $router){

        $router->render('auth/mensaje', ['titulo'=>'Cuenta creada exitosamente!']);
        
    }

    public static function confirmar(Router $router){
        $alertas = [];
        $token =s( $_GET['token']);
        
        if(!$token){
            header('Location: /');
            
        }

        //Encontrar el usuario
        $usuario = Usuario::where('token', $token);
        
        if(empty($usuario)){
            Usuario::setAlerta('error','Token no valido');
        } else{
            $usuario->confirmado = 1;
            $usuario->token = null;
            unset($usuario->password2);

            $usuario->guardar();

            Usuario::setAlerta('exito', 'Cuenta confirmada correctamente');

        }

        $alertas = Usuario::getAlertas();


        $router->render('auth/confirmar', ['titulo'=>'Confirma tu cuenta UpTask', 'alertas'=>$alertas]);
        
    }
}