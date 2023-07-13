//Libraries
#include <Key.h>
#include <Keypad.h>
#include <ESP32_Servo.h>
#include <HTTPClient.h>
#include <WiFi.h>

//WIFI credentials
const char* ssid = "";          //My wifi
const char* password = "";  //wifi password 

//keypad
const byte ROW = 4;  // max no. of ROWS
const byte COLUMN = 4;  // max no. of columns
char keys[ROW][COLUMN] = {  //key arragement on the keypad
  { '1', '2', '3', 'A' },
  { '4', '5', '6', 'B' },
  { '7', '8', '9', 'C' },
  { '*', '0', '#', 'D' }
};
byte RowPins[ROW] = { 13, 14, 27, 26 };  //link to the row 
byte ColumnPins[COLUMN] = { 25, 33, 32, 23 };  //link to the column

Keypad keypad = Keypad(makeKeymap(keys), RowPins, ColumnPins, ROW, COLUMN);

//IFTTTWebhook credentials
String key = "";  //IFTTT webhook key
String Eventname1 = "someone_at_door";                       //webhook 1st event name
String Eventname2 = "door_broken";                           //webhook 2nd event name

//Variables
String DOOR_id = "1";                        //if there is need to control more than 1 DOOR
String pass_word;                            //store entered keypad keys
bool keypadset = false;                      //track keypad to open door when right keys are entered
bool reedsensor_trip = false;                //track any changes on the door when locked
String data_to_send = "";                    //string data sent to the webserver using https
unsigned int new_millis, old_millis;         //variables to be used as delay
int refresh_time = 200;                      //delay time in milliseconds
int servopos = 0;                            //track position of servo

//variables to keep timing of pushbutton(debouncing)
unsigned long button_duration = 0;
unsigned long last_button_duration = 0;


//Inputs & outputs
int sensor_value = 19;  //door sensor connected to this pin
int servopin = 4;       //servo motor connected to this pin
int button = 21;        //push button connected to this pin
int button_value = 0;   //variable stores the value of the push button
int LED = 18;           //LED connected to this pin
int buzzer = 22;        //buzzer connected to this pin 
int LED_status = 0;     //track LED status

Servo myservo;          //creats an object named myservo

//interrupt on opening door
void IRAM_ATTR isr1() {
  if (servopos == 90) {
    reedsensor_trip = true;
  }
}


//interrupt on pressing push button
void IRAM_ATTR isr2() {
  button_duration = millis();
  if (button_duration - last_button_duration > 250) {
    button_value = 1;
    last_button_duration = button_duration;
  }
}

TaskHandle_t Task1;  // Task handle variable

void codeForTask1(void* parameter) { 
  for (;;) {
  char special_key = keypad.getKey();
      if(special_key){
      Serial.print(special_key);          
      pass_word += special_key;
       if(pass_word.length() == 5){
         digitalWrite(buzzer, HIGH);
         delay(100);
         digitalWrite(buzzer, LOW);
          if(pass_word == "2580#"){
    
          Serial.println("correct password");
          myservo.write(0);           
          keypadset = true;
         
          }else{
          Serial.println("wrong password");
          
         }
          pass_word = "";         
        }
      } 
        delay(100);   //give time for the other task        
    }   
    
   
}

void setup() {
  delay(10);
  Serial.begin(115200);          // Starting serial connection
  myservo.attach(servopin);      // Connect pin 4 to servo
  pinMode(sensor_value, INPUT);  // Reed switch sensor input
  pinMode(buzzer, OUTPUT);       // buzzer output
  pinMode(LED, OUTPUT);                                                 // LED output
  pinMode(button, INPUT_PULLDOWN);                                      // Pushbutton input pulled down
  attachInterrupt(digitalPinToInterrupt(sensor_value), isr1, FALLING);  // Creating external interruption on pin 19
  attachInterrupt(digitalPinToInterrupt(button), isr2, RISING);         // Creating external interrupt on pin 21

  WiFi.begin(ssid, password);            // begin connecting to WiFi
  Serial.print("Connecting...");         // display on serial monitor when establishing a connection
  while (WiFi.status() != WL_CONNECTED)  // confirm if WiFi is connected
  {
    delay(500);
    Serial.print(".");
  }

  Serial.print("Connected, my IP: ");
  Serial.println(WiFi.localIP());  // display the IP on the serial monitor
  new_millis = millis();        // for delay
  old_millis = new_millis;

  xTaskCreatePinnedToCore(
    codeForTask1, // function for task. 
    "Task_1",     // task name. 
    1000,         // task size
    NULL,         // task parameter.
    1,            // task priority.
    &Task1,       // track created task.
    0             // Core. 
  );
}


void loop() {
  servopos = myservo.read();
  new_millis = millis();  // millis function used in place of delay
  if (new_millis - old_millis > refresh_time) {
    old_millis = new_millis;
    if (WiFi.status() == WL_CONNECTED) {  //Check status of WiFi
      HTTPClient http;                    //a new client is created
      if (reedsensor_trip || keypadset) {               //when door is opened, send messsage "toggle_DOOR"
        data_to_send = "toggle_DOOR=" + DOOR_id;
        digitalWrite(LED, HIGH);                               //turn LED on when door bypassed
        LED_status = 1;                         //keep record that LED is on
        http.begin("https://maker.ifttt.com/trigger/" + Eventname2 + "/with/key/" + key + "?value1=test");  // send a trigger to IFTTTwebghook app
        http.GET();
        http.end();
        reedsensor_trip = false;  //set back to false
        keypadset = false;        
      } else {
        data_to_send = "check_DOOR_status=" + DOOR_id;  //If door not open send message "check_DOOR_status"
      }

      if (button_value == 1) {
        data_to_send = "request_access=" + DOOR_id;      //send the 'requesting_access' text whenever the push button is pressed
        http.begin("https://maker.ifttt.com/trigger/" + Eventname1 + "/with/key/" + key + "?value1=test");  // send a trigger to IFTTTwebghook app
        http.GET();
        http.end();
        button_value = 0;  //return this variable back to zero after sending "requesting access message"
      }
      
      //start webpage connection
      http.begin("https://edranger.000webhostapp.com/esp32_update.php");
      http.addHeader("Content-Type", "application/x-www-form-urlencoded");  //header preparation

      int response_code = http.POST(data_to_send);  //a response code is generated from the http post request

      //a response is received if the code is higher than 0
      if (response_code > 0) {
        Serial.println("HTTP code " + String(response_code));  //Printing the code on serial monitor

        if (response_code == 200) {                 //200,successful
          String responses = http.getString();      //http used to get request to store data from webpage
          Serial.print("Server reply: ");           //display to serial monitor
          Serial.println(responses);

          //whenever the data from webpage is DOOR_is_closed, servo is turned to close door
          if (responses == "DOOR_is_closed") {
            if (LED_status == 1) {
              digitalWrite(LED, LOW);
              LED_status = 0;
            }
            if (servopos == 0) {
              myservo.write(90);
              delay(200); //allow time for servo to open
              digitalWrite(buzzer, HIGH);
              delay(200);
              digitalWrite(buzzer, LOW);
            }
          }
          //If the received data is DOOR_is_open, we set HIGH the DOOR pin
          else if (responses == "DOOR_is_open") {
            if (servopos > 50) {
              myservo.write(0);
              delay(200); //allow time for servo to open
              digitalWrite(buzzer, HIGH);
              delay(100);
              digitalWrite(buzzer, LOW);
            }
          }
        }  
      }    
      else {
        Serial.print("code Error: ");
        Serial.println(response_code);
      }
      http.end();  
    }              
    else {
      Serial.println("Experienced Problem with WiFi");
    }
  }
}