{
    "_readme": [
        "This file locks the dependencies of your project to a known state",
        "Read more about it at https://getcomposer.org/doc/01-basic-usage.md#composer-lock-the-lock-file",
        "This file is @generated automatically"
    ],
    "content-hash": "03e8a05af388b5f30e45a584e3fe7e80",
    "packages": [],
    "packages-dev": [
        {
            "name": "doctrine/instantiator",
            "version": "1.1.0",
            "source": {
                "type": "git",
                "url": "https://github.com/doctrine/instantiator.git",
                "reference": "185b8868aa9bf7159f5f953ed5afb2d7fcdc3bda"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/doctrine/instantiator/zipball/185b8868aa9bf7159f5f953ed5afb2d7fcdc3bda",
                "reference": "185b8868aa9bf7159f5f953ed5afb2d7fcdc3bda",
                "shasum": ""
            },
            "require": {
                "php": "^7.1"
            },
            "require-dev": {
                "athletic/athletic": "~0.1.8",
                "ext-pdo": "*",
                "ext-phar": "*",
                "phpunit/phpunit": "^6.2.3",
                "squizlabs/php_codesniffer": "^3.0.2"
            },
            "type": "library",
            "extra": {
                "branch-alias": {
                    "dev-master": "1.2.x-dev"
                }
            },
            "autoload": {
                "psr-4": {
                    "Doctrine\\Instantiator\\": "src/Doctrine/Instantiator/"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Marco Pivetta",
                    "email": "ocramius@gmail.com",
                    "homepage": "http://ocramius.github.com/"
                }
            ],
            "description": "A small, lightweight utility to instantiate objects in PHP without invoking their constructors",
            "homepage": "https://github.com/doctrine/instantiator",
            "keywords": [
                "constructor",
                "instantiate"
            ],
            "time": "2017-07-22T11:58:36+00:00"
        },
        {
            "name": "myclabs/deep-copy",
            "version": "1.6.1",
            "source": {
                "type": "git",
                "url": "https://github.com/myclabs/DeepCopy.git",
                "reference": "8e6e04167378abf1ddb4d3522d8755c5fd90d102"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/myclabs/DeepCopy/zipball/8e6e04167378abf1ddb4d3522d8755c5fd90d102",
                "reference": "8e6e04167378abf1ddb4d3522d8755c5fd90d102",
                "shasum": ""
            },
            "require": {
                "php": ">=5.4.0"
            },
            "require-dev": {
                "doctrine/collections": "1.*",
                "phpunit/phpunit": "~4.1"
            },
            "type": "library",
            "autoload": {
                "psr-4": {
                    "DeepCopy\\": "src/DeepCopy/"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "description": "Create deep copies (clones) of your objects",
            "homepage": "https://github.com/myclabs/DeepCopy",
            "keywords": [
                "clone",
                "copy",
                "duplicate",
                "object",
                "object graph"
            ],
            "time": "2017-04-12T18:52:22+00:00"
        },
        {
            "name": "nette/bootstrap",
            "version": "v2.4.5",
            "source": {
                "type": "git",
                "url": "https://github.com/nette/bootstrap.git",
                "reference": "804925787764d708a7782ea0d9382a310bb21968"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/nette/bootstrap/zipball/804925787764d708a7782ea0d9382a310bb21968",
                "reference": "804925787764d708a7782ea0d9382a310bb21968",
                "shasum": ""
            },
            "require": {
                "nette/di": "~2.4.7",
                "nette/utils": "~2.4",
                "php": ">=5.6.0"
            },
            "conflict": {
                "nette/nette": "<2.2"
            },
            "require-dev": {
                "latte/latte": "~2.2",
                "nette/application": "~2.3",
                "nette/caching": "~2.3",
                "nette/database": "~2.3",
                "nette/forms": "~2.3",
                "nette/http": "~2.4.0",
                "nette/mail": "~2.3",
                "nette/robot-loader": "^2.4.2 || ^3.0",
                "nette/safe-stream": "~2.2",
                "nette/security": "~2.3",
                "nette/tester": "~2.0",
                "tracy/tracy": "^2.4.1"
            },
            "suggest": {
                "nette/robot-loader": "to use Configurator::createRobotLoader()",
                "tracy/tracy": "to use Configurator::enableTracy()"
            },
            "type": "library",
            "extra": {
                "branch-alias": {
                    "dev-master": "2.4-dev"
                }
            },
            "autoload": {
                "classmap": [
                    "src/"
                ]
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "BSD-3-Clause",
                "GPL-2.0",
                "GPL-3.0"
            ],
            "authors": [
                {
                    "name": "David Grudl",
                    "homepage": "https://davidgrudl.com"
                },
                {
                    "name": "Nette Community",
                    "homepage": "https://nette.org/contributors"
                }
            ],
            "description": "🅱 Nette Bootstrap: the simple way to configure and bootstrap your Nette application.",
            "homepage": "https://nette.org",
            "keywords": [
                "bootstrapping",
                "configurator",
                "nette"
            ],
            "time": "2017-08-20T17:36:59+00:00"
        },
        {
            "name": "nette/caching",
            "version": "v2.5.6",
            "source": {
                "type": "git",
                "url": "https://github.com/nette/caching.git",
                "reference": "1231735b5135ca02bd381b70482c052d2a90bdc9"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/nette/caching/zipball/1231735b5135ca02bd381b70482c052d2a90bdc9",
                "reference": "1231735b5135ca02bd381b70482c052d2a90bdc9",
                "shasum": ""
            },
            "require": {
                "nette/finder": "^2.2 || ~3.0.0",
                "nette/utils": "^2.4 || ~3.0.0",
                "php": ">=5.6.0"
            },
            "conflict": {
                "nette/nette": "<2.2"
            },
            "require-dev": {
                "latte/latte": "^2.4",
                "nette/di": "^2.4 || ~3.0.0",
                "nette/tester": "^2.0",
                "tracy/tracy": "^2.4"
            },
            "suggest": {
                "ext-pdo_sqlite": "to use SQLiteStorage or SQLiteJournal"
            },
            "type": "library",
            "extra": {
                "branch-alias": {
                    "dev-master": "2.5-dev"
                }
            },
            "autoload": {
                "classmap": [
                    "src/"
      