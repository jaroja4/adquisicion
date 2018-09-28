//Libraries
#include <DHT.h>;
#include <SoftwareSerial.h>

//Constants
#define DHTPIN 3     // what pin we're connected to
#define DHTTYPE DHT22   // DHT 22  (AM2302)
DHT dht(DHTPIN, DHTTYPE); //// Initialize DHT sensor for normal 16mhz Arduino
SoftwareSerial myGsm(10,11);

//Variables
int chk;
float hum;  //Stores humidity value
float temp; //Stores temperature value
int ledPin = 13; 
String data[5];
#define DEBUG true
String state,timegps,latitude,longitude;

void setup()
{
  pinMode(ledPin, OUTPUT);
  myGsm.begin(9600);  
  Serial.begin(9600); 
  dht.begin(); 
  delay(500);  
  Serial.println("Encendiendo modulo 808...");


  
  myGsm.println("AT");
  String respuesta ="";
  String respuestaCorrecta ="OK";
  delay(500);
  while(myGsm.available()!=0){
    Serial.print("graba respuesta");
    char c = myGsm.read(); 
    respuesta+=c;

    if (respuesta == respuestaCorrecta){
        Serial.println("Respuesta es OKOKOK");
    }
    //powerOnSim808();
  }

  Serial.print(respuesta);



  myGsm.println("AT+CGNSPWR=1");
  myGsm.println("AT+CGNSSEQ=RMC");
  delay(3000);
}

void loop()
{
  getTemp();  
  getGPS();
  PreparaCadena();
  checkSignal();
  Serial.println("Espera 10 segundos");
  delay(10000);
}

void powerOnSim808(){
  digitalWrite(ledPin, HIGH);
  delay(2000);
  digitalWrite(ledPin, LOW);
}
  

void getGPS(){
  while(1){
    sendTabData("AT+CGNSINF",3000,DEBUG); 
    delay(1000);
  }
  if (state !=0) {
    Serial.println("State  :"+state);
    Serial.println("Time  :"+timegps);
    Serial.println("Latitude  :"+latitude);
    Serial.println("Longitude  :"+longitude); 
  } else {
    Serial.println("GPS Initialising...");
  }
}

void sendTabData(String command , const int timeout , boolean debug){
  Serial.println (command);
  myGsm.println(command);
  long int time = millis();
  int i = 0;
  String response = ""; 

  while((time+timeout) > millis()){
    while(myGsm.available()!=0){
      char c = myGsm.read(); 
      response+=c;
      if (c != ',') {
         data[i] +=c;
         delay(100);
      } else {
        i++;  
      }
      if (i == 5) {
        delay(100);
        goto exitL;
      }
    }
  }exitL:
  if (debug) {
    data[1]=data[2]=data[3]=data[4]="";
    state = data[1];
    timegps = data[2];
    latitude = data[3];
    longitude =data[4];  
  }
  //Serial.println("GPS Data: " + response);
}

void getTemp()
{
 hum = dht.readHumidity();
 temp= dht.readTemperature();
 Serial.print("HR: " + String(hum, 2) + "TMP: " + String(temp, 2));
}

void PreparaCadena(){
  String buf = "";
  buf += F("AT+HTTPPARA=\"URL\",\"http://tecnoexpresscr.com/getdata/webservice.php?action=AddData&imei=865067024423025&latitud=");
  buf += latitude;
  buf += F("&longitud=");
  buf += longitude;
  buf += F("\"");
  sendData(buf);

  
  buf = "";
  buf += F("AT+HTTPPARA=\"URL\",\"http://tecnoexpresscr.com/getdata/webservice.php?action=AddData&imei=865067024423025&tipo=TEMP&valor=");
  buf += String(temp, 2);
  buf += F("\"");
  sendData(buf);

  
  buf = "";
  buf += F("AT+HTTPPARA=\"URL\",\"http://tecnoexpresscr.com/getdata/webservice.php?action=AddData&imei=865067024423025&tipo=HR&valor=");
  buf += String(hum, 2);
  buf += F("\"");
  sendData(buf);
  buf = ""; 
}

void checkSignal() {
  myGsm.println("AT+CSQ");
  delay(1000);
  printSerialData();
}


void sendData(String cadena){

  myGsm.println("AT+CGATT?");
  delay(200);
  printSerialData();

  
  myGsm.println("AT+SAPBR=3,1,\"CONTYPE\",\"GPRS\"");//setting the SAPBR,connection type is GPRS
  delay(1000);
  printSerialData();
  
  myGsm.println("AT+SAPBR=3,1,\"APN\",\"kolbi3g\"");//setting the APN,2nd parameter empty works for all networks 
  delay(5000);
  printSerialData();  
  
  myGsm.println();
  myGsm.println("AT+SAPBR=1,1"); //Enable the GPRS
  delay(3000);
  printSerialData();

  myGsm.println();
  myGsm.println("AT+SAPBR=2,1"); //Query if the connection is setup properly, if we get back a IP address then we can proceed
  delay(4000);
  printSerialData();

//  myGsm.println();
//  myGsm.println("AT+CIPPING=\"104.131.5.198\"");
//  delay(3000);
//  printSerialData(); 
  
  myGsm.println("AT+HTTPINIT"); //init the HTTP request
  delay(2000); 
  printSerialData();


  myGsm.println();
  myGsm.println("AT+HTTPPARA=\"CID\",1");
  delay(1000);
  printSerialData();

  
  
  myGsm.println();
  //Serial.println("La cadena es: "+cadena);
  myGsm.println(cadena);
  
  delay(1000);
  printSerialData();
  
  myGsm.println();
  myGsm.println("AT+HTTPACTION=0");//submit the GET request 
  delay(5000);//the delay is important if the return datas are very large, the time required longer.
  printSerialData();
  myGsm.println("AT+HTTPREAD=0,20");// read the data from the website you access
  delay(3000);
  printSerialData();
  
  myGsm.println("");
  delay(1000);
  myGsm.println("AT+HTTPTERM");// terminate HTTP service
  printSerialData();  
  myGsm.println();

}

void printSerialData()
{
 while(myGsm.available()!=0)
 Serial.write(myGsm.read());
}
