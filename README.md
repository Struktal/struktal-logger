# Struktal-Logger

This is a PHP library to write log messages to a file

## Installation

To install this library, include it in your project using Composer:

```bash
composer require struktal/struktal-logger
```

## Usage

Before you can use this library, you need to customize a few parameters.
You can do this in the startup of your application:

```php
\struktal\Logger\Logger::setLogDirectory("/path/to/logs/");
\struktal\Logger\Logger::setMinLogLevel(\struktal\Logger\LogLevel::TRACE);
```

You can also set custom, additional log handlers if you want to log to a database or send error logs via email:

```php
\struktal\Logger\Logger::addCustomLogHandler(
    \struktal\Logger\LogLevel::ERROR,
    function(string $formattedMessage, string $serializedMessage, mixed $originalMessage) {
        // Custom log handler logic here
    }
);
```

Then, in your code, you can instantiate the logger by using

```php
$logger = new \struktal\Logger\Logger("custom-tag");
```

and use it to log messages with different log levels:

```php
$logger->trace("This is a trace message");
$logger->debug("This is a debug message");
$logger->info("This is an info message");
$logger->warn("This is a warning message");
$logger->error("This is an error message");
$logger->fatal("This is a fatal message");
```

## License

This software is licensed under the MIT license.
See the [LICENSE](LICENSE) file for more information.
