<h3><b>phpMWSLP</b> - php multi-core webserver log parser</h3>

* [About](#about-program)
* [Configuration](#configuration)
* [Requirements](#requirements)
* [License](#license)

### About program

The specific of the program is that it can process the web server log file in multi-threaded (multi-core) mode.
<br>How it works: 
<br>A large log file is cutting by fragments and processed in several threads (you can specify a number of processor core for a thread).
<br>Data is stored in a SQLite database based on JSON strings.
<br>The program creates a record in the SQLite database for the specified date and updates it as the threads are finished.
<p>
For now the program has following modules:
</p>
<ul>
  <li>module_ip_top_100  // Top 100 IP addresses</li>
  <li>module_ip_unique_count // Total number of unique IP addresses</li>
  <li>module_display_resolutions // Screen resolutions</li>
  <li>module_device_type // Device type</li>
  <li>module_windows_version // Windows versions</li>
  <li>module_os_info // Information about operating systems</li>
  <li>module_iphone_version // iPhone version</li>
  <li>module_ipad_version // iPad version</li>
  <li>module_macintosh_version // iMac version</li>
  <li>module_android_version // Android version</li>
  <li>module_social_networks // Transitions from social networks</li>
  <li>module_search_engines // Transitions from search engines</li>
  <li>module_all_referal_links // All referral links</li>
  <li>module_all_requests // All requests to the web server</li>
  <li>module_all_referal_links_exclude_known // All referrals from sources excluding known ones</li>
  <li>module_search_engines_google // Transitions from google</li>
  <li>module_status_404_requests // All requests with status 404</li>
  <li>module_status_200_requests // All requests with status 200</li>
  <li>module_10min_online_users_count // Number of online every 10 minutes</li>
  <li>module_day_online_users_count // Number of online users between selected dates</li>
  <li>module_cities // Cities statistics</li>
  <li>module_countriesISO // Countries code statistics</li>
  <li>module_countries // Countries statistics</li>
  </ul>
    <p>
The result can be viewed with javascript C3 library:
    </p>
module_day_online_users_count:
<br><img src="https://raw.githubusercontent.com/alchemist314/images/main/phpMWSLP/traffic.png">
module_windows_versions:
<br><img src="https://raw.githubusercontent.com/alchemist314/images/main/phpMWSLP/windows_version.png">
module_device_type, module_10min_online_users_count:
<br><img src="https://raw.githubusercontent.com/alchemist314/images/main/phpMWSLP/os_and_traffic.png"> 

### Configuration

<br>First of all, you need to check the web server log configuration.
<br>For <b>Nginx</b>, set the following log format:

    
```
log_format  main  '$remote_addr - $remote_user [$time_local] "$request" '
                  '$status $body_bytes_sent "$http_referer" '
                  '"$http_user_agent" "$http_x_forwarded_for"';
```

When you access the site, you should see an entry like this (in your web server log file):

```
192.168.1.2 - - [11/Oct/2023:03:34:02 +0300] "GET https%3A//192.168.1.1/test.html HT
TP/1.0" 200 49 "https://192.168.1.1/test.html" "Mozilla/5.0 (Linux; arm_64; Android 13; 21081111RG)
AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 YaBrowser/23.7.5.95.00 SA/3 Mobile Safari/53
7.36"  "192.168.1.2"
```
For collection more information like display resolutions, referal links e.t.c. you should put code below to pages on your web site:

```
<script type="text/javascript">
document.write("<img src='https://your-site-name/stat?s=1;x"+
escape(document.referrer)+
((typeof(screen)=="undefined")?"":";x"+
screen.width+"x"+screen.height+"x"+
(screen.colorDepth?screen.colorDepth:screen.pixelDepth))+
";x"+escape(document.URL)+";x"+escape(navigator.platform)+
";x"+escape(navigator.userAgent)' alt='' border=0 width=0 height=0><\/a>")
</script>

```
Create empty `stat` file in your site root directory.

Now that the logs are done, you can setup the program:

1. Edit the file `app/config/config.php`:
   * Set the path to the root directory (app) `PHP_MWSLP_ROOT`
   * Set the name of the SQLite database `PHP_MWSLP_PDO_TABLENAME`
   * Set the path to the GeoIP database GeoLite2-City.mmdb `PHP_MWSLP_GEO_DB`
   * Set the URL to the public folder `PHP_MWSLP_HTTP`
  
   Additionally, you can enable the following variables:
   
   * Show module output (false/true) `PHP_MWSLP_SHOW_MODULE_OUTPUT`
   * Show SQL query (false/true) `PHP_MWSLP_SHOW_SQL_QUERY`
   * Show line counter (false/true) `PHP_MWSLP_SHOW_LINE_COUNTER`
   * URL length for output `PHP_MWSLP_URL_LENGTH`
   * Read the log file from gz archive (false/true) `PHP_MWSLP_LOG_IS_GZIP`
   * Show horizontal line on the chart (false/true) `PHP_MWSLP_CHART_YGRID_LINE`

3. Edit the file `app/scripts/config`:
  * Set the date for which the log will be processed:
    <br>`log_date=01.05.2024`
  * Set the directory where the web server logs are stored:
    <br>`log_path=/dist/STAT/01.24`
  * Set the name (prefix) of the first part of the log files:
    <br>`log_prefix=access.log`
    <br>(the `/dist/STAT/01.24/` folder should contain web server log files, for example:
     <br>`access.log-20240101.gz`
     <br>`access.log-20240102.gz`
     <br>`access.log-20240103.gz`
     <br>`access.log-20240104.gz`)
  * Set the folder where manipulations with logs will be made:
    <br>`log_tmp=/var/www/html/git/web_stat_tmp/app/tmp`
  * Set the folder where the web server log files will be cut into parts for multi-threaded processing:
    <br>`log_tmp_parts=/var/www/html/git/web_stat_tmp/app/tmp/parts`

  Now you can create a database for storage log data:
  <br>Go to the `/app/install/` folder and run `app/install/create_db.php` (for example, run: `/usr/bin/php -q app/install/create_db.php`)
  <br>In the `app/sql/` folder you should have a SQLite database.

  Now you can try to create first entry.

  Steps to launch the program:
  
  1. Running the script `app/scripts/1_log_glue.sh`
  (Merge the logs for today, yesterday and tomorrow)

  2. Running the script `app/scripts/2_grep_date.sh`
  (Collects logs on the specified date)

  3. Running the script `app/scripts/3_split_file.sh`
  (Cutting the log file by fragments for multi-threaded processing)

  4. Run the file `app/scripts/4_first.sh`
  (This step will create the first record in the SQLite database, which will be updated by processing threads)

  5. Run the file `app/scripts/5_parse_core.sh`
  (this file will launch 3 background threads (0, 1, 2) processing the log file.
  These threads will process modules from the `app/modules/` folder:

       * The "0" thread will collect the following information:
         <br>(modules_core0)
          <ul>
            <li>module_browsers_names // Browsers names</li>
            <li>module_browsers_versions // Top 100 browsers versions</li>
            <li>module_ip_top_100 // Top 100 IP addresses</li>
            <li>module_ip_unique_count // Total number of unique IP addresses</li>
            <li>module_display_resolutions // Screen resolutions</li>
            <li>module_device_type // Device type</li>
            <li>module_windows_version // Windows versions</li>
            <li>module_os_info // Information about operating systems</li>
            <li>module_iphone_version // iPhone version</li>
            <li>module_ipad_version // iPad version</li>
          </ul>

       * The "1" thread will collect the following information:
          <br>(modules_core1)
         <ul>
          <li>module_social_networks // Transitions from social networks</li>
          <li>module_search_engines // Transitions from search engines</li>
          <li>module_all_referal_links // All referral links</li>
          <li>module_all_requests // All requests to the web server</li>
          <li>module_all_referal_links_exclude_known // All referrals from sources excluding known ones</li>
          <li>module_search_engines_google //All requests from google</li>
          <li>module_status_404_requests // All requests with status 404</li>
          <li>module_status_200_requests // All requests with status 200</li>
         </ul>
         
      * The "2" thread will collect the following information:
          <br>(modules_core2)
          <ul>
           <li>module_10min_online_users_count // number of online user every 10 minutes</li>
          </ul>
          
      * The "3" thread will collect the following information:
          <br>(modules_core3)
        <ul>
          <li>module_cities // Cities statistics</li>
          <li>module_countriesISO // Countries code statistics</li>
          <li>module_countries // Countries statistics</li>
        </ul>
        
     The module described above is excluded from processing by default, since it is the most resource-intensive and is processed by a separate multi-threaded (multi-processor) script:

        6. Run the file `app/scripts/parse_to_file.sh`
        (this script will launch 4 background processing threads for collecting Cities and Countries)

        7. Make sure that the previous threads was completed (for example, using the command `ps -aux | grep parse_to_file.sh`) and then run the script `app/scripts/merge.sh`
        (this script will merge the results of 4 previous threads and add data to the database)

Some other useful scripts from folder `app/scripts/`:

For multiple date parsing you can use `app/scripts/multiple_parse.sh`

Instead of running the `app/scripts/5_parse_core.sh` script, you can run each thread separately:
      <ul>
        <li>parse_core0.sh</li>
        <li>parse_core1.sh</li>
        <li>parse_core2.sh</li>
        <li>parse_core3.sh</li>
      </ul>
Also, instead of running `app/scripts/parse_to_file`, you can run each thread separately:
    <ul>
        <li>parse_to_file_core0.sh</li>
        <li>parse_to_file_core1.sh</li>
        <li>parse_to_file_core2.sh</li>
        <li>parse_to_file_core3.sh</li>
    </ul>
    
### Requirements


* PHP >= 7.3
* PDO
* SQLIte
* MaxMind DB Reader
* MaxMind Web Service Clients
* GeoIP2 PHP API

### License

MIT License
