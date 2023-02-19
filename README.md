# WP Plugin sitemap

[![Build Status](https://app.travis-ci.com/Ugolaf/wp_plugin-sitemap.svg?branch=master)](https://app.travis-ci.com/Ugolaf/wp_plugin-sitemap)

This plugin allows a WordPress administrator to manually trigger a crawl of their website pages and view the results on a back-end admin page. The crawler extracts all of the internal hyperlinks from the website's home page and stores the results temporarily in the database. It then displays the results on the admin page and creates a sitemap.html file that shows the results as a sitemap list structure.

[![wp-plugin-sitemap.png](https://i.postimg.cc/L6G5dSgX/wp-plugin-sitemap.png)](https://postimg.cc/CzHSDWNT)


### Features
- Back-end admin page for manual triggering of website crawl
- Crawls website every hour
- Extracts internal hyperlinks from website's home page
- Stores results temporarily in the database
- Deletes the results from the last crawl and sitemap.html file
- Displays results on the admin page
- Displays error notice if an error occurs
- Creates a sitemap.html file that shows the results as a sitemap list structure
- Front-end access for visitors to view the sitemap.html page

### Requirements
WordPress 5.0 or higher
MySQL or MariaDB

### Installation
Download the wp_plugin-sitemap.zip file and paste it to your WordPress plugin admin page.
or
Clone or download the repository and copy the files to your WordPress plugin directory.

Activate the plugin in WordPress.
Access the back-end admin page and trigger a crawl to start generating the sitemap. This will also run the task hourly in the background.

### Extra
-The plugin passes phpcs inspection
-Wired to Travis CI
[![travis-ci-wp-plugin-sitemap.png](https://i.postimg.cc/Bbk9S6nb/travis-ci-wp-plugin-sitemap.png)](https://postimg.cc/bSxFLqbf)
 - There is a package requirements conflict for php 7.2 with
   - composer version 2 -> need dealerdirect/phpcodesniffer-composer-installer 0.7.2 instead of 0.5.0
    - wp-coding-standards/wpcs ^2 -> need dealerdirect/phpcodesniffer-composer-installer[^0.5.0]

### Todo
- Add unit tests
- Add integration tests
### Usage
1. Log in to the back-end admin page.
2. Trigger a crawl to start generating the sitemap.
3. View the results on the admin page.
4. Access the sitemap.html page from the front-end.

### Credits
This app was developed by Ugo LAFAILLE. Based on [wp-media/package-template](https://github.com/wp-media/package-template).
