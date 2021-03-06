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
    $rolXUsuarioXUsuario= new RolXUsuario();
    switch($opt){
        case "ReadAll":
            echo json_encode($rolXUsuario->ReadAll());
            break;
        case "Read":
            echo json_encode($rolXUsuario->Read());
            break;
        case "Create":
            $rolXUsuario->Create();
            break;
        case "Update":
            $rolXUsuario->Update();
            break;
        case "Delete":
            echo json_encode($rolXUsuario->Delete());
            break;   
    }
}


class RolXUsuario{
    public $idRol="";
    public $idUsuario="";
    public $rolesXUsuario = [];
    //
    function __construct(){
        // identificador único
        $this->idRol= $_POST["idRol"] ?? '';
        $this->idUsuario= $_POST["idUsuario"] ?? '';

        
        if(isset($_POST["obj"])){
            $obj= json_decode($_POST["obj"],true);
            $this->idRol= $obj["idRol"] ?? '';
            $this->idUsuario= $obj["idUsuario"] ?? '';
        }
    }

    function AddData(){
        try {
            $sql='INSERT INTO variables (id, imei, tipo, valor, latitud, longitud) 
            VALUES (UUID(), :imei, :tipo, :valor, :latitud, :longitud);'; 
       
            $param= array(':imei'=>$this->imei, ':tipo'=>$this->tipo,
             ':valor'=>$this->valor, ':latitud'=>$this->latitud, ':longitud'=>$this->longitud);
            $data = DATA::Ejecutar($sql,$param, false);
        }     
        catch(Exception $e) {
            error_log("error: ". $e->getMessage());
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => $e->getMessage()))
            );
        }
    }

    

    function ReadAll(){
        try {
            $sql='SELECT usr.id, usr.nombre, usr.user, usr.company, usr.active
            FROM roles;';
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
    
    function ReadbyID(){
        try {
            $sql='SELECT f.consecutivo, f.fechaCreacion, f.local, f.terminal, f.totalComprobante
                FROM factura f
                WHERE f.id=:id';
            $param= array(':id'=>$this->id);
            $data= DATA::Ejecutar($sql,$param);     
            if($data)
            {
                $this->consecutivo = $data[0]['consecutivo'];
                $this->fechaEmision = $data[0]['fechaCreacion'];
                $this->bodega = $_SESSION["userSession"]->bodega;
                $this->usuario = $_SESSION["userSession"]->username;
                $this->totalComprobante = $data[0]['totalComprobante'];
                $this->lista= ProductoXFactura::Read($this->id);
            }            
            return $this;
        }     
        catch(Exception $e) {
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => 'Error al cargar el factura'))
            );
        }
    }

    function Create(){
        try {
            $sql="INSERT INTO dispositivos (id, nombre, imei, numSIM) VALUES (UUID(), :nombre, :imei, :numSIM);";       
            $param= array(':uuid'=>$this->id, ':idBodega'=>$_SESSION["userSession"]->idBodega, ':local'=>$this->local, ':terminal'=>$this->terminal, 
                    ':idCondicionVenta'=>$this->idCondicionVenta, ':idSituacionComprobante'=>$this->idSituacionComprobante, ':idEstadoComprobante'=>$this->idEstadoComprobante, 
                    ':idMedioPago'=>$this->idMedioPago, ':fechaEmision'=>$this->fechaEmision, ':totalVenta'=>$this->totalVenta, ':totalDescuentos'=>$this->totalDescuentos, 
                    ':totalVentaneta'=>$this->totalVentaneta, ':totalImpuesto'=>$this->totalImpuesto, ':totalComprobante'=>$this->totalComprobante, ':idEmisor'=>$this->idEmisor, ':idUsuario'=>$_SESSION["userSession"]->id);
            $data = DATA::Ejecutar($sql,$param, false);
            if($data)
            {
                ProductoXFactura::$id=$this->id;
                 //save array obj
                 if(ProductoXFactura::Create($this->detalleFactura)){
                    $this->restartInsumo($this->detalleOrden);
                    // retorna orden autogenerada.
                    OrdenXFactura::$id=$this->id;
                    OrdenXFactura::Create($this->detalleOrden);                    
                    $this->ReadbyID();                    
                    return $this;
                }
                else throw new Exception('Error al guardar los productos.', 03);
            }
            else throw new Exception('Error al guardar.', 02);
        }     
        catch(Exception $e) {
            error_log("error: ". $e->getMessage());
            header('HTTP/1.0 400 Bad error');
            die(json_encode(array(
                'code' => $e->getCode() ,
                'msg' => $e->getMessage()))
            );
        }
    }

    function Update(){
        try {
            $sql="UPDATE factura 
                SET idUsuario=:idUsuario, fecha=:fecha, subTotal= :subTotal, iva=:iva, porcentajeIva=:porcentajeIva, descuento=:descuento, porcentajeDescuento=:porcentajeDescuento, total=:total
                WHERE id=:id";
            $param= array(':id'=>$this->id, ':idUsuario'=>$this->idUsuario, ':fecha'=>$this->fecha, ':subTotal'=>$this->subTotal, ':iva'=>$this->iva, ':porcentajeIva'=>$this->porcentajeIva , 
                ':descuento'=>$this->descuento, ':porcentajeDescuento'=>$this->porcentajeDescuento, ':total'=>$this->total);
            $data = DATA::Ejecutar($sql,$param,false);
            if($data){
                //update array obj
                if($this->listaProducto!=null)
                    if(ProductosXFactura::Update($this->listaProducto))
                        return true;            
                    else throw new Exception('Error al guardar las categorias.', 03);
                else {
                    // no tiene categorias
                    if(ProductosXFactura::Delete($this->id))
                        return true;
                    else throw new Exception('Error al guardar las categorias.', 04);
                }
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


    function Delete(){
        try {
            if($this->CheckRelatedItems()){
                //$sessiondata array que devuelve si hay relaciones del objeto con otras tablas.
                $sessiondata['status']=1; 
                $sessiondata['msg']='Registro en uso'; 
                return $sessiondata;           
            }                    
            $sql='DELETE FROM factura  
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