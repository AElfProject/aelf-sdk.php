    "ext-tokenizer": "*",
                "php": "^7.0"
            },
            "require-dev": {
                "phpunit/phpunit": "^6.2.4"
            },
            "type": "library",
            "extra": {
                "branch-alias": {
                    "dev-master": "2.0-dev"
                }
            },
            "autoload": {
                "classmap": [
                    "src/"
                ]
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "BSD-3-Clause"
            ],
            "authors": [
                {
                    "name": "Sebastian Bergmann",
                    "email": "sebastian@phpunit.de"
                }
            ],
            "description": "Wrapper around PHP's tokenizer extension.",
            "homepage": "https://github.com/sebastianbergmann/php-token-stream/",
            "keywords": [
                "tokenizer"
            ],
            "time": "2017-08-20T05:47:52+00:00"
        },
        {
            "name": "phpunit/phpunit",
            "version": "5.7.22",
            "source": {
                "type": "git",
                "url": "https://github.com/sebastianbergmann/phpunit.git",
                "reference": "10df877596c9906d4110b5b905313829043f2ada"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/sebastianbergmann/phpunit/zipball/10df877596c9906d4110b5b905313829043f2ada",
                "reference": "10df877596c9906d4110b5b905313829043f2ada",
                "shasum": ""
            },
            "require": {
                "ext-dom": "*",
                "ext-json": "*",
                "ext-libxml": "*",
                "ext-mbstring": "*",
                "ext-xml": "*",
                "myclabs/deep-copy": "~1.3",
                "php": "^5.6 || ^7.0",
                "phpspec/prophecy": "^1.6.2",
                "phpunit/php-code-coverage": "^4.0.4",
                "phpunit/php-file-iterator": "~1.4",
                "phpunit/php-text-template": "~1.2",
                "phpunit/php-timer": "^1.0.6",
                "phpunit/phpunit-mock-objects": "^3.2",
                "sebastian/comparator": "^1.2.4",
                "sebastian/diff": "^1.4.3",
                "sebastian/environment": "^1.3.4 || ^2.0",
                "sebastian/exporter": "~2.0",
                "sebastian/global-state": "^1.1",
                "sebastian/object-enumerator": "~2.0",
                "sebastian/resource-operations": "~1.0",
                "sebastian/version": "~1.0.3|~2.0",
                "symfony/yaml": "~2.1|~3.0"
            },
            "conflict": {
                "phpdocumentor/reflection-docblock": "3.0.2"
            },
            "require-dev": {
                "ext-pdo": "*"
            },
            "suggest": {
                "ext-xdebug": "*",
                "phpunit/php-invoker": "~1.1"
            },
            "bin": [
                "phpunit"
            ],
            "type": "library",
            "extra": {
                "branch-alias": {
                    "dev-master": "5.7.x-dev"
                }
            },
            "autoload": {
                "classmap": [
                    "src/"
                ]
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "BSD-3-Clause"
            ],
            "authors": [
                {
                    "name": "Sebastian Bergmann",
                    "email": "sebastian@phpunit.de",
                    "role": "lead"
                }
            ],
            "description": "The PHP Unit Testing framework.",
            "homepage": "https://phpunit.de/",
            "keywords": [
                "phpunit",
                "testing",
                "xunit"
            ],
            "time": "2017-09-24T07:23:38+00:00"
        },
        {
            "name": "phpunit/phpunit-mock-objects",
            "version": "3.4.4",
            "source": {
                "type": "git",
                "url": "https://git