# Lion-Framework
A simple and easy to use PHP framework

[![Latest Stable Version](http://poser.pugx.org/lion-framework/lion-framework/v)](https://packagist.org/packages/lion-framework/lion-framework) [![Total Downloads](http://poser.pugx.org/lion-framework/lion-framework/downloads)](https://packagist.org/packages/lion-framework/lion-framework) [![Latest Unstable Version](http://poser.pugx.org/lion-framework/lion-framework/v/unstable)](https://packagist.org/packages/lion-framework/lion-framework) [![License](http://poser.pugx.org/lion-framework/lion-framework/license)](https://packagist.org/packages/lion-framework/lion-framework) [![PHP Version Require](http://poser.pugx.org/lion-framework/lion-framework/require/php)](https://packagist.org/packages/lion-framework/lion-framework)

## Install
```shell
composer create-project lion-framework/lion-framework
```

```shell
composer install
```

# Lion-Framework the API Backend
Lion-Framework can also serve as an API backend for a JavaScript single page application or a mobile application. For example, you can use Lion-Framework as an API backend for your Vite.js app or Kotlin app. <br>

You can use Lion-Framework to provide authentication and data storage/retrieval for your application, while taking advantage of Lion-Framework services such as emails, databases, and more.

## Usage
Start by running the server, by default it runs on port `4040`.
```shell
php lion serve
```

use another port.
```shell
php lion serve --port=4041
```

## Commands
More information about the use of internal commands. [Lion-Command](https://github.com/Sleon4/Lion-Command)

```shell
php lion serve
php lion new:controller <name-controller>
php lion new:model <name-model>
php lion new:middleware <name-middleware>
php lion new:command <name-command>
php lion new:capsule <name-capsule>
php lion new:test <name-test>
php lion new:rsa
```

## REQUEST AND RESPONSE
Learn more about using request features. [Lion-Request](https://github.com/Sleon4/Lion-Request)

## SECURITY
Learn more about using security features. [Lion-Security](https://github.com/Sleon4/Lion-Security)

## CARBON
The Carbon class inherits from the PHP DateTime class and is installed by default. [nesbot/carbon](https://carbon.nesbot.com/)

### 1. ROUTES AND MIDDLEWARE
Lion-Route has been implemented for route handling. More information at [Lion-Route](https://github.com/Sleon4/Lion-Route). <br>
Middleware is easy to implement. They must have the main class imported into Middleware, which initializes different functions and objects at the Middleware level. <br>
The rule for middleware is simple, in the constructor they must be initialized with the $this->init() function. More information about the use of Middleware in [Lion-Route](https://github.com/Sleon4/Lion-Route). <br>
You can create a middleware with the command.
```php
namespace App\Http\Middleware;

use App\Http\Middleware\Middleware;

class HomeMiddleware extends Middleware {

	public function __construct() {
		$this->init();
	}

	public function example(): void {
		if (!$this->request->user_session) {
			$this->processOutput(
				$this->response->error('Username does not exist.')
			);
		}
	}

}
```

### 2. CONTROLLERS
Controllers are easy to implement. They must have the parent class imported into `Controller`, which initializes different functions and objects at the Controller level. <br>
The rule for Controllers is simple, in the constructor they must be initialized with the `$this->init()` function. <br>
You can create a controller with the command.
```php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class HomeController extends Controller {

	public function __construct() {
		$this->init();
	}

	public function index() {
		return $this->response->warning('Page not found. [index]');
	}

}
```

### 3. MODELS
The models are easy to implement. They must have the main class imported into `Model`, which initializes various functions and objects at the model level. <br>
The rule for models is simple, in the constructor they must be initialized with the `$this->init()` function. <br>
You can create a model with the command.
```php
namespace App\Models;

use App\Models\Model;

class HomeModel extends Model {

	public function __construct() {
		$this->init();
	}

}
```

Note that when you want to implement methods that implement processes with databases, the `LionSql\Drivers\MySQLDriver` class must be imported for their respective operation. more information on [Lion-SQL](https://github.com/Sleon4/Lion-SQL). <br>
Note that at the framework level Lion-SQL is already installed and implemented, the variables are located in the `.env` file, follow the import instructions for their use.

## Credits
[PHRoute](https://github.com/mrjgreen/phroute) <br>
[PHP dotenv](https://github.com/vlucas/phpdotenv) <br>
[Valitron](https://github.com/vlucas/valitron) <br>
[PHPMailer](https://github.com/PHPMailer/PHPMailer) <br>
[PHP-JWT](https://github.com/firebase/php-jwt) <br>
[Symfony-Console](https://github.com/symfony/console) <br>
[Carbon](https://carbon.nesbot.com/)

## Other libraries
[Lion-SQL](https://github.com/Sleon4/Lion-SQL) <br>
[Lion-Security](https://github.com/Sleon4/Lion-Security) <br>
[Lion-Route](https://github.com/Sleon4/Lion-Route) <br>
[Lion-Mailer](https://github.com/Sleon4/Lion-Mailer) <br>
[Lion-Files](https://github.com/Sleon4/Lion-Files) <br>
[Lion-Command](https://github.com/Sleon4/Lion-Command) <br>
[Lion-Request](https://github.com/Sleon4/Lion-Request)

## License
Copyright © 2022 [MIT License](https://github.com/Sleon4/Lion-Framework/blob/main/LICENSE)