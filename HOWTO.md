# Server Monitor

## Overview

My PHP project code for Ubersmith's technical test as specified in README-SPEC.MD.

The goal of this exercise was to write a program that can be used to monitor statistics detailing how web servers are currently performing and store those statistics in a database.

## Installation

1. Clone repo @
2. Run composer.json in your root project directory.
3. Create your MySQL database as per instructions in README-SPEC.md. I used localhost running Apache Module mod_status to get responses.
4. Modify the ./config/config.ini to match your database credentials (dsn, username, password ).

## Special Notes

1. I built on a Windows machine, WAMP64 stack, using PHP 8.3.0. Where possible, I have added conditional coding for the execs to run on both Windows and Unix like platforms, however I am unable to test the Unix execs locally.
2. As I could not find existing servers with publically accessible server-status modules, I'm not able to test for **Lighttpd** and **NGINX** servers responses. Therefore I have not added support in my code to query these servers. If I was able to query these, I would have used an appropriate switch/case in my buildURL method to modify the query string based on the server httpd table value.
3. The daemon uses 'nohup' to detach from the terminal and run in the background. Ensure 'nohup' is available on the machine running the main script.
4. I have added a phpunit test for each class on public methods to demonstrate usage. My workflow included testing the functionality as I developed.

## Run

To initiate the main script, open your terminal and run php ./bin/update.php from the root folder.

To run as a daemon, run php ./bin/update.php -d . The script will detach from the terminal and run in the background unitl terminated.

Optional switches include:

-i \<interval> fixed poll interval (in seconds) - // Default interval is 5 seconds
-n \<instance count> max number of concurrent instances // Default instance count is 1
-w \<load percentage> max aggregate cpu load (as a percentage) across all instances // Default max CPU load is 100%

## Log

Log can be found at ./logs/ServerMonitor.log

## License

GPL v3.

## Author

Lee Kierstead
lee@kwpdev.com
