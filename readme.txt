Backend API RESTful to manage time consumed in projects.

API Version: V1
Programming language: PHP (Version 8.0.14)
Database: MySQL (Version 5.7.36)
Author: Anderlan Mota <contato@anderlan.com.br> 
Github: https://github.com/anderlanmota/api-manage-time

Installation:

1. Upload the files to the server.

2. Move the config folder to a location that is not publicly accessible via the web. 

3. Edit the autoload.php file on line 3 (define( 'CONFIG_FOLDER', dirname( __FILE__ ) . "/config/" );), informing the new path where the files of the config folder are.

4. Edit the information in the settings files:
- database.json database access data. 
- maintenance.json token to access the resource /{{endpoint}}/maintenance?token= and period in seconds, which is used to store edit histories and data deleted by users. 
- secret.json a secret key for the system to use to generate a JWT authentication

5. Create the database and import the database.sql file
6. Schedule a cron to run daily by calling the resource /{{endpoint}}/maintenance?token=
7. Okay, the system is installed. The initial administrative user is:
login: admin / password: 123123


See the api documentation: 
https://documenter.getpostman.com/view/8513372/UVXonZaK

Attention: See the examples of each function to understand the request and return format of the api. 

The endpoint for testing is: https://anderlan.com.br/api-manage-time/v1/