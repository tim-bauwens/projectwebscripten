<?php

namespace RentMyTools\Provider\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class HomeController implements ControllerProviderInterface {

	public function connect(Application $app) {
            //@note $app['controllers_factory'] is a factory that returns a new instance of ControllerCollection when used.
            //@see http://silex.sensiolabs.org/doc/organizing_controllers.html
            $controllers = $app['controllers_factory'];

            if($app['session']->get('is_user'))
            {
                $app['user'] = $app['session']->get('user');
            }
            else{
                $app['user'] = "";
            }

            // Bind sub-routes
            $controllers->get('/', array($this, 'overview'));
            return $controllers;

	}
        
        public function overview(Application $app) {
            return $app['twig']->render('home.twig');

            //return $app['twig']->('layout.twig', ('userMessage' => $userMessage));


	}
}