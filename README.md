# phacil-framework
A super easy PHP Framework for web development!

## Requirements

 - PHP 5.3+
 - OPCache PHP Extension
 - HTTP Web Server (Apache 2.4+ recomended)

## Structure

| Folder | Description |
| ------ | ------ |
| Controller | Contains the structure and files of controllers code |
| Model | Contais directories and files for the model code |
| View | Contains the template files |
| Cache | Full writable default cache storage |
| public_html | Contains the index.php and .htaccess Apache file. All your access public files stay here, like CSS, JavaScript, etc. |  
| System | Most important folder of this framework. Contains the libraries, classes and anothers features to improve correct working for the Web App. |
| Logs | Contais debug and error logs |  
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

3. Now create a folder insede view called **default** and a subfolder called **common** and a file home.twig.
4. Edit the **view/default/common/home.twig** like this.
    
 ```html
 <h1>{{ variable }}</h1>
 ```
5. Edit the constants *HTTP_URL* and *DIR_APPLICATION* in file **config.php**. Edit others *DIR_**  constants path if necessary.
 ```php
 <?php

define('HTTP_URL', 'http://phacil.local/');
define('HTTPS_URL', HTTP_URL);
define('HTTP_IMAGE', HTTP_URL);

define('USE_DB_CONFIG', false);

define('DEBUG', true);

$configs = array('PatternSiteTitle'=>' - ExacTI phacil',
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
6. Access your web app in your favorite browser and enjoy this magnific Hello World!

#### Explanation

All routes are a mapping class with extends the primary Controller class. In a simple term, the class name in controller is *Controller****Folder****Filename*.

 `class ControllerFolderFile extends Controller {
 }`

 The public function named `index()` is a default function to generate the page.

  `public function index() { }`

  Array `$this->data` receives in array format the variables to send to template, in this sample called **variable** with the string *Hello World!*.

  In the last we finish with a simple `$this->out();` to indicate the output and process template file. It's a automated mechanism to link this controller with the respective viewer.

  The viewer in this sample is a twig template format ([Twig page](https://twig.symfony.com)).  ExacTI Phacil Framework permits to use varius PHP template engines, like Twig, Mustache, Dwoo and Smarty.

  ## Config file Parameters

  A simple description of default constants configurations in *config.php* file.

  | Constant | Type | Description | Required |
  | ----- | ----- | ----- | ----- |
  | HTTP_URL | string | Contains the URL for this WEB APP | Yes |
  | HTTPS_URL | string | Same this HTTP_URL, but in SSL format | 
  | HTTP_IMAGE | string | The image URL |
  | CDN | string | CDN URL to use for static content |
  | DB_CONFIG | boolean | Permit to use configurations direct in database. Requires database instalattion. Values: true or false. | Yes |
  | DEBUG | boolean | Indicate the debug mode. | Yes |
  | `$configs` | array | No-SQL configs |
  | DIR_APPLICATION | string | Absolute path to application folder | Yes |
  | DIR_LOGS | string | Path to log folder |
  | DIR_PUBLIC | string | Path to the public folder. This directory is a directory to configure in your HTTP server. | Yes |
  | DIR_SYSTEM | string | System directory | Yes |
  | DIR_IMAGE | string | Directory to store images used by Image library. |
  | DIR_TEMPLATE | string | Directory with templates folder | Yes |
  | DIR_CACHE | string | Directory to storage the generated caches. | Yes |
  | DB_DRIVER | string | Driver to connect a database source |
  | DB_HOSTNAME | string | Database host |
  | DB_USERNAME | string | Username to connect a database |
  | DB_PASSWORD | string | Database password |
  | DB_DATABASE | string | Database name |
  | SQL_CACHE | boolean | Use the SQL Select cache system |

  ## Template Engines Support

  This framework supports this PHP templates engines:
  - TPL (basic template with PHP and HTML);
  - [Twig](https://twig.symfony.com);
  - [Mustache](https://mustache.github.io);
  - [Dwoo](http://dwoo.org);
  - [Smarty](https://www.smarty.net).
  
  To use a determined template engine, just create a file with name of engine in extension, for sample, if you like to use a Twig, the template file is **demo.twig**, if desire the Mustache template, use **demo.mustache** extension file.
  The ExacTI Phacil Framework allows to use varius template engines in the same project.

  ## Easy made functions

  This framework is very focused in agile, secutiry and reutilizable code in PHP. Contains a very utily functions we see in the next section.

  ### Database 

  To execute a query:
   ```php
   $variable = $this->db->query("SELECT * FROM mundo");
   ```

   Withou SQL Cache (if enabled):
   ```php
   $variable = $this->db->query("SELECT * FROM mundo", false);
   ```
Escape values to more security (no SQL injection issue):
 `$this->db->escape($value)`

To get this rows:
 `$variable->rows`;

 Get one row: `$variable->row`;

 Number of rows: `$variable->num_rows`;

 Sample:
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

  ### Document easy made

  Use the especial Document class to manipulate easily informations about your final HTML.
  
  To call a document class, use `$this->document` inside Controller.

  To add a CSS: `$this->document->addStyle($href, $rel = 'stylesheet', $media = 'screen', $minify = true)`;

  To add a JavaScript: `$this->document->addScript($script, $sort = '0', $minify = true)`;

Facebook Metatag: `$this->document->addFBMeta($property, $content = '')`;

Document HTML Title: `$this->document->setTitle($title)`;

Page description: `$this->document->setDescription($description)`;

  Sample:
   ```php
   <?php
   class ControllerCommonHome extends Controller {
	   public function index() {
		   $this->document->addStyle('pipoca.css');
	   }
   }
   ```

### Classes and functions

This framework have a lot of utilities and accepts much more in system folder with autoload format.

#### To show all classes

Execute a Classes() class in one Controller.

 ```php
 $cla = new Classes();

var_dump($cla->classes());
 ```
#### To show all classes and functions registereds 

 Execute a Classes() class and call functions() in one Controller.

 ```php
 $cla = new Classes('HTML');

var_dump($cla->functions());
 ```

 **Note:** *The HTML parameter is the output in HTML style. Is optional.*


## Loaders

In this framework, loaders is a simple way to get resources to use in your PHP code. Is very intuitive and requirew few steps.

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

## Models

This framework is totally MVC (Model, View and Controller) based. The models is just like the controllers and uses the same structure, with a different folder. 

To create a model, put in the models folder a directory and file with the code. 
 ```php
 <?php

 class ModelFolderFile extends Model {
	 public function SayMyName() {
		 return "Heisenberg";
	 }
 }
 
 ```

 ## Constructs

 In same cases we need to add a __construct in a class to better code practices. To call a constructs in controllers and models, use:
  ```php
  public function __construct($registry)
    {
		parent::__construct($registry);
		
		// YOUR CODE HERE AFTER THIS LINE!
    }
  ```

  ## Requests and Sessions

  To use a magic request system, you just need to call a `$this->request` method. For sample, to obtain a POST value, use `$this->request->post['field']` to get the post value with security. 
  For a \$_SERVER predefined variables, use `$this->request->server['VALUE']` and $this->request->get() for \$_GET values. 
  The advantages to use this requests instead the predefined variables of PHP is more the more security, upgradable and unicode values.

  ### Sessions

  Sessions is a little different method, you can define and use with `$this->session->data['name']`.

## Special controller parameters

| Parameter | Type | Description|
| ----- | ----- | ----- |
| `$this->template` | string | Specify manualy the path to template |
| `$this->twig` | array | Create Twig aditional functions. Eg.: `$this->twig = array('base64' => function($str) { return base64_encode($str); }); `|
| `$this->children` | array | Load in variable other renders of web app. Th childrens *footer* and *header* is default loaded when uses `$this->out()` output. To load without this defaults childrens, use `$this->out(false)`.|
| `$this->redirect($url)` | string | Create a header to redirect to another page. |





## Routes 

The ExacTI Phacil Framework is a simple router base to create a simple and consistent web navigation.

Withou SEO URL, we invoke a page with *route* get parameter when contains a scheme folder/file of controller, like this: *http://example.com/index.php?route=folder/file*.

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

 If we access *index.php?route=folder/file* we see the "Index" message. But, like a tree, if we access *index.php?route=folder/file/another* obtains the another function code, with "other" message. 
 
 Multiple Get parameters is simple and we can define normaly, eg.: *index.php?route=folder/file/another&p=2* to print "another" message.

 Private and protected functions is only for internal use, if we tried to access *index.php?route=folder/file/foo*, the framework return 404 HTTP error.

 ### SEO URL

 If you need a beautiful URL for SEO or other optimizations, you can use the url_alias database table. Is very simple and "translate" the routes in URL.

 Imagine this SQL table called url_alias:

 | url_alias_id | query | get | keyword |
 | ----- | ----- | ----- | ----- |
 | 1 | contact/contato | | contact|
 | 2 | webservice/sitemap | | sitemap |

 With the url_alias, the access to route *http://example.com/index.php?route=contact/contato* and *http://example.com/contact* is the same!(and very prety!).

 ### Links

 We have a function to create a strict and dinamic links automatic: `$this->url->link($route, $args = '', $connection = 'NONSSL')`. Is a simple function to generate internal links with correct URL  encode.

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

  With extra GET paramethers:
  ```php
  $fields = array('foo' => 'bar');
  echo $this->url->link('contact/contato', $fields);
  echo $this->url->link('contact/contato/place', $fields);
  ```

  We get this URL in output with GET paramethers: 
  
  *http://example.com/contact?foo=bar*

  *http://example.com/index.php?route=contact/contato/place&foo=bar*

  ## License

  This project is manteined for [ExacTI IT Solutions](https://exacti.com.br) with GPLv3 license.