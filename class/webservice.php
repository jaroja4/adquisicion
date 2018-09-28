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
        echo ($webService->AddData());
            break; 
    }    
}else{
    echo ("Datos invalidos");
} 

class WebService{
    public $id="";
    public $imei="";
    public $valor="";
    public $nombre="";
    public $numSIM="";
    public $tipo="";
    public $latitud="";
    public $longitud="";
    //
    function __construct(){
        // identificador único
        $this->id= $_GET["id"] ?? '';
        $this->imei= $_GET["imei"] ?? '';
        $this->valor= $_GET["valor"] ?? '';
        $this->nombre= $_GET["nombre"] ?? '';
        $this->numSIM= $_GET["numSIM"] ?? '';
        $this->tipo= $_GET["tipo"] ?? '';
        $this->latitud= $_GET["latitud"] ?? '';
        $this->longitud= $_GET["longitud"] ?? '';
    }

    function AddData(){
        try {
            if(strlen($this->latitud) < 2 || strlen($this->longitud) < 2){
                $sql='SELECT latitud, longitud FROM variables
                WHERE imei = :imei ORDER BY FECHA DESC LIMIT 1;'; 
       
                $param= array(':imei'=>$this->imei);
                $coordenadas = DATA::Ejecutar($sql,$param, false);
                if($coordenadas){
                    foreach ($coordenadas as $key => $value){
                        $this->latitud = $value["latitud"];
                        $this->longitud = $value["longitud"]; 
                    }
                }else{
                    $this->latitud = "0.000000";
                    $this->longitud = "0.000000";
                }
            } 
            $sql='INSERT INTO variables (id, imei, tipo, valor, latitud, longitud) 
            VALUES (UUID(), :imei, :tipo, :valor, :latitud, :longitud);'; 
       
            $param= array(':imei'=>$this->imei, ':tipo'=>$this->tipo,
             ':valor'=>$this->valor, ':latitud'=>$this->latitud, ':longitud'=>$this->longitud);
            $data = DATA::Ejecutar($sql,$param, false);
            if($data){
                return "Dato OK";
            }else{
                return "Dato no Almacenado";
            }
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