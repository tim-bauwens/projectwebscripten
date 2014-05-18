<?php

namespace RentMyTools\Provider\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterController implements ControllerProviderInterface {

	public function connect(Application $app) {

		//@note $app['controllers_factory'] is a factory that returns a new instance of ControllerCollection when used.
		//@see http://silex.sensiolabs.org/doc/organizing_controllers.html
		$controllers = $app['controllers_factory'];

        //make form
        $app->match('/register', function (Request $request) use ($app) {
            // some default data for when the form is displayed the first time
            $data = array(
                'Username' => '',
                'Password: ' => '',
                'Firstname: ' => '',
                'Lastname: ' => '',
                'Email: ' => '',
                'Phonenumber: ' => '',
                'Address: ' => ''
            );

            $form = $app['form.factory']->createBuilder('form')
            ->add('username', 'text', array(
                'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 5)))
            ))
            ->add('firstname', 'text', array(
                'constraints' => new Assert\NotBlank()
            ))
            ->add('lastname', 'text', array(
                'constraints' => new Assert\NotBlank()
            ))
            ->add('password', 'password', array(
                'constraints' => new Assert\NotBlank()
            ))
            ->add('email', 'email', array(
                'constraints' => array(new Assert\NotBlank(),new Assert\Email())
            ))
            ->add('phonenumber', 'text')
            ->add('address', 'text')
            ->getForm();

            if ('POST' == $request->getMethod()) {
                $form->bind($request);

                if ($form->isValid()) {
                    $data = $form->getData();

                    $data["password"] = crypt($data["password"] , "Z3plF2E");
                    $app['account']->insert($data);

                    $app['session']->set('is_user', true);
                    $app['session']->set('user', $data["username"]);
                    //send register mail
                    $message = \Swift_Message::newInstance()
                        ->setSubject('[Rent my tools] Registration succesful!')
                        ->setFrom(array('noreply.rentmytools@gmail.com'))
                        ->setTo(array($data['email']))
                        ->setBody('<html>
                                    <head></head>
                                    <body>
                                    <div>
                                        <p>' . $data['username'] . ', you have succesfully registered at www.rentmytools.com!</p>
                                    </div>
                                    </body>
                                </html>', 'text/html');

                    $app['mailer']->send($message);
                    // redirect 
                    return $app->redirect('register/success');
                }
            }

            // display the form
            return $app['twig']->render('register/overview.twig', array('form' => $form->createView()));
        });


		// Bind sub-routes
		$controllers->get('/', array($this, 'overview'));
        $controllers->get('/success', array($this, 'success'));
		return $controllers;

	}
        
    public function overview(Application $app) {
        return $app['twig']->render('register/overview.twig');
	}
    public function success(Application $app) {
        //send mail
        return $app['twig']->render('register/success.twig');
    }
}