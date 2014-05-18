<?php

namespace RentMyTools\Provider\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class UserController implements ControllerProviderInterface {

	public function connect(Application $app) {
            //@note $app['controllers_factory'] is a factory that returns a new instance of ControllerCollection when used.
            //@see http://silex.sensiolabs.org/doc/organizing_controllers.html
            $controllers = $app['controllers_factory'];

            //add sub-controllers
            $controllers->get('/{username}', array($this, 'profile'))->assert('username', '\w+');
            return $controllers;

	}
        
    public function profile(Application $app, $username) {
        $user = $app['account']->getUserDataByUser($username);
         if (!$user) {
            $app->abort(404, 'user '. $username .' does not exist');
        }
        //get 5 most recent items
        $itemDetails = $app['account']->getNewestByUser($username);
        for($i = 0; $i < sizeof($itemDetails); $i++){
            //cut off the description after 120 characters
            $itemDetails[$i]['description'] = substr(strip_tags($itemDetails[$i]['description']),0,120) . '...';
            // get the first image path per item
            $directory = $app['paths']['web']  . '/files/' . $itemDetails[$i]['userID'] . '/' . $itemDetails[$i]['id'] . '/';
            $itemDetails[$i]['imagePath'] = $itemDetails[$i]['userID'] . '/' . $itemDetails[$i]['id'] . '/' . getAllImages($directory)[0];
        }
        return $app['twig']->render('user/profile.twig', array('user' => $user[0],'itemDetails' => $itemDetails));
	}
}