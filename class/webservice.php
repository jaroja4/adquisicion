<?php
date_default_timezone_set('America/Costa_Rica');

// Classes
require_once("Conexion.php");

if(isset($_GET["action"])){
    $opt= $_GET["action"];
    unset($_GET["action"]);

    $webService= new WebService();
    switch($opt){
        case "AddData":
            $device->AddData();
            break; 
    }    
}

class WebService{
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
        $this->idDispositivo= $_GET["idDispositivo"] ?? '';
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
}

?>