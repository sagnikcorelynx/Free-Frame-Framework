# FreeFrame – A Lightweight PHP Framework

FreeFrame is a lightweight, modular PHP framework inspired by Laravel and CakePHP, built for rapid development with minimal setup. It comes with its own powerful CLI tool named `free`, allowing you to scaffold components, manage your project structure, and streamline development.

---

### 🚀 Features

- Custom CLI tool – free lets you create controllers, models, and run other useful commands.

- Easy to use – Designed to be lightweight and modular, FreeFrame is perfect for building your own web applications.

- Automatic routing – Route your requests easily to the correct controllers and methods.

- Environment handling – Use .env files for configuration, making it easy to manage different environments.

---

### 🧱 Project Structure
```text
```

### Namespace or Class not found issue resolved
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

### Create a Project
```sh
$ composer create-project freeframe/freeframe my-app --stability=dev
```

### Start Server
```sh
$ php free serve
```