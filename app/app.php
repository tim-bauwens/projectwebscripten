<?php

// Bootstrap
require __DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php';
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

$app->error(function (\Exception $e, $code) use ($app){
	if ($code == 404) {
		return $app['twig']->render('errors/404.twig', array('error' => $e->getMessage()));
	} else {
		return 'Something went wrong // ' . $e->getMessage();
	}
});

// Mount our ControllerProviders
$app->mount('/', new RentMyTools\Provider\Controller\HomeController());
$app->mount('/about', new RentMyTools\Provider\Controller\AboutController());
$app->mount('/contact', new RentMyTools\Provider\Controller\ContactController());

$app->mount('/rent', new RentMyTools\Provider\Controller\RentController());

$app->mount('/login', new RentMyTools\Provider\Controller\LoginController());
$app->mount('/logout', new RentMyTools\Provider\Controller\LogoutController());
$app->mount('/register', new RentMyTools\Provider\Controller\RegisterController());

$app->mount('/user', new RentMyTools\Provider\Controller\UserController());
$app->mount('/manage', new RentMyTools\Provider\Controller\ManageController());







