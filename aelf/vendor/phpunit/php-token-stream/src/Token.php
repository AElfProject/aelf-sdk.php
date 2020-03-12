"GuzzleHttp\\Psr7\\": "src/"
                },
                "files": [
                    "src/functions_include.php"
                ]
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Michael Dowling",
                    "email": "mtdowling@gmail.com",
                    "homepage": "https://github.com/mtdowling"
                },
                {
                    "name": "Tobias Schultze",
                    "homepage": "https://github.com/Tobion"
                }
            ],
            "description": "PSR-7 message implementation that also provides common utility methods",
            "keywords": [
                "http",
                "message",
                "psr-7",
                "request",
                "response",
                "stream",
                "uri",
                "url"
            ],
            "time": "2018-12-04T20:46:45+00:00"
        },
        {
            "name": "myclabs/deep-copy",
            "version": "1.8.1",
            "source": {
                "type": "git",
                "url": "https://github.com/myclabs/DeepCopy.git",
                "reference": "3e01bdad3e18354c3dce54466b7fbe33a9f9f7f8"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/myclabs/DeepCopy/zipball/3e01bdad3e18354c3dce54466b7fbe33a9f9f7f8",
                "reference": "3e01bdad3e18354c3dce54466b7fbe33a9f9f7f8",
                "shasum": ""
            },
            "require": {
                "php": "^7.1"
            },
            "replace": {
                "myclabs/deep-copy": "self.version"
            },
            "require-dev": {
                "doctrine/collections": "^1.0",
                "doctrine/common": "^2.6",
                "phpunit/phpunit": "^7.1"
            },
            "type": "library",
            "autoload": {
                "psr-4": {
                    "DeepCopy\\": "src/DeepCopy/"
                },
                "files": [
                    "src/DeepCopy/deep_copy.php"
                ]
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "description": "Create deep copies (clones) of your objects",
            "keywords": [
                "clone",
                "copy",
                "duplicate",
                "object",
                "object graph"
            ],
            "time": "2018-06-11T23:09:50+00:00"
        },
        {
            "name": "phar-io/manifest",
            "version": "1.0.3",
            "source": {
                "type": "git",
                "url": "https://github.com/phar-io/manifest.git",
                "reference": "7761fcacf03b4d4f16e7ccb606d4879ca431fcf4"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/phar-io/manifest/zipball/7761fcacf03b4d4f16e7ccb606d4879ca431fcf4",
                "reference": "7761fcacf03b4d4f16e7ccb606d4879ca431fcf4",
                "shasum": ""
            },
            "require": {
                "ext-dom": "*",
                "ext-phar": "*",
                "phar-io/version": "^2.0",
                "php": "^5.6 || ^7.0"
            },
            "type": "library",
            "extra": {
                "branch-alias": {
                    "dev-master": "1.0.x-dev"
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
                    "name": "Arne Blankerts",
                    "email": "arne@blankerts.de",
                    "role": "Developer"
                },
                {
                    "name": "Sebastian Heuer",
                    "email": "sebastian@phpeople.de",
                    "role": "Developer"
                },
                {
                    "name": "Sebastian Bergmann",
                    "email": "sebastian@phpunit.de",
                    "role": "Developer"
                }
            ],
            "description": "Component for reading phar.io manifest information from a PHP Archive (PHAR)",
            "time": "2018-07-08T19:23:20+00:00"
        },
        {
            "name": "phar-io/version",
            "version": "2.0.1",
            "source": {
                "type": "git",
                "url": "https://github.com/phar-io/version.git",
                "reference": "45a2ec53a73c70ce41d55cedef9063630abaf1b6"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/phar-io/version/zipball/45a2ec53a73c70ce41d55cedef9063630abaf1b6",
                "reference": "45a2ec53a73c70ce41d55cedef9063630abaf1b6",
                "shasum": ""
            },
            "require": {
                "php": "^5.6 || ^7.0"
            },
            "type": "library",
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
                    "name": "Arne Blankerts",
                    "email": "arne@blankerts.de",
                    "role": "Developer"
                },
                {
                    "name": "Sebastian Heuer",
                    "email": "sebastian@phpeople.de",
                    "role": "Developer"
                },
                {
                    "name": "Sebastian Bergmann",
                    "email": "sebastian@phpunit.de",
                    "role": "Developer"
                }
            ],
            "description": "Library for handling version information and constraints",
            "time": "2018-07-08T19:19:57+00:00"
        },
        {
            "name": "php-coveralls/php-coveralls",
            "version": "v2.1.0",
            "source": {
                "type": "git",
                "url": "https://github.com/php-coveralls/php-coveralls.git",
                "reference": "3b00c229726f892bfdadeaf01ea430ffd04a939d"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/php-coveralls/php-coveralls/zipball/3b00c229726f892bfdadeaf01ea430ffd04a939d",
                "reference": "3b00c229726f892bfdadeaf01ea430ffd04a939d",
                "shasum": ""
            },
            "require": {
                "ext-json": "*",
                "ext-simplexml": "*",
                "guzzlehttp/guzzle": "^6.0",
                "php": "^5.5 || ^7.0",
                "psr/log": "^1.0",
                "symfony/config": "^2.1 || ^3.0 || ^4.0",
                "symfony/console": "^2.1 || ^3.0 || ^4.0",
                "symfony/stopwatch": "^2.0 || ^3.0 || ^4.0",
                "symfony/yaml": "^2.0 || ^3.0 || ^4.0"
            },
            "require-dev": {
                "phpunit/phpunit": "^4.8.35 || ^5.4.3 || ^6.0"
            },
            "suggest": {
                "symfony/http-kernel": "Allows Symfony integration"
            },
            "bin": [
                "bin/php-coveralls"
            ],
            "type": "library",
            "extra": {
                "branch-alias": {
                    "dev-master": "2.1-dev"
                }
            },
            "autoload": {
                "psr-4": {
                    "PhpCoveralls\\": "src/"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Kitamura Satoshi",
                    "email": "with.no.parachute@gmail.com",
                    "homepage": "https://www.facebook.com/satooshi.jp",
                    "role": "Original creator"
                },
                {
                    "name": "Takashi Matsuo",
                    "email": "tmatsuo@google.com"
                },
                {
                    "name": "Google Inc"
                },
                {
                    "name": "Dariusz Ruminski",
                    "email": "dariusz.ruminski@gmail.com",
                    "homepage": "https://github.com/keradus"
                },
                {
                    "name": "Contributors",
                    "homepage": "https://github.com/php-coveralls/php-coveralls/graphs/contributors"
                }
            ],
            "description": "PHP client library for Coveralls API",
            "homepage": "https://github.com/php-coveralls/php-coveralls",
            "keywords": [
                "ci",
                "coverage",
                "github",
                "test"
            ],
            "time": "2018-05-22T23:11:08+00:00"
        },
        {
            "name": "phpdocumentor/reflection-common",
            "version": "1.0.1",
            "source": {
                "type": "git",
                "url": "https://github.com/phpDocumentor/ReflectionCommon.git",
                "reference": "21bdeb5f65d7ebf9f43b1b25d404f87deab5bfb6"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/phpDocumentor/ReflectionCommon/zipball/21bdeb5f65d7ebf9f43b1b25d404f87deab5bfb6",
                "reference": "21bdeb5f65d7ebf9f43b1b25d404f87deab5bfb6",
                "shasum": ""
            },
            "require": {
                "php": ">=5.5"
            },
            "require-dev": {
                "phpunit/phpunit": "^4.6"
            },
            "type": "library",
            "extra": {
                "branch-alias": {
                    "dev-master": "1.0.x-dev"
                }
            },
            "autoload": {
                "psr-4": {
                    "phpDocumentor\\Reflection\\": [
                        "src"
                    ]
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Jaap van Otterdijk",
                    "email": "opensource@ijaap.nl"
                }
            ],
            "description": "Common reflection classes used by phpdocumentor to reflect the code structure",
            "homepage": "http://www.phpdoc.org",
            "keywords": [
                "FQSEN",
                "phpDocumentor",
                "phpdoc",
                "reflection",
                "static analysis"
            ],
            "time": "2017-09-11T18:02:19+00:00"
        },
        {
            "name": "phpdocumentor/reflection-docblock",
            "version": "4.3.0",
            "source": {
                "type": "git",
                "url": "https://github.com/phpDocumentor/ReflectionDocBlock.git",
                "reference": "94fd0001232e47129dd3504189fa1c7225010d08"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/phpDocumentor/ReflectionDocBlock/zipball/94fd0001232e47129dd3504189fa1c7225010d08",
                "reference": "94fd0001232e47129dd3504189fa1c7225010d08",
                "shasum": ""
            },
            "require": {
                "php": "^7.0",
                "phpdocumentor/reflection-common": "^1.0.0",
                "phpdocumentor/type-resolver": "^0.4.0",
                "webmozart/assert": "^1.0"
            },
            "require-dev": {
                "doctrine/instantiator": "~1.0.5",
                "mockery/mockery": "^1.0",
                "phpunit/phpunit": "^6.4"
            },
            "type": "library",
            "extra": {
                "branch-alias": {
                    "dev-master": "4.x-dev"
                }
            },
            "autoload": {
                "psr-4": {
                    "phpDocumentor\\Reflection\\": [
                        "src/"
                    ]
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Mike van Riel",
                    "email": "me@mikevanriel.com"
                }
            ],
            "description": "With this component, a library can provide support for annotations via DocBlocks or otherwise retrieve information that is embedded in a DocBlock.",
            "time": "2017-11-30T07:14:17+00:00"
        },
        {
            "name": "phpdocumentor/type-resolver",
            "version": "0.4.0",
            "source": {
                "type": "git",
                "url": "https://github.com/phpDocumentor/TypeResolver.git",
                "reference": "9c977708995954784726e25d0cd1dddf4e65b0f7"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/phpDocumentor/TypeResolver/zipball/9c977708995954784726e25d0cd1dddf4e65b0f7",
                "reference": "9c977708995954784726e25d0cd1dddf4e65b0f7",
                "shasum": ""
            },
            "require": {
                "php": "^5.5 || ^7.0",
                "phpdocumentor/reflection-common": "^1.0"
            },
            "require-dev": {
                "mockery/mockery": "^0.9.4",
                "phpunit/phpunit": "^5.2||^4.8.24"
            },
            "type": "library",
            "extra": {
                "branch-alias": {
                    "dev-master": "1.0.x-dev"
                }
            },
            "autoload": {
                "psr-4": {
                    "phpDocumentor\\Reflection\\": [
                        "src/"
                    ]
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Mike van Riel",
                    "email": "me@mikevanriel.com"
                }
            ],
            "time": "2017-07-14T14:27:02+00:00"
        },
        {
            "name": "phpspec/prophecy",
            "version": "1.8.0",
            "source": {
                "type": "git",
                "url": "https://github.com/phpspec/prophecy.git",
                "reference": "4ba436b55987b4bf311cb7c6ba82aa528aac0a06"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/phpspec/prophecy/zipball/4ba436b55987b4bf311cb7c6ba82aa528aac0a06",
                "reference": "4ba436b55987b4bf311cb7c6ba82aa528aac0a06",
                "shasum": ""
            },
            "require": {
                "doctrine/instantiator": "^1.0.2",
                "php": "^5.3|^7.0",
                "phpdocumentor/reflection-docblock": "^2.0|^3.0.2|^4.0",
                "sebastian/comparator": "^1.1|^2.0|^3.0",
                "sebastian/recursion-context": "^1.0|^2.0|^3.0"
            },
            "require-dev": {
                "phpspec/phpspec": "^2.5|^3.2",
                "phpunit/phpunit": "^4.8.35 || ^5.7 || ^6.5 || ^7.1"
            },
            "type": "library",
            "extra": {
                "branch-alias": {
                    "dev-master": "1.8.x-dev"
                }
            },
            "autoload": {
                "psr-0": {
                    "Prophecy\\": "src/"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Konstantin Kudryashov",
                    "email": "ever.zet@gmail.com",
                    "homepage": "http://everzet.com"
                },
                {
                    "name": "Marcello Duarte",
                    "email": "marcello.duarte@gmail.com"
                }
            ],
            "description": "Highly opinionated mocking framework for PHP 5.3+",
            "homepage": "https://github.com/phpspec/prophecy",
            "keywords": [
                "Double",
                "Dummy",
                "fake",
                "mock",
                "spy",
                "stub"
            ],
            "time": "2018-08-05T17:53:17+00:00"
        },
        {
            "name": "phpunit/php-code-coverage",
            "version": "6.1.4",
            "source": {
                "type": "git",
                "url": "https://github.com/sebastianbergmann/php-code-coverage.git",
                "reference": "807e6013b00af69b6c5d9ceb4282d0393dbb9d8d"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/sebastianbergmann/php-code-coverage/zipball/807e6013b00af69b6c5d9ceb4282d0393dbb9d8d",
                "reference": "807e6013b00af69b6c5d9ceb4282d0393dbb9d8d",
                "shasum": ""
            },
            "require": {
                "ext-dom": "*",
                "ext-xmlwriter": "*",
                "php": "^7.1",
                "phpunit/php-file-iterator": "^2.0",
                "phpunit/php-text-template": "^1.2.1",
                "phpunit/php-token-stream": "^3.0",
                "sebastian/code-unit-reverse-lookup": "^1.0.1",
                "sebastian/environment": "^3.1 || ^4.0",
                "sebastian/version": "^2.0.1",
                "theseer/tokenizer": "^1.1"
            },
            "require-dev": {
                "phpunit/phpunit": "^7.0"
            },
            "suggest": {
                "ext-xdebug": "^2.6.0"
            },
            "type": "library",
            "extra": {
                "branch-alias": {
                    "dev-master": "6.1-dev"
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
            "description": "Library that provides collection, processing, and rendering functionality for PHP code coverage information.",
            "homepage": "https://github.com/sebastianbergmann/php-code-coverage",
            "keywords": [
                "coverage",
                "testing",
                "xunit"
            ],
            "time": "2018-10-31T16:06:48+00:00"
        },
        {
            "name": "phpunit/php-file-iterator",
            "version": "2.0.2",
            "source": {
                "type": "git",
                "url": "https://github.com/sebastianbergmann/php-file-iterator.git",
                "reference": "050bedf145a257b1ff02746c31894800e5122946"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/sebastianbergmann/php-file-iterator/zipball/050bedf145a257b1ff02746c31894800e5122946",
                "reference": "050bedf145a257b1ff02746c31894800e5122946",
                "shasum": ""
            },
            "require": {
                "php": "^7.1"
            },
            "require-dev": {
                "phpunit/phpunit": "^7.1"
            },
            "type": "library",
            "extra": {
                "branch-alias": {
                    "dev-master": "2.0.x-dev"
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
            "description": "FilterIterator implementation that filters files based on a list of suffixes.",
            "homepage": "https://github.com/sebastianbergmann/php-file-iterator/",
            "keywords": [
                "filesystem",
                "iterator"
            ],
            "time": "2018-09-13T20:33:42+00:00"
        },
        {
            "name": "phpunit/php-text-template",
            "version": "1.2.1",
            "source": {
                "type": "git",
                "url": "https://github.com/sebastianbergmann/php-text-template.git",
                "reference": "31f8b717e51d9a2afca6c9f046f5d69fc27c8686"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/sebastianbergmann/php-text-template/zipball/31f8b717e51d9a2afca6c9f046f5d69fc27c8686",
                "reference": "31f8b717e51d9a2afca6c9f046f5d69fc27c8686",
                "shasum": ""
            },
            "require": {
                "php": ">=5.3.3"
            },
            "type": "library",
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
            "description": "Simple template engine.",
            "homepage": "https://github.com/sebastianbergmann/php-text-template/",
            "keywords": [
                "template"
            ],
            "time": "2015-06-21T13:50:34+00:00"
        },
        {
            "name": "phpunit/php-timer",
            "version": "2.0.0",
            "source": {
                "type": "git",
                "url": "https://github.com/sebastianbergmann/php-timer.git",
                "reference": "8b8454ea6958c3dee38453d3bd571e023108c91f"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/sebastianbergmann/php-timer/zipball/8b8454ea6958c3dee38453d3bd571e023108c91f",
                "reference": "8b8454ea6958c3dee38453d3bd571e023108c91f",
                "shasum": ""
            },
            "require": {
                "php": "^7.1"
            },
            "require-dev": {
                "phpunit/phpunit": "^7.0"
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
                    "email": "sebastian@phpunit.de",
                    "role": "lead"
                }
            ],
            "description": "Utility class for timing",
            "homepage": "https://github.com/sebastianbergmann/php-timer/",
            "keywords": [
                "timer"
            ],
            "time": "2018-02-01T13:07:23+00:00"
        },
        {
            "name": "phpunit/php-token-stream",
            "version": "3.0.1",
            "source": {
                "type": "git",
                "url": "https://github.com/sebastianbergmann/php-token-stream.git",
                "reference": "c99e3be9d3e85f60646f152f9002d46ed7770d18"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/sebastianbergmann/php-token-stream/zipball/c99e3be9d3e85f60646f152f9002d46ed7770d18",
                "reference": "c99e3be9d3e85f60646f152f9002d46ed7770d18",
                "shasum": ""
            },
            "require": {
                "ext-tokenizer": "*",
                "php": "^7.1"
            },
            "require-dev": {
                "phpunit/phpunit": "^7.0"
            },
            "type": "library",
            "extra": {
                "branch-alias": {
                    "dev-master": "3.0-dev"
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
            "time": "2018-10-30T05:52:18+00:00"
        },
        {
            "name": "phpunit/phpunit",
            "version": "7.5.2",
            "source": {
                "type": "git",
                "url": "https://github.com/sebastianbergmann/phpunit.git",
                "reference": "7c89093bd00f7d5ddf0ab81dee04f801416b4944"
            },
 