<?php
date_default_timezone_set('America/Costa_Rica');

// Classes
require_once("Conexion.php");

if(isset($_POST["action"])){
    $opt= $_POST["action"];
    unset($_POST["action"]);
    // Session
    if (!isset($_SESSION))
        session_start();  
    // Instance
    $device= new Device();
    switch($opt){
        case "ReadAll":
            echo json_encode($device->ReadAll());
            break;
        case "ReadbyID":
            echo json_encode($device->ReadbyID());
            break;
        case "Create":
            echo json_encode($device->Create());
            break;
        case "Update":
            $device->Update();
            break;
        case "AddData":
            $device->AddData();
            break;
        case "Delete":
            echo json_encode($device->Delete());
            break;   
    }    
}else{

    // if(isset($_GET["action"])){
    //     $opt= $_GET["action"];
    //     unset($_GET["action"]);
    //     // Session
    //     if (!isset($_SESSION))
    //         session_start();  
    //     // Instance
    //     $device= new Device();
    //     switch($opt){
    //         case "ReadAll":
    //             echo json_encode($device->ReadAll());
    //             break;
    //         case "ReadbyID":
    //             echo json_encode($device->ReadbyID());
    //             break;
    //         case "Create":
    //             echo json_encode($device->Create());
    //             break;
    //         case "Update":
    //             $device->Update();
    //             break;
    //         case "AddData":
    //             $device->AddData();
    //             break;
    //         case "Delete":
    //             echo json_encode($device->Delete());
    //             break;   
    //     }    
    // }
}

class Device{
    public $id="";
    public $idDispositivo="";
    public $valor="";
    public $nombre="";
    public $imei= "";
    public $numSIM="";
    public $tipo="";
    public $latitud="";
    public $longitud="";
    //
    function __construct(){
        // identificador único
        $this->id= $_GET["id"] ?? '';

        $this->idDispositivo= $_GET["idDispositivo"] ?? '6d9729f6-be34-11e8-b101-c85b76da12f5';
        $this->valor= $_GET["valor"] ?? '';
        $this->nombre= $_GET["nombre"] ?? '';
        $this->imei= $_GET["imei"] ?? '';
        $this->numSIM= $_GET["numSIM"] ?? '';
        $this->tipo= $_GET["tipo"] ?? '';
        $this->latitud= $_GET["latitud"] ?? '';
        $this->longitud= $_GET["longitud"] ?? '';
    }

    function AddData(){
        try {
            $sql='INSERT INTO variables (id, idDispositivo, tipo, valor, latitud, longitud) 
            VALUES (UUID(), :idDispositivo, :tipo, :valor, :latitud, :longitud);'; 
       
            $param= array(':idDispositivo'=>$this->idDispositivo, ':tipo'=>$this->tipo,
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
            $sql='SELECT id FROM dispositivos';
            $data= DATA::Ejecutar($sql);
            if($data){
                $lista = [];
                foreach ($data as $key => $value){
                    $sql="SELECT dis.nombre, var.tipo, var.valor, var.fecha, var.longitud, var.latitud
                    FROM dispositivos dis
                    INNER JOIN variables var ON dis.id = var.idDispositivo
                    WHERE idDispositivo = :idDispositivo
                    ORDER BY fecha DESC
                    LIMIT 1";              
                    $param= array(':idDispositivo'=>$value["id"]);
                    $data = DATA::Ejecutar($sql,$param, false);
                    if($data){
                        foreach ($data as $key => $value){
                            $dispositivo = new Device();
                            $dispositivo->nombre = $value['nombre'];
                            $dispositivo->tipo = $value['tipo'];
                            $dispositivo->valor = $value['valor'];
                            $dispositivo->fecha = $value['fecha'];
                            $dispositivo->latitud = $value['latitud'];
                            $dispositivo->longitud = $value['longitud'];
                            array_push ($lista, $dispositivo);
                        }
                    }
                }                
                return $lista;
            }
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

            $this->fechaCreacion = date("Y-m-d H:i:s");
            $this->fechaEmision = date("D \d\\e F Y");

            $sql="INSERT INTO factura   (id, idBodega, fechaCreacion, local, terminal, idCondicionVenta,idSituacionComprobante,idEstadoComprobante, idMedioPago,fechaEmision, totalVenta, totalDescuentos, totalVentaneta, totalImpuesto, totalComprobante, idEmisor, idUsuario)
                                       
            VALUES  (:uuid, :idBodega, CURRENT_TIMESTAMP(), :local, :terminal, :idCondicionVenta, :idSituacionComprobante, :idEstadoComprobante, :idMedioPago, :fechaEmision, :totalVenta, :totalDescuentos, :totalVentaneta, :totalImpuesto, :totalComprobante, :idEmisor, :idUsuario)"; 
       
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