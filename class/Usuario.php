<?php
if(isset($_POST["action"])){
    $opt= $_POST["action"];
    unset($_POST['action']);
    // Classes
    require_once("Conexion.php");
    require_once('Evento.php');
    //require_once("encdes.php");
    // Session
    if (!isset($_SESSION))
        session_start();
    // Instance
    $usuario= new Usuario();
    switch($opt){
        case "ReadAll":
            echo json_encode($usuario->ReadAll());
            break;
        case "Read":
            echo json_encode($usuario->Read());
            break;
        case "Create":
            $usuario->Create();
            break;
        case "Update":
            $usuario->Update();
            break;
        case "Delete":
            echo json_encode($usuario->Delete());
            break;   
        case "Login":
            $usuario->username= $_POST["username"];
            $usuario->password= $_POST["password"];
            $usuario->Login();
            echo json_encode($_SESSION['userSession']);
            break;   
        case "CheckSession":     
            $usuario->CheckSession();
            echo json_encode($_SESSION['userSession']);
            break;
        case "EndSession":
            $usuario->EndSession();
            break;        
        case "CheckUsername":
            $usuario->username= $_POST["username"];
            echo json_encode($usuario->CheckUsername());
            break;
        case "setBodega":
            $usuario->setBodega();
            break;
    }
}

abstract class userSessionStatus
{
    const invalido = 'invalido'; // login invalido
    const login = 'login'; // login ok; credencial ok
    const nocredencial= 'nocredencial'; // login ok; sin credenciales
    const inactivo= 'inactivo';
    const noexiste= 'noexiste';
    const noip= 'noip';
}

class Usuario{
    public $id;
    public $username;
    public $password;
    public $nombre;
    public $email;
    public $activo = 0;
    public $status = userSessionStatus::invalido; // estado de la sesion de usuario.
    public $listarol= array(); // array de roles del usuario.
    public $eventos= array(); // array de eventos asignados a la sesion de usuario.
    public $url;
    public $ip;

    function __construct(){
        // identificador único
        if(isset($_POST["id"])){
            $this->id= $_POST["id"];
        }
        if(isset($_POST["obj"])){
            $obj= json_decode($_POST["obj"],true);
            require_once("UUID.php");
            $this->id= $obj["id"] ?? UUID::v4();
            $this->nombre= $obj["nombre"] ?? '';  
            $this->username= $obj["username"] ?? '';
            $this->password= $obj["password"] ?? '';  
            $this->email= $obj["email"] ?? '';  
            $this->activo= $obj["activo"] ?? '';
            //roles del usuario.
            if(isset($obj["listarol"] )){
                require_once("RolesXUsuario.php");
                //
                foreach ($obj["listarol"] as $idRol) {
                    $rolUsr= new RolesXUsuario();
                    $rolUsr->idRol= $idRol;
                    $rolUsr->idUsuario= $this->id;
                    array_push ($this->listarol, $rolUsr);
                }
            }
        }
    }

    // login and user session

    function checkIp(){
        $sql= 'SELECT ip
            FROM ipAutorizada
            where ip=:ip';
        $param= array(':ip'=>$this->ip);
        $data= DATA::Ejecutar($sql, $param);
        if(!count($data)){
            unset($_SESSION["userSession"]);
            $this->status= userSessionStatus::noip;
            $_SESSION["userSession"]= $this;
            return false;
        }
        else return true;
    }

    function CheckSession(){
        if(isset($_SESSION["userSession"]->id)){
            // VALIDA SI TIENE CREDENCIALES PARA LA URL CONSULTADA
            $_SESSION['userSession']->status= userSessionStatus::nocredencial;
            $_SESSION['userSession']->url = $_POST["url"];
            $urlarr = explode('/', $_SESSION['userSession']->url);
            $myUrl = end($urlarr)==''?'index.html':end($urlarr);
            foreach ($_SESSION['userSession']->eventos as $evento) {
                if(strtolower($myUrl) == strtolower($evento->url)){
                    $_SESSION['userSession']->status= userSessionStatus::login;
                    break;
                }
            }
        }
        else {
            $this->status= userSessionStatus::invalido;
            $this->url = $_POST["url"];
            $_SESSION["userSession"]= $this;
        }
    }

    function EndSession(){
        unset($_SESSION['userSession']);
        //return true;
    }

    function Login(){
        try {
            //Check activo & password.
            $sql= 'SELECT DISTINCT u.id, u.name, u.password, active, e.menuL1 as nombreUrl, e.url, er.idEvento, e.menuL2
            FROM user u inner join rolesXUsuario ru on ru.idUsuario = u.id
                inner join eventosXRol er on er.idRol = ru.idRol
                inner join Event e on e.id = er.idEvento
                where u.name=:username';
            $param= array(':username'=>$this->username);
            $data= DATA::Ejecutar($sql, $param);
            if($data){
                if($data[0]['active']==0){
                    unset($_SESSION["userSession"]);
                    $this->status= userSessionStatus::inactivo;
                }
                else {
                    // usuario activo; check password
                    // if(password_verify($this->password, $data[0]['password'])){
                    if($this->password == $data[0]['password']){
                        foreach ($data as $key => $value){
                            // Session Datos del usuario y eventos relacionados a su rol
                            $evento= new Evento(); // evento con credencial del usuario.
                            if($key==0){
                                $this->id = $value['id'];
                                $this->username = $value['name'];
                                $this->nombre = $value['name'];
                                $this->activo = $value['active'];
                                $this->status = userSessionStatus::login;
                                $this->url = isset($_SESSION['userSession']->url)? $_SESSION['userSession']->url : 'index.html'; // Url consultada
                                //
                                $evento->id= $value['idEvento'];
                                $evento->nombre= $value['nombreUrl'];
                                $evento->url= $value['url'];
                                $evento->menuPadre= $value['menuL2'];
                                //$evento->subMenuPadre= $value['subMenuPadre'];
                                $this->eventos = array($evento);
                            }
                            else {
                                $evento->id= $value['idEvento'];
                                $evento->nombre= $value['nombreUrl'];
                                $evento->url= $value['url'];
                                $evento->menuPadre= $value['menuPadre'];
                                $evento->subMenuPadre= $value['subMenuPadre'];
                                $evento->icono= $value['icono'];
                                array_push ($this->eventos, $evento);
                            }                    
                        }
                    }
                    else { // password invalido
                        unset($_SESSION["userSession"]);
                        $this->status= userSessionStatus::invalido;
                    }
                }
            }
            else {
                unset($_SESSION["userSession"]);
                $this->status= userSessionStatus::noexiste;
            }
            // set user session.
            $_SESSION["userSession"]= $this;
        }     
        catch(Exception $e) {
            unset($_SESSION["userSession"]);
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => $e->getMessage()))
            );
        } 
    }

    // usuario CRUD

    function ReadAll(){
        try {
            $sql='SELECT id, nombre, username, email, activo
                FROM     usuario       
                ORDER BY nombre asc';
            $data= DATA::Ejecutar($sql);
            return $data;
        }     
        catch(Exception $e) {
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => 'Error al cargar la lista'))
            );
        }
    }

    function Read(){
        try {
            $sql='SELECT u.id, u.nombre, u.username, u.password, email, activo, r.id as idRol, r.nombre as nombreRol
                FROM user  u LEFT JOIN rolesXUsuario ru on ru.idUsuario = u.id
                    LEFT JOIN rol r on r.id = ru.idRol
                where u.id=:id';
            $param= array(':id'=>$this->id);
            $data= DATA::Ejecutar($sql,$param);     
            
            foreach ($data as $key => $value){
                require_once("Rol.php");
                $rol= new Rol(); // crol del producto
                if($key==0){
                    $this->id = $value['id'];
                    $this->nombre = $value['nombre'];
                    $this->username = $value['username'];
                    $this->password = $value['password'];
                    $this->email = $value['email'];
                    $this->activo = $value['activo'];                    
                    //rol
                    if($value['idRol']!=null){
                        $rol->id = $value['idRol'];
                        $rol->nombre = $value['nombreRol'];
                        array_push ($this->listarol, $rol);
                    }
                }
                else {
                    $rol->id = $value['idRol'];
                    $rol->nombre = $value['nombreRol'];
                    array_push ($this->listarol, $rol);
                }
            }
            return $this;
        }     
        catch(Exception $e) {
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => 'Error al cargar el usuario'))
            );
        }
    }

    function Create(){
        try {
            $sql="INSERT INTO usuario   (id, nombre, username, password, email, activo)
                VALUES (:uuid, :nombre, :username, :password, :email, :activo)";
            //
            $param= array(':uuid'=>$this->id, ':nombre'=>$this->nombre, ':username'=>$this->username, ':password'=> password_hash($this->password, PASSWORD_DEFAULT), 
                ':email'=>$this->email, ':activo'=>$this->activo);
            $data = DATA::Ejecutar($sql,$param, false);
            if($data)
            {
                $created= true;
                $errmsg='';
                //save array obj
                if(!RolesXUsuario::Create($this->listarol)){
                    $created= false;
                    $errmsg= 'Error al guardar los roles.';
                }
                if($created)
                    return true;
                else throw new Exception($errmsg, 05);
            }
            else throw new Exception('Error al guardar.', 02);
        }     
        catch(Exception $e) {
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => $e->getMessage()))
            );
        }
    }

    function Update(){
        try {
            if($this->password=='NOCHANGED'){
                $sql="UPDATE usuario 
                    SET nombre=:nombre, username=:username, email=:email, activo=:activo
                    WHERE id=:id";
                $param= array(':id'=>$this->id, ':nombre'=>$this->nombre, ':username'=>$this->username, ':email'=>$this->email, ':activo'=>$this->activo);
            }
            else {
                $sql="UPDATE usuario 
                    SET nombre=:nombre, username=:username, password= :password, email=:email, activo=:activo
                    WHERE id=:id";
                $param= array(':id'=>$this->id, ':nombre'=>$this->nombre, ':username'=>$this->username, ':password'=> password_hash($this->password, PASSWORD_DEFAULT), 
                    ':email'=>$this->email, ':activo'=>$this->activo);
            }
            $data = DATA::Ejecutar($sql,$param,false);
            if($data){
                //update array obj
                $created= true;
                $errmsg='';
                if($this->listarol!=null){
                    if(!RolesXUsuario::Update($this->listarol)){
                        $created= false;
                        $errmsg= 'Error al actualizar los roles.';
                    }
                }
                else {
                    // no tiene roles
                    if(!RolesXUsuario::Delete($this->id)){
                        $created= false;
                        $errmsg= 'Error al actualizar los roles.';
                    }                        
                }
                if($created)
                    return true;
                else throw new Exception($errmsg, 04);
                
            }
            else throw new Exception('Error al guardar.', 123);
        }     
        catch(Exception $e) {
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => $e->getMessage()))
            );
        }
    }   

    function CheckUsername(){
        try{
            $sql="SELECT id
                FROM user 
                WHERE username= :username";
            $param= array(':username'=>$this->username);
            $data= DATA::Ejecutar($sql, $param);
            if(count($data))
                $sessiondata['status']=1; // usuario duplicado
            else $sessiondata['status']=0; // usuario unico
            return $sessiondata;
        }
        catch(Exception $e){
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => $e->getMessage()))
            );
        }
    }

    private function CheckRelatedItems(){
        try{
            $sql="SELECT idUsuario
                FROM rolesXUsuario x
                WHERE x.idUsuario= :id";
            $param= array(':id'=>$this->id);
            $data= DATA::Ejecutar($sql, $param);
            if(count($data))
                return true;
            else return false;
        }
        catch(Exception $e){
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => $e->getMessage()))
            );
        }
    }

    function Delete(){
        try {
            if($this->CheckRelatedItems()){
                //$sessiondata array que devuelve si hay relaciones del objeto con otras tablas.
                $sessiondata['status']=1; 
                $sessiondata['msg']='Registro en uso'; 
                return $sessiondata;           
            }                    
            $sql='DELETE FROM user
                WHERE id= :id';
            $param= array(':id'=>$this->id);
            $data= DATA::Ejecutar($sql, $param, false);
            if($data){
                $sessiondata['status']=0; 
                return $sessiondata;
            }
            else throw new Exception('Error al eliminar.', 978);
        }
        catch(Exception $e) {
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => $e->getMessage()))
            );
        }
    }


}




?>