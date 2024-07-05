<?php

namespace Controllers;

use Model\Tarea;
use Model\Proyecto;
use MVC\Router;
//use Classes\Email;

Class TareaController{

    public static function index(){

        session_start();
        $proyectoId = $_GET['id'];

        if(!$proyectoId){
            header('Location: /dashboard');
        }

        $proyecto = Proyecto::where('url', $proyectoId);

        if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']){
            header('Location: /404');
        };

        $tareas = Tarea::belongsTo('proyectoId', $proyecto->id);

        echo json_encode(['tareas' => $tareas ]);
    }

    public static function crear(){

        session_start();

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            
            
            $tarea = new Tarea($_POST);

            $proyecto = Proyecto::where('url',$tarea->proyectoId);

            if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']){
                $respuesta = [
                    'tipo'=> 'error',
                    'mensaje' => 'Error al agregar la tarea'
                ];
                echo json_encode($respuesta);
                return $respuesta;
                
            } 
            
            $tarea->proyectoId = $proyecto->id;
            $resultado = $tarea->guardar();
            $respuesta = [
                'tipo'=> 'exito',
                'mensaje' => 'Tarea agregada exitosamente',
                'proyectoId' => $proyecto->id
            ]; 

            echo json_encode($respuesta);
        }
    }

    public static function actualizar(){

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            
            session_start();
            $proyecto = Proyecto::where('url', $_POST['url']);

            if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']){
                $respuesta = [
                    'tipo'=> 'error',
                    'mensaje' => 'Error al actualizar la tarea'
                ];
                echo json_encode($respuesta);
                return $respuesta;
                
            } 

            $tarea = new Tarea($_POST);
            $tarea->proyectoId = $proyecto->id;

            $resultado = $tarea->guardar();
            if($resultado){
                $respuesta = [
                    'tipo'=> 'exito',
                    'id' => $tarea-> id,
                    'mensaje' => 'Tarea actualizada correctamente',
                    'proyectoId' => $proyecto->id
                ]; 
            } 

            echo json_encode(['respuesta'=> $respuesta]);
        }
    }

    public static function eliminar(){
        
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            
            session_start();
            $proyecto = Proyecto::where('url', $_POST['url']);

            if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']){
                $respuesta = [
                    'tipo'=> 'error',
                    'mensaje' => 'Error al eliminar la tarea'
                ];
                echo json_encode($respuesta);
                return $respuesta;
                
            } 

            $tarea = Tarea::where('id', $_POST['id']);
            $resultado = $tarea->eliminar();
           if($resultado){
                $respuesta = [
                    'tipo'=> 'exito',
                    'mensaje' => 'Tarea eliminada correctamente',
                    
                ]; 
            }

            echo json_encode(['respuesta'=> $respuesta]);
        }
    }
}