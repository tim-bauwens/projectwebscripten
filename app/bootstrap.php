<?php

// Require Composer Autoloader
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

// Create new Silex App
$app = new Silex\Application();

// App Configuration
$app['debug'] = true;

// Use Twig — @note: Be sure to install Twig via Composer first!
$app->register(new Silex\Provider\TwigServiceProvider(), array(
	'twig.path' => __DIR__ .  DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'views'
));

// Use Repository Service Provider — @note: Be sure to install RSP via Composer first!
$app->register(new Knp\Provider\RepositoryServiceProvider(), array(
	'repository.repositories' => array(
		'account' => 'RentMyTools\\Repository\\AccountRepository',
        'manage' => 'RentMyTools\\Repository\\ManageRepository',
        'tools' => 'RentMyTools\\Repository\\ToolsRepository'
	)
));

//register database
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_mysql',
        'host' => 'localhost',
        'dbname' => 'rentmytoolsDB',
        'user' => 'root',
        'password' => 'Azerty123',
        'charset' => 'utf8',
        'driverOptions' => array(
            1002 => 'SET NAMES utf8'
        )
    )
));

$app['paths'] = array(
    'web' => __DIR__ . '/../web'
);

$app->register(new Silex\Provider\SessionServiceProvider());

//register formServiceProvider
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'translator.messages' => array(),
));

$app->register(new Silex\Provider\SwiftmailerServiceProvider(), array(
    'swiftmailer.options' => array(
        'host' => 'smtp.gmail.com',
        'port' => '465',
        'username' => 'noreply.rentmytools@gmail.com',
        'password' => 'ditishetwachtwoord',
        'encryption' => 'ssl',
        'auth_mode' => null
    )
));
