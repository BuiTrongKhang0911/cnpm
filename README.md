CNPM - Car Rental System

How to Run the Project

Step 1: Clone the Project from GitHub

Open a software like Visual Studio Code (VS Code) or any IDE of your choice.

Open the terminal and run the following command to clone the project: 
git clone https://github.com/BuiTrongKhanh0911/cnpm.git



Step 2: Start XAMPP

Open the XAMPP Control Panel. 

Start Apache and MySQL by clicking the "Start" button next to each module.

Step 3: Create the Database

Access phpMyAdmin by clicking the Admin button next to the "Start" button for MySQL in the XAMPP Control Panel (this usually opens http://localhost/phpmyadmin).

Create a new database with the name:car-rental



Step 4: Import the Database

In phpMyAdmin, select the newly created car-rental database.

Click on the Import tab.

Choose the car-rental.sql file located in the SQL folder of the cloned project.

Click "Go" to import the data.

Step 5: Run the Project

Ensure the cnpm project folder is placed inside the htdocs directory of XAMPP (typically C:/xampp/htdocs on Windows).

Open a browser and navigate to:http://localhost/cnpm


You can now use the car rental system.

Notes

Ensure Apache and MySQL are running before accessing the website.
If you encounter any issues, verify that the database was imported correctly and the project folder is placed in the htdocs directory.

