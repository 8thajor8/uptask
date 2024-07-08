<?php

namespace Controllers;

use MVC\Router;
use Model\Usuario;
use Model\Proyecto;
//use Classes\Email;

Class DashboardController{

    public static function index(Router $router){
        
        session_start();
        isAuth();

        $id = $_SESSION['id'];
        $proyectos = Proyecto::belongsTo('propietarioId', $id);
        
        $router->render('dashboard/index',['titulo'=>'Proyectos', 'proyectos'=>$proyectos]);
    }

    public static function crear_proyecto(Router $router){

        session_start();
        isAuth();
        
        $alertas=[];
        $proyecto = new Proyecto();

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            
            $proyecto = new Proyecto($_POST);

            $alertas = $proyecto->validarProyecto();

            if(empty($alertas)){
                //Generar una URL unica
                $hash = md5(uniqid());
                $proyecto->url = $hash;
                //Almacenar el creador del Proyecto
                $proyecto->propietarioId = $_SESSION['id'];

                //Guardar el proyecto
                $proyecto->guardar();
                header('Location: /proyecto?id=' . $proyecto->url);
            }
        }
        $alertas = Proyecto::getAlertas();
        $router->render('dashboard/crear-proyecto',['titulo'=>'Crear Proyecto', 'alertas'=>$alertas]);
    }

    public static function proyecto(Router $router){

        session_start();
        isAuth();
        
        $token = $_GET['id'];
        
        if(!$token){
            header('Location: /dashboard');
        }
        //Revisar que la persona que visita el proyecto es quien lo creo
        $proyecto = Proyecto::where('url', $token);
        if($proyecto->propietarioId !== $_SESSION['id']){
            header('Location: /dashboard');
        }
        
        
        $router->render('dashboard/proyecto',['titulo'=>$proyecto->proyecto]);
    }

    public static function perfil(Router $router){

        session_start();
        isAuth();
        
        $alertas=[];

        $usuario = Usuario::find($_SESSION['id']);
        
        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            
            $usuario->sincronizar($_POST);

            
            $alertas = $usuario->validarPerfil();

            
            if(empty($alertas)){

                $existeUsuario = Usuario::where('email', $usuario->email);

                if($existeUsuario && $existeUsuario->email !== $usuario->email){

                    Usuario::setAlerta('error', 'El usuario ya existe');
                } else{

                    $usuario->guardar();

                    Usuario::setAlerta('exito', 'Guardado Correctamente');
                    

                    $_SESSION['nombre'] = $usuario->nombre;

                }
                
                
            }

           
        }
        $alertas = $usuario->getAlertas();
        $router->render('dashboard/perfil',['titulo'=>'Perfil', 'usuario'=> $usuario, 'alertas'=>$alertas]);
    }

    public static function cambiar_password(Router $router){

        session_start();
        isAuth();
        $alertas=[];
        $usuario = Usuario::find($_SESSION['id']);

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            
            $usuario->sincronizar($_POST);

            $alertas = $usuario->nuevo_password();
            
            if(empty($alertas)){

                $resultado = $usuario->comprobar_password();
                
                if($resultado){
                    

                    $usuario->password = $usuario -> password_nuevo;

                    unset($usuario->password_actual);
                    unset($usuario->password_nuevo);

                    $usuario->hashPassword();

                    $resultado = $usuario->guardar();

                    if($resultado){
                        Usuario::setAlerta('exito','Password cambiado con exito');
                    }
                    
                } else{
                    Usuario::setAlerta('error', 'El password es incorrecto');
                }
            }
        }
        
        $alertas = $usuario->getAlertas();
        $router->render('dashboard/cambiar-password',['titulo'=>'Cambiar Password', 'usuario'=> $usuario, 'alertas'=>$alertas]);
    }

}