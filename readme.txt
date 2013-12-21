//*********************************//
    Jira Data Grabber Instruction
    by Tetsuro Mori
    2013-06-20
//*********************************//

Overview:
This application is consist of two major functions.
- Get Tempo work log data and save in database
- Get Jira Issue data and save in database
Those data is saved mainly for project management purpose.


1. Set up servers (PHP, MySQL, curl, cron)


2. Import "Database/jira_api_2013-06-20.sql" file to MySQL. Database name is "jira_api".


3. Open "application/config/constants.php" with text editor, and set up database server
   setting. Default is set for local environment.
       define('DB_HOST',     '127.0.0.1');
       define('DB_USER',     'root');
       define('DB_PASSWORD', 'root');
       define('DB_NAME',     'jira_api');
       define('DB_PORT',     '8889');


4. Add your Jira account information to the same file "application/config/constants.php".

    Set Up Jira API's base URL. Subdomain is your company's account name.
       define('JIRA_API_BASE_URL',	'https://CompanyAccountName.atlassian.net/rest/api/2/');
       
    Set Up Jira URL. Subdomain is your company's account name.
       define('JIRA_URL', "https://CompanyAccountName.atlassian.net");
       
    Put our Jira account's user name and password for curl.
       define('USERNAME', 'username');
       define('PASSWORD', 'password');
       
    To use Tempo API, you need to set up your TEMPO account on Jira. After setting a
    TEMPO API TOKEN, enter the token into this setting
       define('TEMPO_API_TOKEN', 'tempotoken');


5. From command line console, go to this app's directory, and type the following command

     php index.php jiradb

   As a default, this app grabs issue data which is updated between 2 hours ago and now.
   

6. If you want to grab more issue data, enter those arguments in order. 

     I.    You must type "index" when you add arguments
     II.   Number of Jira issues to abstract per API call. Max 1000.
	 III.  Starting time in how many seconds ago from now. 7200 means 2 hours ago. When
	       it's set 0, it goes back to when the first ticket was updated on 2011-08-03.
	 IV.   Ending time in how many seconds ago. 0 means NOW.
	 V.    Amount of Issue data to get at each API call. Unit in Seconds. 7200 means
	       2 hour amount of data
	 VI.   Time between each API call's starting time. Unit in sec. 3600 means every hour
	 VII.  Extra JQL query in UTF8 format. e.g. project%20%3d%20%22Global%20Ichiba%22%20
     VIII. Which field to get. Keep it empty if you don't know.
     
   Examples:
     Get all the issue data. It takes several hours, and it might stop due to lack of memory.
        php index.php jiradb index 1000 0 0


7. From command line console, go to this app's directory, and type the following command

     php index.php worklogApp update_worklogs

   As a default, this app grabs work log data from 8 days ago till now. Each API call
   grabes 4-day amount of data, so one run calls API twice.
        
        
6. If you want to grab more work log data, enter those arguments in order. 

     I.    Number of days of work log data you want to get.
     II.   Number of days of work log data this app grabs at each API call
     
   Examples:
     Get work log data of the past one year, and each API call will grab
     4-day amount of work log data.
        php index.php worklogApp update_worklogs 365 4