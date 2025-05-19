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

- Create a blnak project `$ composer create-project freeframe/freeframe my-app`
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
> Install Mongodb extension
```sh
extension=php_mongodb.dll
```
### Migrate Tables from RDB
```sh
$ php free migrate
```

### Create Routes
> At routes/route.php
```php
use App\Controllers\HomeController;

$router->get('/', 'HomeController@index');
```

### 👏 Credit
Built with ❤️ by **[Sagnik Dey](https://github.com/sagnikrivud)**

💻 Tech Stack


![CSS3](https://img.shields.io/badge/css3-%231572B6.svg?style=plastic&logo=css3&logoColor=white) ![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=plastic&logo=php&logoColor=white) ![HTML5](https://img.shields.io/badge/html5-%23E34F26.svg?style=plastic&logo=html5&logoColor=white) ![JavaScript](https://img.shields.io/badge/javascript-%23323330.svg?style=plastic&logo=javascript&logoColor=%23F7DF1E) ![AWS](https://img.shields.io/badge/AWS-%23FF9900.svg?style=plastic&logo=amazon-aws&logoColor=white) ![Vue.js](https://img.shields.io/badge/vuejs-%2335495e.svg?style=plastic&logo=vuedotjs&logoColor=%234FC08D) ![Vuetify](https://img.shields.io/badge/Vuetify-1867C0?style=plastic&logo=vuetify&logoColor=AEDDFF) ![NPM](https://img.shields.io/badge/NPM-%23000000.svg?style=plastic&logo=npm&logoColor=white) ![jQuery](https://img.shields.io/badge/jquery-%230769AD.svg?style=plastic&logo=jquery&logoColor=white) ![Express.js](https://img.shields.io/badge/express.js-%23404d59.svg?style=plastic&logo=express&logoColor=%2361DAFB) ![Laravel](https://img.shields.io/badge/laravel-%23FF2D20.svg?style=plastic&logo=laravel&logoColor=white) ![NuxtJS](https://img.shields.io/badge/Nuxt-black?style=plastic&logo=nuxt.js&logoColor=white) ![Socket.io](https://img.shields.io/badge/Socket.io-black?style=plastic&logo=socket.io&badgeColor=010101) ![Apache](https://img.shields.io/badge/apache-%23D42029.svg?style=plastic&logo=apache&logoColor=white) ![MariaDB](https://img.shields.io/badge/MariaDB-003545?style=plastic&logo=mariadb&logoColor=white) ![MongoDB](https://img.shields.io/badge/MongoDB-%234ea94b.svg?style=plastic&logo=mongodb&logoColor=white) ![MySQL](https://img.shields.io/badge/mysql-%2300f.svg?style=plastic&logo=mysql&logoColor=white) ![SQLite](https://img.shields.io/badge/sqlite-%2307405e.svg?style=plastic&logo=sqlite&logoColor=white) ![Inkscape](https://img.shields.io/badge/Inkscape-e0e0e0?style=plastic&logo=inkscape&logoColor=080A13) ![Jira](https://img.shields.io/badge/jira-%230A0FFF.svg?style=plastic&logo=jira&logoColor=white) ![Vagrant](https://img.shields.io/badge/vagrant-%231563FF.svg?style=plastic&logo=vagrant&logoColor=white) ![Ubuntu](https://img.shields.io/badge/Ubuntu-E95420?style=plastic&logo=ubuntu&logoColor=white)
![Shell](https://img.shields.io/badge/shell-%231563FF.svg?style=plastic&logo=shell&logoColor=white) ![Cakephp](https://img.shields.io/badge/cakephp-%23FF2D20.svg?style=plastic&logo=cakephp&logoColor=white) ![Arduino](https://img.shields.io/badge/arduino-%231563FF.svg?style=plastic&logo=arduino&logoColor=white) ![C++](https://img.shields.io/badge/c++-%231563FF.svg?style=plastic&logo=cplusplus&logoColor=white) ![MsSQLServer](https://img.shields.io/badge/mssql-%23FF2D20.svg?style=plastic&logo=microsoft-sql-server&logoColor=white) ![CodeIgniter](https://img.shields.io/badge/CodeIgniter-%23FF2D20.svg?style=plastic&logo=codeigniter&logoColor=white) ![Lumen](https://img.shields.io/badge/Lumen-%23FF2D20.svg?style=plastic&logo=lumen&logoColor=white) ![Node.js](https://img.shields.io/badge/Node.js-%2343853D.svg?style=plastic&logo=node.js&logoColor=white) ![Postgresql](https://img.shields.io/badge/postgres-%23316192.svg?style=plastic&logo=postgresql&logoColor=white) ![RabbitMQ](https://img.shields.io/badge/Rabbitmq-%23FF6600.svg?style=plastic&logo=rabbitmq&logoColor=white)
