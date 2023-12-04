# Web Server Status Monitor

The goal of this exercise is to write a program that can be used to monitor statistics detailing how web servers are currently performing and store those statistics in a database.

Your program should run from the command-line and:

- Read from the `server` table containing some web servers.
- For each of those web servers, retrieve their `server-status`.
- Store those results in the `server_status` table based on the current Unix epoch time.

You should create a **MySQL** database with 2 tables:

```sql
CREATE TABLE `server` (
  `server_id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `httpd` varchar(50) NOT NULL,
  `address` varchar(50) NOT NULL,
  `port` int(11) unsigned NOT NULL,
  PRIMARY KEY (`server_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT INTO `server` (`name`, `httpd`, `address`, `port`)
VALUES
	('Apache', 'apache', 'apache.org', 80),
	('Yellow Pages', 'apache', 'yellow.com', 80);

CREATE TABLE `server_status` (
  `time` int(11) unsigned NOT NULL,
  `server_id` int(11) unsigned NOT NULL,
  `total_requests` int(11) unsigned NOT NULL,
  `total_kbytes` int(11) unsigned NOT NULL,
  `active_connections` int(11) unsigned NOT NULL,
  KEY `server_id` (`server_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

For each web server under the `server` table, you will need to retrieve that web server's `server-status` (e.g. http://apache.org/server-status?auto) and parse the Total Requests (`Total Accesses`), Total kBytes (`Total kBytes`), and Active Connections (`BusyWorkers`).

Once you have parsed the statistics, store them in the `server_status` table with the current Unix epoch time and the `server_id` from the `server` table.

## Implementation Notes

- You must use **PHP** to implement your solution.
- Any config options you may require should be stored in a `config.ini` file you create. This file should contain the Data Source Name (`dsn`) to connect to the database. You will need to write a method to read this file.
- Your solution should utilize threads when processing each web server.
- Name the main script `update` (of course with the appropriate suffix)
- The `server` table should contain the URLs for some websites that are using **Apache** as their web server and have a publicly accessible `server-status`, but you should also add support for **Lighttpd** and **NGINX** web servers.
- **_Geek Out_**: Update your program so it can be daemonized and accept the following options:
  - -d run as deamon
  - -i \<interval> fixed poll interval (in seconds)
  - -n \<instance count> max number of concurrent instances
  - -w \<load percentage> max aggregate cpu load (as a percentage) across all instances

## Evaluation Criteria

You should aim to produce "production quality" code, ready to be released to users. The main criteria on which we will evaluate your software are:

- Organization of your code.
- Documentation.
- Error handling.
- Testing and quality assurance.

## Helpful Notes

- If you have any instructions or special considerations please detail them in HOWTO.md. Don't worry about making it pretty.
