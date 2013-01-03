LAN
===

Guest management/communication application for LANs (LAN parties).  No account required, just visit the server in the browser.

Note
----
Server must be placed on the same network subnet as the LAN party.  Otherwise the system will not be able to determine the MAC address of clients. 

Goal
----
* Provide a list of users connected to the system.  The list will provide:
  * Local IP address of everyone
  * Hostname of everyone
  * Real Name of everyone
* Provide a communication platform
  * Internal chat functionality
* Connect it all to a DB for persistence
* Provide STEAM information (currently playing, join game, etc).


Long term goals
---------------
* Voting
* Tournament Organization

Requirements
------------
* php5.3 or greater (5.4 prefered)
* Mysql
* Server that can host the application locally
* Unix based server. (MAC os X will require that you set the server IP manually in the config file)

Setup
-----
1. Clone repository to server
2. Set up mysql user and database
3. Copy config.sample.php to config.inc.php
4. Complete the config.inc.php file
5. Install the database schema: From a command line interface run 'php scripts/install.php'
6. To run the server, run: 'php bin/server.php'
