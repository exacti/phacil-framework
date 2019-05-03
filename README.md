#  Phacil-framework

 ![GitHub](https://img.shields.io/github/license/exacti/phacil-framework.svg)  ![GitHub top language](https://img.shields.io/github/languages/top/exacti/phacil-framework.svg) ![GitHub code size in bytes](https://img.shields.io/github/languages/code-size/exacti/phacil-framework.svg) ![GitHub issues](https://img.shields.io/github/issues/exacti/phacil-framework.svg) ![PHP Version](https://img.shields.io/badge/php-%3E%3D5.3.29-blue.svg) ![GitHub last commit](https://img.shields.io/github/last-commit/exacti/phacil-framework.svg) ![GitHub Release Date](https://img.shields.io/github/release-date/exacti/phacil-framework.svg) ![GitHub release](https://img.shields.io/github/release/exacti/phacil-framework.svg)


A super easy PHP Framework for web development!


## Requirements

 - PHP 5.3+ (PHP 7.0+ recommended with OPCache and igbinary extensions)
 - HTTP Web Server (Apache 2.4+ recommended with mod_rewrite)
 - Some writable directories (like logs and cache)

## Structure

| Folder | Description |
| ------ | ------ |
| Controller | Contains the structure and files of controller's code |
| Model | Contains directories and files for the model code |
| View | Contains the template files |
| Cache | Full writable default cache storage |
| public_html | Contains the index.php and .htaccess Apache file. All your access public files stay here, like CSS, JavaScript, etc. |  
| System | Most important folder of this framework. Contains the libraries, classes and others features to improve correct working for the Web App. |
| Logs | Contains debug and error logs |  
| config.php | File with contains all basic configurations for the Web App, like DB connection, mail config, directory config, etc.|  

## Get started!

Copy or clone this repository in your computer/server and edit the config.php file. See below the Config File Basic Options.

### Hello World! sample

This is a simple Hello World! for this framework.

1. In  **controller** folder, create a folder called  *common* and create a file called *home.php*.
2. Edit **controller/common/home.php** like that.
    ```php
    <?php
    
    class ControllerCommonHome extends Controller {
        public function index() {
            $this->data['variable'] = "Hello World!";
                
            $this->out();
        }
    }
    ```

3. Now create a folder inside view called **default** and a subfolder called **common** and a file home.twig.
4. Edit the **view/default/common/home.twig** like this.
    ```html
    <h1>{{ variable }}</h1>
    ```
5. Rename config.dist.php to config.php and edit the constants *HTTP_URL* and *DIR_APPLICATION* in file **config.php**. Edit others _DIR\_\*_ constants path if necessary.
    ```php
    <?php

    define('HTTP_URL', 'http://phacil.local/');
    define('HTTPS_URL', HTTP_URL);
    define('HTTP_IMAGE', HTTP_URL);
    
    define('USE_DB_CONFIG', false);

    define('DEBUG', true);

    $configs = array(
                 'PatternSiteTitle'=>' - ExacTI phacil',
				 'config_mail_protocol'=>'smtp',
				 'config_error_display' => 1,
				 'config_template' => "default",
				 'config_error_filename'=> 'error.log');

    //App folders
    define('DIR_APPLICATION', '/Applications/MAMP/htdocs/phacil/');
    define('DIR_LOGS', DIR_APPLICATION.'logs/');
    define('DIR_PUBLIC', DIR_APPLICATION.'public_html/');
    define('DIR_SYSTEM', DIR_APPLICATION.'system/');
    define('DIR_IMAGE', DIR_APPLICATION.'public_html/imagens/');
    define('DIR_TEMPLATE', DIR_APPLICATION.'view/');
    define('DIR_CACHE', DIR_APPLICATION.'cache/');
    ```
6. Point your http/php server to public_html folder, access your web app in your favorite browser and enjoy this magnify Hello World!


#### Explanation

All routes are a mapping class with extends the primary Controller class. In a simple term, the class name in controller is *Controller*___Folder___*Filename*.
   
   `class ControllerFolderFile extends Controller {
 }`

 The public function named `index()` is a default function to generate the page.
 
 `public function index() { }`
 
 Array `$this->data` receives in array format the variables to send to template, in this sample called **variable** with the string *Hello World!*.
  
  In the last we finish with a simple `$this->out();` to indicate the output and process template file. It's an automated mechanism to link this controller with the respective viewer.
  
  The viewer in this sample is a twig template format ([Twig page](https://twig.symfony.com)).  ExacTI Phacil Framework permits to use various PHP template engines, like Twig, Mustache, Dwoo and Smarty.
  
## Config file Parameters
  
  A simple description of default constants configurations in *config.php* file.

| Constant | Type | Description | Required |
| ----- | ----- | ----- | ----- |
| HTTP_URL | string | Contains the URL for this WEB APP | Yes |
| HTTPS_URL | string | Same this HTTP_URL, but in SSL format |
| HTTP_IMAGE | string | The image URL |
| CDN | string | CDN URL to use for static content |
| DB_CONFIG | boolean | Permit to use configurations direct in database. Requires database installation. Values: true or false. | Yes |
| DEBUG | boolean | Indicate the debug mode. | Yes |
| `$configs` | array | No-SQL configs |
| DIR_APPLICATION | string | Absolute path to application folder | Yes |
| DIR_LOGS | string | Path to log folder |
| DIR_PUBLIC | string | Path to the public folder. This directory is a directory to configure in your HTTP server. | Yes |
| DIR_SYSTEM | string | System directory | Yes |
| DIR_IMAGE | string | Directory to store images used by Image library. |
| DIR_TEMPLATE | string | Directory with templates folder | Yes |
| DIR_CACHE | string | Directory to storage the generated caches. | Yes |
| CACHE_EXPIRE | timestamp | Time in UNIX timestamp format with seconds for valid cache. Default value is 3600. (1 Hour) |
| CACHE_DRIVER | string | Cache method for Phpfastcache. Default is file. | 
| CACHE_SETTINGS | array | Settings for Phpfastcache (https://www.phpfastcache.com). |
| DIR_CONFIG | string | Directory with extra configs files |
| DB_DRIVER | string | Driver to connect a database source. See below the default's drivers available. |
| DB_HOSTNAME | string | Database host |
| DB_USERNAME | string | Username to connect a database |
| DB_PASSWORD | string | Database password |
| DB_DATABASE | string | Database name |
| SQL_CACHE | boolean | Use the SQL Select cache system |
| ROUTES | array | Specify manually routes |
| DEFAULT_ROUTE | string | Define the default route to assume with initial page. Default is *common/home*. |
| CUSTOM_DB_CONFIG | string | Custom SQL to load application configs in database. |
| NOT_FOUND | string | Custom route to not found page. Default is error/not_found. |

## Outputs and renders

Phacil Framework count with three methods for output content: `$this->response->setOutput()`, `$this->render()` and `$this->out()`.

### setOutput
  When you call a `$this->response->setOutput` inside a controller, you specify an output of content to screen without template. It's very useful for JSON, XML or others data contents.

##### Sample
   ```php
   <?php 
   class ControllerDemoSample extends Controller {
     public function index() {
       $variable = 'value';

       $this->response->setOutput($variable);
       // prints "value" in screen
     }
   }
   ```

### render
The `$this->render` just render the controller with template but not output this. 
Needs to specify a `$this->template` to associate with template file.
It's much used in children's controllers, like headers and footers.

##### Sample
```php
   <?php 
   class ControllerCommonHeader extends Controller {
     public function index() {
       $variable = 'value';

       $this->template = 'default/common/header.twig';

       $this->render();
     }
   }
   ```
### out

The `$this->out` is a smart way to output content to screen and combine the render with an associative template. For sample, if you have a controller in a file called `sample.php` inside the `demo` controller folder and a viewer called `sample.twig` inside `demo` view folder, automatically links with one another and the `$this->template` isn't need (Unless you want to specify another template with a different name).
If you specify `$this->out(false)` the auto childrens "header" and "footer" are not loaded.

For functions inside the controller that are different from index, for automatic mapping with the template it is necessary to add an underscore (_) after the controller name and add the function name (E.g.: contact_work.twig to link with the route common/contact/work, see *Routes* section for more information).

##### Sample
 ```php
 <?php
 class ControllerCommonContact extends Controller {
   public function index() {
     $this->document->setTitle('Contact Us');

     $this->out();
     // automatically link with common/contact.twig template
   }

   public function work() {
     $this->document->setTitle('Work with Us');

     $this->out();
     // automatically link with common/contact_work.twig template
   }
 }
 ```


  
## Template Engines Support

  This framework supports this PHP templates engines:
  - TPL (basic template with PHP and HTML);
  - [Twig](https://twig.symfony.com);
  - [Mustache](https://mustache.github.io);
  - [Dwoo](http://dwoo.org);
  - [Smarty](https://www.smarty.net).
  
  To use a determined template engine, just create a file with name of engine in extension, for sample, if you like to use a Twig, the template file is **demo.twig**, if desire the Mustache template, use **demo.mustache** extension file.
  The ExacTI Phacil Framework allows to use various template engines in the same project.

## Easy made functions

  This framework is very focused in agile, security and reusable code in PHP. Contains a very utile functions we see in the next section.

### Database 

  To execute a query:
   ```php
   $variable = $this->db->query("SELECT * FROM mundo");
   ```

   Without SQL Cache (if config enabled):
   ```php
   $variable = $this->db->query("SELECT * FROM mundo", false);
   ```
Escape values to more security (no SQL injection issue):
 `$this->db->escape($value)`

To get these rows:
 `$variable->rows`;

 Get one row: `$variable->row`;

 Number of rows: `$variable->num_rows`;

##### Sample:
  ```php
  <?php 
  class ModelDemoSample extends Model {
	  public $data = array();

	  public function dataSample ($code) {
		  $variable = $this->db->query("SELECT * FROM settings WHERE code = '". $this->db->escape($code). "'");

		  $this->data['rows'] = $variable->rows;
		  $this->data['totalRecords'] = $variable->num_rows;

		  return $this->data;
	  }
  }
  ```
  

#### Supported databases and drivers

| Driver | Database Type | Description |
| ----- | ----- | ------ |
| db_pdo | MariaDB | Optimized PDO connection to work with MariaDB databases with charset utf8mb4. Also works very well with MySQL.|
| dbmysqli | MySQL/MariaDB | MySQLi PHP connection |
| mssql | MS SQL Server | Use mssql PHP extension for connect to Microsoft SQL Server databases. |
| mpdo | MySQL | PDO connection for MySQL databases |
| mysql | MySQL | Legacy MySQL extension. Works only in PHP 5.|
| oracle | Oracle | Connect to Oracle databases |
| postgre | PostgreSQL | Driver for connect to PostgreSQL databases. |
| sqlsrv | MS SQL Server | Connect a Microsoft SQL Server database with sqlsrv PHP extension. |
| sqlsrvpdo | MS SQL Server | Connect to Microsoft SQL Server using PDO driver. |


### Cache

This framework use the PhpFastCache (https://www.phpfastcache.com) library to provide a most efficient cache system with many possibilities, like Mencache, Redis, APC or a simple file cache.

| Regular drivers | High performances drivers | Development drivers |
|---------------------------------|------------------------------------|-------------------------------|
|  Apc(u)                       | Cassandra                        | Devnull                     |
|  Cookie                       | CouchBase                        | Devfalse                    |
|  Files                        | Couchdb                          | Devtrue                     |
|  Leveldb          | Mongodb                          | Memstatic                   |
|  Memcache(d)                  | Predis                           |                               |
|  Sqlite                       | Redis                            |                               |
|  Wincache         | Riak                             |                               |
|  Xcache           | Ssdb                             |                               |
|  Zend Disk Cache              | Zend Memory Cache                |                               |

To storage caches in binary mode (most fast), PHP need the igbinary extension (https://github.com/igbinary/igbinary). Without igbinary the framework works fine, but cached values is stored using serialize PHP function.

You can configure to use automatic cache in SQL databases with SQL_CACHE constant configuration (see in Config section) also you can use for manual caches using in a controller or model with `$this->cache->set($key, $value)` to set a cache and `$this->cache->get($key)` to obtain a cache value.


### Document easy made

  Use the especial Document class to manipulate easily informations about your final HTML.
  
  To call a document class, use `$this->document` inside Controller.

  To add a CSS: `$this->document->addStyle($href, $rel = 'stylesheet', $media = 'screen', $minify = true)`;

  To add a JavaScript: `$this->document->addScript($script, $sort = '0', $minify = true)`;

Facebook Metatag: `$this->document->addFBMeta($property, $content = '')`;

Document HTML Title: `$this->document->setTitle($title)`;

Page description: `$this->document->setDescription($description)`;

##### Sample:
   ```php
   <?php
   class ControllerCommonHome extends Controller {
	   public function index() {
		   $this->document->addStyle('pipoca.css');
	   }
   }
   ```

### Classes and functions

This framework has a lot of utilities and accepts much more in system folder with autoload format.

#### To show all classes

Execute a Classes() class in one Controller.

 ```php
 $cla = new Classes();

var_dump($cla->classes());
 ```
#### To show all classes and functions registered 

 Execute a Classes() class and call functions() in one Controller.

 ```php
 $cla = new Classes('HTML');

var_dump($cla->functions());
 ```

 **Note:** *The HTML parameter is the output in HTML style. Is optional.*


## Loaders

In this framework, loaders are a simple way to get resources to use in your PHP code. Is very intuitive and require few steps.

For sample, to load a model, is just `$this->load->model('folder/file');` and to use is `$this->model_folder_file->object();`, like this sample:
 ```php
 <?php
 class ControllerSampleUse extends Controller {
	 public function index() {
		 $this->load->model('data/json');
		 $this->data['totalData'] = $this->model_data_json->total();

		 $this->out();
	 }
 }
 
 ```

You can use loaders to:
 - Controllers;
 - Models;
 - Librarys;
 - Configs;
 - Databases;
 - Languages.

### Load database connection

If you need an extra database connection or a different driver/database access, you can use the `$this->load->database($driver, $hostname, $username, $password, $database);` method (see more about databases functions in the Databases section of this document). 

The name of database is a registered object to access database functions.
This load is simple and registry to object of origin. 

##### Sample:
 ```php
 <?php  
 class ModelDemoSample extends Model {
   public function otherData() {
     $this->load->database('mpdo', 'localhost', 'root', 'imtheking', 'nameDatabase');

     $sql = $this->nameDatabase->query("SELECT * FROM mundo");

     return $sql->rows;
   }
 }
 ```

## Models

This framework is totally MVC (Model, View and Controller) based. The models are just like the controllers and uses the same structure, with a different folder. 

To create a model, put in the models folder a directory and file with the code. 
 ```php
 <?php

 class ModelFolderFile extends Model {
	 public function SayMyName() {
		 return "Heisenberg";
	 }
 }
 
 ```

 To use this model in a controller, you can use the `loader`.

  ```php
  <?php
  class ControllerFolderFile extends Controller {
    public function index() {
      $this->load->model('folder/file');
      echo $this->model_folder_file->SayMyName();
      // this output is "Heisenberg".
    }
  }
  ```


## Constructs and Destructs

 In some cases, we need to add a __construct or __destruct in a class to better code practices. To call a constructs in controllers and models, use the `$registry` parent:
  ```php
  public function __construct($registry)
    {
    parent::__construct($registry);
    
    // YOUR CODE HERE AFTER THIS LINE!
    }
  ```

## Requests and Sessions

  To use a magic request system, you just need to call a `$this->request` method. For sample, to obtain a POST value, use `$this->request->post['field']` to get the post value with security. 
  For a $_SERVER predefined variables, use `$this->request->server['VALUE']` and `$this->request->get[key]` for $_GET values. 
  The advantages to use this requests instead the predefined variables of PHP are more the more security, upgradable and unicode values.

### Sessions

  Sessions is a little different method, you can define and use with `$this->session->data['name']`.

## Special controller parameters

| Parameter | Type | Description|
| ----- | ----- | ----- |
| `$this->template` | string | Specify manually the path to template |
| `$this->twig` | array | Create Twig additional functions. E.g.: `$this->twig = array('base64' => function($str) { return base64_encode($str); }); `|
| `$this->children` | array | Load in a template variable other renders of web app. Th children's *footer* and *header* is default loaded when uses `$this->out()` output. To load without this defaults childrens, use `$this->out(false)`.|
| `$this->redirect($url)` | string | Create a header to redirect to another page. |


## Routes 

The ExacTI Phacil Framework is a simple router base to create a simple and consistent web navigation.

Without SEO URL, we invoke a page with *route* get parameter when contains a scheme folder/file of controller, like this: *http://example.com/index.php?route=folder/file*.

In a sample case, we have this controller:
 ```php
 <?php
 class ControllerFolderFile extends Controller {
	 public function index() {
		 echo "Index";
	 }
	 public function another() {
		 echo $this->foo($this->request->get['p']);
	 }
	 private function foo($parameter) {
		 return ($parameter != 0) ? "another" : "other";
	 }
 }
 ```

 If we access *index.php?route=folder/file* we see the "Index" message. But, like a tree, if we access *index.php?route=folder/file/another* obtains another function code, with "other" message. 
 
 Multiple Get parameters is simple and we can define normally, e.g.: *index.php?route=folder/file/another&p=2* to print "another" message.

 Private and protected functions is only for internal use, if we tried to access *index.php?route=folder/file/foo*, the framework return 404 HTTP error.

### SEO URL

 If you need a beautiful URL for SEO or other optimizations, you can use the url_alias database table. Is very simple and "translate" the routes in URL.

 Execute this SQL in your database to create a url_alias table:
  ```SQL
  CREATE TABLE `url_alias` (
  `url_alias_id` int(11) NOT NULL AUTO_INCREMENT,
  `query` varchar(255) COLLATE utf8_bin NOT NULL,
  `get` longtext COLLATE utf8_bin,
  `keyword` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`url_alias_id`),
  UNIQUE KEY `keyword` (`keyword`) 
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
  ```

 Imagine this SQL table called url_alias:

| url_alias_id | query | get | keyword |
| ----- | ----- | ----- | ----- |
| 1 | contact/contato | | contact|
| 2 | webservice/sitemap | | sitemap |

 With the url_alias, the access to route *http://example.com/index.php?route=contact/contato* and *http://example.com/contact* is the same!(and very pretty!).

#### SEO URL without Database

 You can create URL without a SQL Database configuration. For this you just need specify routes in the config file using the ***ROUTES*** constant with array content, like this:

  ```php
  define('ROUTES', array(
    'magic' => 'common/home/xml', 
    'json' => 'common/home/json'));
  ```

### Links

 We have a function to create a strict and dynamic links automatic: `$this->url->link($route, $args = '', $connection = 'NONSSL')`. Is a simple function to generate internal links with correct URL  encode.

 In sample of this table above, if we have this code:
  ```php
  echo $this->url->link('contact/contato');
  ```

  We get this URL in output: *http://example.com/contact*

  But if you don't have a route in url_alias table, returns the complete route. 

  ```php
  echo $this->url->link('catalog/products');
  ```

  Return this URL in output: *http://example.com/index.php?route=catalog/products*

  With extra GET parameters:
  ```php
  $fields = array('foo' => 'bar');
  echo $this->url->link('contact/contato', $fields);
  echo $this->url->link('contact/contato/place', $fields);
  ```

  We get this URL in output with GET parameters: 
  
  *http://example.com/contact?foo=bar*

  *http://example.com/index.php?route=contact/contato/place&foo=bar*

   ***Note:*** *It's necessary specify the config `config_seo_url` for correctly function of this URLs. If you use the SQL url_alias table, you need  specify the `USE_DB_CONFIG` to true in config file.*
   
### Passing URI Segments to your Functions
   
   Use the *'__%__'* character and its variations (see below) to create a route with wildcard. All contents in the URL will matches with wildcard is passed to your controller function as argument.
   
| Wildcard | Description |
| ----- | -----|
| %d | Matches any decimal digit equivalent to [0-9].|
| %w | Matches any letter, digit or underscore. Equivalent to [a-zA-Z0-9_]. Spaces or others character is not allowed. |
| %a | Matches any character in the valid ASCII range. Latin characters like *'ç'* or *'ã'* is not accepted. |
| % | Accept any character.| 
   
##### Sample:
   ```php
   define("ROUTES", array(
        "produto/%d/%/promo" => "feriado/natal/presentes"
   )
   ```
   
   ```php
   <?php
   class ControllerFeriadoNatal extends Controller {
       public function presentes($id, $name) {
           echo $id;
           echo $name;
       } 
   }
   ```
   
   In this sample above, imagine the URL _http://yoursite.com/produto/**87**/**alfajor**/promo_, this URL is sending to function *presentes* values for `$id` and `$name` arguments in sequential method, in other words, the value of `$id` is set to `87` and `$name` to `alfajor`. The wildcard *%d* in router relative to `$id` argument define is only accepted number values.
   
   In this moment, this resource is only available in constant ROUTES method. SQL routes not support wildcard at this moment.

## JSON, XML and others custom responses

  If you need to output in another format or another response, you can use the Response method `$this->response`.

  For sample, to create a controller when the output is a JSON data:

   ```php
   <?php
   class ControllerApiData extends Controller {
	   public function index() {
		   $record = array(array("index" => 482, "id" => 221), array("index" => 566, "id" => 328));

		   $json = json_encode($record);

		   $this->response->addHeader('Content-Type: application/json');

		   $this->response->setOutput($json);
	   }
   }
   ```

   The `$this->response->addHeader` is responsible to send a personalized HTTP header to browser, in this case specify the *Content-Type* information. The `$this->response->setOutput()` works for send  output of the content of a variable, function, constant, etc., to the browser.

   We have this output:

 ```json
 [{"index":482,"id":221},{"index":566,"id":328}]
 ```

 Another sample, but with XML data:

  ```php
  <?php
  class ControllerApiData extends Controller {
	  public function xml() {
        $test_array = array (
            'bla' => 'blub',
            'foo' => 'bar',
        );
        $xml = new SimpleXMLElement('<root/>');
        array_walk_recursive($test_array, array ($xml, 'addChild'));

        $this->response->addHeader('Content-Type: text/xml');

        $this->response->setOutput( $xml->asXML() );
    }
  }
  ```

  The response is:
   ```xml
   <?xml version="1.0"?>
<root>
    <blub>bla</blub>
	<bar>foo</bar>
</root>   
   ```

## Set and load configurations

   The config library in Phacil Framework obtains values to use in entire web app of: `$config` array in *config.php* file, the settings table in your configured database or both.

   Is based in key->value mode with options like serialized value.

   To use a configuration value, just call `$this->config->get('nameOfKey')`. 
   
   To storage a config value, use a SQL table called settings or an array.

#### Sample of storage in array method

   ```php
   $config = array(
     "title" => "ExacTI Phacil Framework",
     "config_error_display" => true,
     "config_error_log" => false,
     "phones" => array(
       "BR" => '+55 11 4115-5161',
       "USA" => '+1 (845) 579-0362')
   );
   ```
#### Sample of storage in SQL table

| setting_id | group | key | value | serialized |
| ----- | ----- | ----- | ----- | ----- | 
| 1 |  | title | ExacTI Phacil Framework | 0 |
| 2 | config |  config_error_display | 1 | 0 |
| 3 | config | config_error_log | 0 | 0|
| 4 |  | phones | a:2:{s:2:"BR";s:16:"+55 11 4115-5161";s:3:"USA";s:17:"+1 (845) 579-0362";} | 1 |

### Reserved config keys

   This config keys are reserved to use for framework.
  
  *  **config_error_log:** Define if errors, notices and warnings is stored in log file. *(boolean)*
  *  **config_error_display:** Indicates if PHP show the errors, notices and warnings in screen. *(boolean)*
  *  **config_template:** Folder template name. *(string)*
  * **config_error_filename:** Log filename. *(string)*
  * **config_seo_url:** Indicates if SEO URL routes is enabled. *(boolean)*
  * **config_mail_protocol:** Define the method of mail library use to send e-mails (mail or smtp values only). *(string)*
  * **config_compression:** Output gzip compression value [0-9]. *(integer)*
  * **PatternSiteTitle:** Used to `$this->document->setTitle()` function, replaces %s to this value. *(string)*
  * **date_timezone:** Define the default timezone to entire application. *(string)* 

### Recommended settings SQL Query

#### MySQL/MariaDB sample
 ```SQL
 CREATE TABLE `settings` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `group` varchar(32) COLLATE utf8_bin,
  `key` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `value` mediumtext COLLATE utf8_bin,
  `serialized` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`setting_id`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
 ```
#### MS SQL Server sample
```SQL
 CREATE TABLE settings (
  [setting_id] int NOT NULL IDENTITY PRIMARY KEY,
  [group] varchar(32),
  [key] varchar(64) NOT NULL DEFAULT '',
  [value] nvarchar(max),
  [serialized] tinyint DEFAULT '0',
  UNIQUE ([key])
);
 ```

## Registrations

 If you need to register a class or other resource to use in your entire application, you can create using the file *registrations.php* in the **system** folder.
 Use to specify a `$registry->set('name', $function);` with the function, class or method if you need. After this, just use `$this->name` to access this registry inside a controller or model.

#### Sample to register a simple class
 Declare in /system/registrations.php
  ```php
  <?php
  // use for register aditionals

  $criptoClass = new CriptoClass('salt');
  $registry->set('cripto', $criptoClass);
  ```

  Using in /controler/demo/sample.php
   ```php
   <?php 
   class ControllerDemoSample extends Controller {
     public function index() {
       $value = 123;
       $use = $this->cripto->function($value);

       echo $use;
     }
   }
   ```

   You can make registrations more complex too, for sample, use another database or a load call.

   Declare in /system/registrations.php
 ```php
 <?php
 //use for register aditionals

 class DBne extends Controller {
   public function __construct($registry) {
     parent::__construct($registry);
     $database = "test";
     $this->load->database($driver, $hostname, $username, $password, $database);
     //see the load database method above for better comprehension
    }
 }
 new DBne($registry);
 ```

 Using in /controller/demo/sample.php
  ```php
  class ControllerDemoSample extends Controller {
    public function index() {
      $this->test->query("SELECT * FROM mundo");
    }
  }
  ```

 ***Pay attention:*** *Use the registrations only for objects that you need to access from the entire application. E.g.: If you need to connect a one more database but you just use for one model, it's better to load just inside the model, for awesome performance.*
 
## Add new modules, libraries and functions
 
 To add new classes or functions, you can put a folder in system directory with a autoload.php file. All autoload.php files are included to framework automatically.
 
### Use Composer
 
 Composer is an application-level package manager for the PHP programming language that provides a standard format for managing dependencies of PHP software and required libraries.  
 To use Composer packages and autoloads, just configure your _composer.json_ with this vendor-dir: system/vendor. 
 
 ```json
 {
   "config": {
     "vendor-dir": "./system/vendor/"
   }
 }
 ```


## License

  This project is maintained for [ExacTI IT Solutions](https://exacti.com.br) with GPLv3 license.
