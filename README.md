# IoT Gate Monitoring and Control System

The IoT Gate Monitoring and Control System is a web-based application that allows users to monitor and control gates/doors remotely.
It provides a user-friendly interface for managing gate access, monitoring gate status, and generating reports.

## Technologies Used

The project is built using the following technologies:

- PHP: Backend scripting language for server-side logic
- HTML: Markup language for structuring web pages
- CSS: Stylesheet language for enhancing the visual appearance
- Database: Backend storage for gate and user information
- C++: Programming language for ESP32 microcontroller

## Setup Instructions

To set up the IoT Gate Monitoring and Control System, follow these steps:

### Web Application

1. Clone the repository to your local machine.
2. Import the provided database schema into your preferred database management system.
3. Configure the database connection settings in the appropriate PHP files.
4. Upload the project files to your web server or run it locally using a development environment like XAMPP or WAMP.
5. Access the website through a web browser.

### ESP32 Microcontroller

1. Open the ESP32 code provided in the repository with arduino IDE.
2. Connect your ESP32 microcontroller to your computer.
3. Compile and upload the code to the ESP32 board.
4. Connect the a magnetic reed switch sensor to the ESP32 board as per the project requirements.
5. Connect a push button to the ESP32 board to send notification for the gate to be opened.
6. Ensure the ESP32 is connected to a stable network.
The prototype is as shown;

![prototype](https://github.com/eddiekimungui/IoT-project/assets/133564980/e1da5e88-8eaf-4d8d-a53c-1d5555051d01)



## Database

The project uses a database to store gate and user information.
The database operates on an online free web host server called 000webhost.
The database has one table with the following columns:

- `id`: The identity of the door being controlled and monitored (integer type, primary key).
- `status`: The status of the door (1 for open, 0 for closed).
- `r_access`: A variable that puts up a message on the webpage depending on its value (1 for "requesting access", 0 to clear the message).
- `password` and `username`: Login credentials for the webpage user control panel.
  
  ![Screenshot (84)](https://github.com/eddiekimungui/IoT-project/assets/133564980/11e057f3-9fbd-4964-97aa-8212799f0bb7)

## Webpage Design

The webpage runs on 000webhost and is designed using PHP, HTML, and CSS.
It provides a dynamic interface that responds to user inputs and interacts with the database. 
The ESP32 interacts with the webpage through HTTPS requests.
The website layout is as shown;

![door im](https://github.com/eddiekimungui/IoT-project/assets/133564980/3ea1c619-5e2e-4a87-bf77-c3130bfd6841)

## ESP32 Programming

The ESP32 microcontroller is programmed using C++ in the Arduino IDE environment.
The code utilizes the Servo, HTTP client, and Wi-Fi libraries for easier development. 
The ESP32 connects to the internet and communicates with the server through HTTPS GET and POST requests. 
Interrupts are used to handle events when the push button or magnetic reed switch sensor is triggered.

## Additional Features

- Authentication: The webpage requires a password and username for access.
- Logging: Access logs are maintained to track gate activities.
- Notification Alerts: The system sends text messages and makes calls using the IFTTT app for certain events.
- Alphanumeric Keypad: An installed keypad allows direct door opening in case of a power failure.
- LED Indication: An LED on the ESP32 board indicates a bypassed door.
- Buzzer: sounds everytime the gate is opened remotely.
- servo motor: ensures opening and closing of the gate.
- Breadboard power supply: supply 5v to the ESP32 board.
