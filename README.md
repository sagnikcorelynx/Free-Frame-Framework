# FreeFrame – A Lightweight PHP Framework

<img src="./Public/assets/logo.webp" width="50%" height="50%"/>

FreeFrame is a lightweight, modular PHP framework inspired by Laravel and CakePHP, built for rapid development with minimal setup. It comes with its own powerful CLI tool named `free`, allowing you to scaffold components, manage your project structure, and streamline development.

---

### 🚀 Features

- Custom CLI tool – free lets you create controllers, models, and run other useful commands.

- Easy to use – Designed to be lightweight and modular, FreeFrame is perfect for building your own web applications.

- Automatic routing – Route your requests easily to the correct controllers and methods.

- Environment handling – Use .env files for configuration, making it easy to manage different environments.

---

### System Compatibility
- PHP 8.0
- Composer 2.0
- Apache (XAMPP, LAMP)
- PDO Driver
- MongoDB driver

### 🧱 Framework Structure
```text
my-app/
├── App/
│   ├── Controllers/
|   |── Middlewares/ 
│   ├── Models/
│   ├── Services/
│   └── Helpers/
├── config/
├── core/
|   ├── bootstrap.php
|   |── Router.php
├── public/
│   └── index.php
├── resources/
│   ├── pages/
│   └── assets/
├── routes/
│   └── route.php
├── storage/
│   ├── Logs/
|   ├── Public/
│   └── Cache/
|   
├── .env
├── .env.example
├── free
├── Execute.sh
├── composer.json
└── README.md
```

### Implemented Features
```text
✔ CLI (php free)

✔ Routing system (routes/route.php)

✔ Controllers (make:controller)

✔ Services (make:service)

✔ Models (make:model)

✔ Middleware (make:middleware)

✔ Logging system (error.log)

✔ log:clear and storage:link commands

✔ Route listing (route:list)

✔ Auto exception logging

✔ Basic MySQL & MongoDB integration setup

✔ Custom command generator (make:command)

✔ Debugger placeholder

✔ CLI server (php free serve)
```

### 🏚️ Namespace or Class not found issue resolved
```sh
$ composer dump-autoload
```
### Publish Framework
- Tag latest commit with a semantic version:
```sh
$ git tag v1.0.0
$ git push origin v1.0.0
```
- Submit Framework repo at Packagist
https://packagist.org/packages, then click on `Update`

- Create a blnak project `$ composer create-project freeframe/freeframe my-app ^2.5`
- Specify version `$ composer create-project freeframe/freeframe my-app "2.5"`
- Alter try `$ composer create-project freeframe/freeframe my-app --stability=dev`

### ✅ Install Symfony Console via Composer
1. Install
```sh
$ composer require symfony/console
```
2. Create CLI Entry File
```text
framework/
    ├── ignite_file        ← this is CLI
    ├── composer.json
    ├── vendor/
```

### 🗂️ Create a Project
```sh
$ composer create-project freeframe/freeframe my-app --stability=dev
```

###  Start Server
```sh
$ php free serve
```
> Open `http://localhost:8000/`

### List of Commands
> Check version
```sh
$ php free --version
```
```sh
FreeFrame CLI v1.0.0
```
###  Create Controller
```sh
$ php free make:controller HomeController
```
> `App\Controllers\New-Controller`

### Create Service Classes under App/Services
```sh
$ php free make:service UserService
```
### Create Model
```sh
$ php free make:model User
```
> Created at `App/Models` folder

### Clear Error logs
```sh
$ php free log:clear
```
### Create Storage link in public
```sh
$ php free storage:link
```
### See Available commands
```sh
$ php free help
```
![Help](Public/assets/helps.PNG)

### List of Routes
```sh
$ php free route:list
```

### Create Middleware
```sh
$ php free make:middleware AuthMiddleware
```

### Connect database (Default Support `Mysql` & `MongoDB`)
> At Controller or Service Layer
```php
use Core\Database;

$db = (new Database())->getConnection();

// Example RDB query
if ($db instanceof PDO) {
    $stmt = $db->query("SELECT * FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Example MongoDB query:
if ($db instanceof \MongoDB\Database) {
    $collection = $db->users;
    $users = $collection->find()->toArray();
}
```
### Install Mongodb extension for PHP
> Download extension for Windows from here according to PHP version: [Click](https://pecl.php.net/package/mongodb/2.0.0/windows)
> Check thread safety or not 
```sh
$ php -i > phpinfo.txt 
```
> Search `Thread Safety`, 💡 If Thread Safety is enabled, you need the TS version of the MongoDB DLL.
If it's disabled, download the NTS version.
> Place the downloaded .dll into: `C:\xampp\php\ext`
> Open your php.ini file `(in C:\xampp\php)` and add: `extension=mongodb`
> Restart Apache using the XAMPP control panel.
```sh
extension=php_mongodb.dll
```
### Install Cassandra DB for PHP
> Download and install cassndra dll (windows) file from here: [Click](https://pecl.php.net/package/cassandra/1.3.2/windows)

> Unzip the Zip and take `php_cassandra.dll` file and then place it on `C:\xampp\php\ext`, after that
Open Open your php.ini file `(in C:\xampp\php)` and add: `extension=php_cassandra.dll`
- For linux
```sh
$ sudo pecl install cassandra
```
Add this extension at php.ini file `(in C:\xampp\php)`
```php
extension=cassandra.so
```

### Migrate Tables from RDB
```sh
$ php free migrate
```

### Create Custom Request
```sh
$ php free make:request CommonRequest
```
```php
use Core\Http\Request;
use Core\Http\Response;
use App\Requests\CommonRequest;

public function store()
{
    $request = new Request();
    $userRequest = new CommonRequest();

    if (!$userRequest->validate($request->all())) {
        return Response::json(['errors' => $userRequest->errors()], 422)->send();
    }

    // Proceed with storing user...

    return Response::json(['message' => 'User created successfully']);
}
```

### Auth Scaffolding feature (JWT Authentication)
```sh
$ php free auth:install
```
> `AuthController`, `AuthMiddleware` will created `JWT secret` will append in `.env`

### ORM Relationships
```php
use App\Models\Profile;
use App\Models\Post;
use App\Models\User;

public function profileDetails()
{
    return $this->hasOnlyOne(Profile::class, 'user_id');
}

public function posts()
{
    return $this->hasManyMore(Post::class, 'user_id');
}

public function author()
{
    return $this->belongsToOnly(User::class, 'user_id');
}

```

### Create Routes
> At routes/route.php
```php
use App\Controllers\HomeController;

$router->get('/', 'HomeController@index');
```
> Define Routes with Prefixes
```php
$router->group(['prefix' => '/api'], function ($router) {
    $router->get('/users', 'UserController@index');
    $router->post('/login', 'AuthController@login');
});

$router->group(['prefix' => '/admin'], function ($router) {
    $router->get('/dashboard', 'AdminController@dashboard');
    $router->post('/settings', 'AdminController@saveSettings');
});
```
This will shown as
```text
/api/users

/api/login

/admin/dashboard

/admin/settings
```

### Session Manage
```php
use Core\Facades\Session;

//Set a Key
Session::set($key, $value);
// Get a Key Value
Session::get($key);
//Remove Key
Session::forget($key);
//Check session has a particular key
Session::has($key);
//Clear all Session
Session::flush();
```

### PHP Curl Operation
```php
use Core\Http\HttpRemote;

(array) $headers;
(array) $data;

$http = new HttpRemote();
//Fetch a remote URL
$http->get($url, $headers);
// Post data to Remote URL
$http->post($url, $data, $headers);
// Update to Remote URL
$http->put($url, $data, $headers);
//Delete request
$http->delete($url, $data, $headers);
```

### Global Middlewares
> `App/Middleware/RateLimitMiddleware`
```php
// Maximum requests allowed per time window
protected int $maxRequests = 10;
// Time window duration in seconds (e.g., 60 seconds = 1 minute)
protected int $timeWindow = 60;
```
> `App/Middleware/AuthMiddleware`
```php
public function handle($request, Closure $next)
{
    // Add your logic here

    return $next($request);
}
```

### Json Resource Class
> Create a Custom Json Resource file Ex: `UserResource` under `App\Resources`
```php
namespace App\Resources;

use App\Resources\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(): array
    {
        return [
            'user_id' => $this->resource['id'],
            'full_name' => $this->resource['name'],
            'email' => $this->resource['email'],
            // Add more formatted fields here
        ];
    }
}
// Use in Controller
$userResource = new UserResource($user);
$userResource->send();
```

### Maintain Migration file
> Create a `users` table
```sh
$ php free make:migration create_users_table
```
> Migrate the db
```sh
$ php free migrate
```
> Drop a table
```sh
$ php free make:migration drop_orders_table
```
### Language manage
> Add language files at `lang` directory in array format, and then set the desired lang code at `config/app.php`
```php
lang('lang_key_name');
```

### Mailing system
```php
use Core\Mail\Mail;

$mailer = new Mail();
$mailer->to('recipient@example.com', 'John Doe')
     ->subject('Welcome to FreeFrame!')
     ->body('<h1>Hello from FreeFrame</h1><p>This is your welcome email.</p>')
     ->send();
```

### IMAP (Incoming Mail Access Protocol) Feature
[Docuemntation:](https://github.com/Webklex/php-imap) 
> Package used 
```sh
$ composer require webklex/php-imap
```

Fill the `.env`
```env
IMAP_HOST=imap.gmail.com
IMAP_PORT=993
IMAP_ENCRYPTION=ssl
IMAP_VALIDATE_CERT=true
IMAP_USERNAME=your_email@gmail.com
IMAP_PASSWORD=your_password
```
Usage
```php
use Core\Mail\ImapClient;

$client = new ImapClient();
$mailbox = $client->getFolders();
$mails = $client->getInboxMessages();
$spambox = $client->getSpamMessages();
```

### Payments supported
1. [Stripe](https://docs.stripe.com/api)
2. [Paypal](https://developer.paypal.com/api/rest/)
3. [Razorpay](https://razorpay.com/docs/api/)
4. [Square payment](https://developer.squareup.com/docs)
- Use
```php
use Payments\PaymentManager;
use Payments\Gateways\StripeGateway;
use Payments\Gateways\PaypalGateway;
use Payments\Gateways\RazorpayGateway;
use Payments\Gateways\SquareGateway;
```

### 👏 Credit
Built with ❤️ by **[Sagnik Dey](https://github.com/sagnikrivud)**


I'm a self-taught programmer and I'm open to any kind of feedback/suggestions. This framework is a hobby project and I'm doing it in my free time. If you find any bug or something that you think should be improved please open an issue or make a pull request.

I'm also a big fan of the Laravel framework, and I've been inspired by it, so if you see something that looks like Laravel, is because I like how they do things. But, I'm not trying to copy them, I'm just trying to do something similar but with my own style.


💻 Tech Stack


![CSS3](https://img.shields.io/badge/css3-%231572B6.svg?style=plastic&logo=css3&logoColor=white) ![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=plastic&logo=php&logoColor=white) ![HTML5](https://img.shields.io/badge/html5-%23E34F26.svg?style=plastic&logo=html5&logoColor=white) ![JavaScript](https://img.shields.io/badge/javascript-%23323330.svg?style=plastic&logo=javascript&logoColor=%23F7DF1E) ![AWS](https://img.shields.io/badge/AWS-%23FF9900.svg?style=plastic&logo=amazon-aws&logoColor=white) ![Vue.js](https://img.shields.io/badge/vuejs-%2335495e.svg?style=plastic&logo=vuedotjs&logoColor=%234FC08D) ![Vuetify](https://img.shields.io/badge/Vuetify-1867C0?style=plastic&logo=vuetify&logoColor=AEDDFF) ![NPM](https://img.shields.io/badge/NPM-%23000000.svg?style=plastic&logo=npm&logoColor=white) ![jQuery](https://img.shields.io/badge/jquery-%230769AD.svg?style=plastic&logo=jquery&logoColor=white) ![Express.js](https://img.shields.io/badge/express.js-%23404d59.svg?style=plastic&logo=express&logoColor=%2361DAFB) ![Laravel](https://img.shields.io/badge/laravel-%23FF2D20.svg?style=plastic&logo=laravel&logoColor=white) ![NuxtJS](https://img.shields.io/badge/Nuxt-black?style=plastic&logo=nuxt.js&logoColor=white) ![Socket.io](https://img.shields.io/badge/Socket.io-black?style=plastic&logo=socket.io&badgeColor=010101) ![Apache](https://img.shields.io/badge/apache-%23D42029.svg?style=plastic&logo=apache&logoColor=white) ![MariaDB](https://img.shields.io/badge/MariaDB-003545?style=plastic&logo=mariadb&logoColor=white) ![MongoDB](https://img.shields.io/badge/MongoDB-%234ea94b.svg?style=plastic&logo=mongodb&logoColor=white) ![MySQL](https://img.shields.io/badge/mysql-%2300f.svg?style=plastic&logo=mysql&logoColor=white) ![SQLite](https://img.shields.io/badge/sqlite-%2307405e.svg?style=plastic&logo=sqlite&logoColor=white) ![Inkscape](https://img.shields.io/badge/Inkscape-e0e0e0?style=plastic&logo=inkscape&logoColor=080A13) ![Jira](https://img.shields.io/badge/jira-%230A0FFF.svg?style=plastic&logo=jira&logoColor=white) ![Vagrant](https://img.shields.io/badge/vagrant-%231563FF.svg?style=plastic&logo=vagrant&logoColor=white) ![Ubuntu](https://img.shields.io/badge/Ubuntu-E95420?style=plastic&logo=ubuntu&logoColor=white)
![Shell](https://img.shields.io/badge/shell-%231563FF.svg?style=plastic&logo=gnubash&logoColor=white) ![Cakephp](https://img.shields.io/badge/cakephp-%23FF2D20.svg?style=plastic&logo=cakephp&logoColor=white) ![Arduino](https://img.shields.io/badge/arduino-%231563FF.svg?style=plastic&logo=arduino&logoColor=white) ![C++](https://img.shields.io/badge/c++-%231563FF.svg?style=plastic&logo=cplusplus&logoColor=white) ![MsSQLServer](https://img.shields.io/badge/mssql-%23FF2D20.svg?style=plastic&logo=microsoft-sql-server&logoColor=white) ![CodeIgniter](https://img.shields.io/badge/CodeIgniter-%23FF2D20.svg?style=plastic&logo=codeigniter&logoColor=white) ![Lumen](https://img.shields.io/badge/Lumen-%23FF2D20.svg?style=plastic&logo=lumen&logoColor=white) ![Node.js](https://img.shields.io/badge/Node.js-%2343853D.svg?style=plastic&logo=nodedotjs&logoColor=white) ![Postgresql](https://img.shields.io/badge/postgres-%23316192.svg?style=plastic&logo=postgresql&logoColor=white) ![RabbitMQ](https://img.shields.io/badge/Rabbitmq-%23FF6600.svg?style=plastic&logo=rabbitmq&logoColor=white)
