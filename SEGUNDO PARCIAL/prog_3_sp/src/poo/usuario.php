<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once "accesoDatos.php";

class Usuario
{
    public int $id;
    public string $correo;
    public string $clave;
    public string $nombre;
    public string $apellido;
    public string $foto;
    public string $perfil;
  

    public function Agregar() 
    {
        $retorno = false;
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();    

        $consulta = $objetoAccesoDato->RetornarConsulta ("INSERT INTO `usuarios`(`correo`, `clave`, `nombre`, `apellido`, `perfil`, `foto`)
        VALUES (:correo, :clave, :nombre, :apellido, :perfil, :foto)");
                                                      
        $consulta->bindValue(':correo', $this->correo, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
        $consulta->bindValue(':perfil', $this->perfil, PDO::PARAM_STR);
        $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
        $consulta->execute();   

        if ($consulta->rowCount()>0) 
        {
            $retorno = true;
        }

        return $retorno;
    }
    //###################################################################################################
    //###################################################################################################
    public static function obtenerId($nombre,$correo, $clave,$apellido)
    { 

        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT id FROM usuarios WHERE nombre = :nombre AND correo = :correo AND clave = :clave AND apellido = :apellido");
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->bindValue(':correo', $correo, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);
        $consulta->bindValue(':apellido', $apellido, PDO::PARAM_STR);
        $consulta->execute();

        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

        if ($resultado && isset($resultado['id'])) 
        {
            return $resultado['id'];
        }
        else
        {
            return null;
        }
    }
    //###################################################################################################
    //###################################################################################################
    public static function actualizarPath($id,$nuevaRutaFoto)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

        $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE usuarios SET foto = :foto WHERE id = :id");
        $consulta->bindValue(':foto', $nuevaRutaFoto, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->rowCount() > 0;
    }
    //###################################################################################################
    //###################################################################################################  
    public static function TraerUsuario($obj)
    {
        $accesoDatos = AccesoDatos::dameUnObjetoAcceso();
        
        $consulta = $accesoDatos->retornarConsulta(
            "SELECT * FROM usuarios
             WHERE correo = :correo AND clave = :clave"
        );
        $consulta->bindValue(":correo", $obj->correo, PDO::PARAM_STR);
        $consulta->bindValue(":clave", $obj->clave, PDO::PARAM_STR);
        $consulta->execute();

        $usuario = $consulta->fetchObject('Usuario');

        return $usuario;
    }
    //###################################################################################################  
    public static function TraerUsuarioPorCorreo($correo)
    {
        $accesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $accesoDatos->retornarConsulta(
            "SELECT * FROM usuarios
             WHERE correo = :correo"
        );
        $consulta->bindValue(":correo", $correo, PDO::PARAM_STR);
        $consulta->execute();

        $usuario = $consulta->fetchObject('Usuario');

        return $usuario;
    }
    //###################################################################################################
    //###################################################################################################
    public static function TraerTodosUsuarios()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->retornarConsulta("SELECT * FROM usuarios");
        $consulta->execute();
        $usuarios = $consulta->fetchAll(PDO::FETCH_CLASS, "Usuario");

        return $usuarios;
    }

    public function TraerTodos(Request $request, Response $response, array $args): Response
    {
        $obj_respuesta = new stdClass();
        $obj_respuesta->exito = false;
        $obj_respuesta->mensaje = "No se encontraron usuarios!";
        $obj_respuesta->dato = "{}";
        $obj_respuesta->status = 424;

        $usuarios = Usuario::TraerTodosUsuarios();

        if(count($usuarios)){
            $obj_respuesta->exito = true;
            $obj_respuesta->mensaje = "Usuarios encontrados!";
            $obj_respuesta->dato = json_encode($usuarios);
            $obj_respuesta->status = 200;    
        }

        $newResponse = $response->withStatus($obj_respuesta->status);
        $newResponse->getBody()->write(json_encode($obj_respuesta));
        return $newResponse->withHeader('Content-Type', 'application/json');
    }
 
    //###################################################################################################
    //###################################################################################################
    public static function ValidarUsuario($correo, $clave)
    {
        $objetoAccesoDato = AccesoDatos::DameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM usuarios WHERE correo=:correo AND clave=:clave");

        $consulta->bindValue(':correo', $correo, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);
        $consulta->execute();

        $usuario = false;

        if ($consulta->rowCount()>0) 
        {
            $usuario = $consulta->fetchObject('Usuario');
        }

        return $usuario;
    }
    //###################################################################################################
    //###################################################################################################
    public static function ValidarCorreoExiste($correo)
    {
        $objetoAccesoDato = AccesoDatos::DameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM usuarios WHERE correo=:correo ");

        $consulta->bindValue(':correo', $correo, PDO::PARAM_STR);
        $consulta->execute();

        $usuario = false;

        if ($consulta->rowCount()>0) 
        {
            $usuario= $consulta->fetchObject('Usuario');
        }

        return $usuario;
    }
}