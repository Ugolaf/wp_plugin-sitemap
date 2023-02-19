# Explanation

## The problem to be solved
The problem to be solved is that the administrator of a WordPress website wants to be able to see how their website pages are linked together to the home page so that they can manually search for ways to improve their SEO rankings. In order to achieve this, we need to create a web crawler that can extract all the internal hyperlinks from the website's home page and generate a sitemap that can be viewed by the administrator. The administrator need an UI to visualize his sitemap.

## My preliminary reaction
After analyzing the primary need for the user story, I first looked for existing solutions. And I found many solution from the WordPress marketplace such as (Sitemap – Create a Responsive HTML Sitemap) or (WP Sitemap Page) which are plugins dealing only with the need "creation of sitemap", but we can also generate a sitemap with more complete plugins such as Yoast SEO or Elementor.

In the simple case of the user storie I would not have created a plugin but used an existing solution. But in the event that we are obliged to create it, I have analyzed what was being done by the competitors and what points could therefore be improved. 

So I decided to allow the administrator to adjust the crawler depth, which can allow him to make a progressive analysis and not being disturbed by too much informations on a large website. Secondly, I helped the administrator by displaying the sitemap as a tree diagram, like a family tree. I decided not to do any upgrades to the generated sitemap, because the real users are search engine robots.

## Technical spec
The solution will be a PHP web crawler that will extract all of the internal hyperlinks from the website's home page and store the results temporarily in the database. It will also generate a sitemap.html file that will show the results as a sitemap list structure. The web crawler will be triggered manually by the administrator and will run every hour after the initial crawl.

## Technical decisions
I decided to use PHP because it's the core language of WordPress. I also chose to use MySQL as the database management system because it is easy to use, secure, and reliable.

For the web crawling functionality, I decided to use the DOMDocument class in PHP to extract all of the internal hyperlinks from the website's home page. I also decided to use the cron job scheduler to run the web crawler every hour.

For the sitemap generation functionality, I decided to use PHP's WordPress functions to generate a sitemap.html file that would display the results as a sitemap list structure.

## How the code works
The code works as follows:

On plugin activation, two tables are created on the current database used by Wordpress.
The administrator logs in to the back-end admin page and triggers a crawl.
The web crawler starts by deleting the results from the last crawl and the sitemap.html file if they exist.
The web crawler extracts all of the internal hyperlinks recursivly with a maximum depth given from the website's home page using the DOMDocument class and stores the results temporarily in the database.
The web parser displays the results on the admin page from database results as a visual tree with some css.
The web parser generates a sitemap.html file that shows the results as a sitemap list structure from database results.
Visitors or search engines can view the sitemap.html page from the front end.


## How the solution achieves the admin's desired outcome
The solution achieves the admin's desired outcome by providing a way for them to see how their website pages are linked together to the home page. The web crawler extracts all of the internal hyperlinks from the website's home page and generates a sitemap that can be viewed by the administrator. This allows the administrator to manually search for ways to improve their SEO rankings. The web crawler runs every hour to ensure that the sitemap is always up-to-date.
