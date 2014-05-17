<?php

namespace RentMyTools\Provider\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class LoginController implements ControllerProviderInterface {

	public function connect(Application $app) {

		//@note $app['controllers_factory'] is a factory that returns a new instance of ControllerCollection when used.
		//@see http://silex.sensiolabs.org/doc/organizing_controllers.html
		$controllers = $app['controllers_factory'];

		//make form
        $app->match('/login', function (Request $request) use ($app) {
            // some default data for when the form is displayed the first time
            $data = array(
                'username' => '',
                'Password: ' => '',
            );

            $form = $app['form.factory']->createBuilder('form')
            ->add('username', 'text', array(
                'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 5)))
            ))
            ->add('password', 'password', array(
                'constraints' => new Assert\NotBlank()))

            ->getForm();

            if ('POST' == $request->getMethod()) {
              $form->bind($request);

                if ($form->isValid()) {
                    $data = $form->getData();

                    // do something with the data
                    $passwords = $app['account']->findPasswByUser($data["username"]);
                    foreach ($passwords as $password) {
                        if(crypt($data["password"] , "Z3plF2E") == $password["password"]){
                          $app['session']->set('is_user', true);
                          $app['session']->set('user', $data["username"]);

                          return $app->redirect('manage');
                        } 
                        else{
                            echo "<p id = \"error\">Wrong Username or Password</p>";
                        }
                    }
                }
            }
            // display the form
            return $app['twig']->render('login.twig', array('form' => $form->createView()));
        });
                
        // Bind sub-routes
		$controllers->get('/', array($this, 'overview'));
		return $controllers;
	}
        
    public function overview(Application $app) {
        return $app['twig']->render('login.twig');
	}
}