<?php

namespace RentMyTools\Provider\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class AboutController implements ControllerProviderInterface {

	public function connect(Application $app) {

            //@note $app['controllers_factory'] is a factory that returns a new instance of ControllerCollection when used.
            //@see http://silex.sensiolabs.org/doc/organizing_controllers.html
            $controllers = $app['controllers_factory'];
            
            // Bind sub-routes
            $controllers->get('/', array($this, 'overview'));
            return $controllers;

	}
        public function overview(Application $app) {
            return $app['twig']->render('about/overview.twig');
	}
}