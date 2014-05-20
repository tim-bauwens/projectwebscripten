<?php

namespace RentMyTools\Provider\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class ContactController implements ControllerProviderInterface {

    public function connect(Application $app) {

        //@note $app['controllers_factory'] is a factory that returns a new instance of ControllerCollection when used.
        //@see http://silex.sensiolabs.org/doc/organizing_controllers.html
        $controllers = $app['controllers_factory'];

                //make form
                $app->match('/contact', function (Request $request) use ($app) {
                    //some default data for when the form is displayed the first time
                    $data = array(
                        'Name' => $app['session']->get('user'),
                        'Email: ' => '',
                        'Message: ' => '', 
                    );

                    $form = $app['form.factory']->createBuilder('form',$data)
                    ->add('Name', 'text', array(
                        'constraints' => new Assert\NotBlank()
                    ))
                    ->add('Email', 'email', array(
                        'constraints' => array(new Assert\NotBlank(),new Assert\Email())
                    ))
                    ->add('Message', 'textarea', array(
                        'constraints' => new Assert\NotBlank()
                    ))

                    ->getForm();

                    if ('POST' == $request->getMethod()) {
                        $form->bind($request);

                        if ($form->isValid()) {
                            $data = $form->getData();

                            $message = \Swift_Message::newInstance()
                                ->setSubject('[Rent my Tools] Contact')
                                ->setFrom(array('noreply.rentmytools@gmail.com'))
                                ->setTo(array('noreply.rentmytools@gmail.com'))
                                ->setBody('<html>
                                            <head></head>
                                            <body>
                                            <div>
                                                <p>Name: ' . $data['Name'] . '</p>

                                                <p>Email: ' . $data['email'] . '</p>
                                            </div>
                                            <div>
                                                <p>' . $data['Message'] . '</p>
                                            </div>
                                            </body>
                                        </html>', 'text/html');

                            $app['mailer']->send($message);

                            // redirect somewhere
                            return $app->redirect('contact/success');
                        }
                    }

                    // display the form
                    return $app['twig']->render('contact/overview.twig', array('form' => $form->createView()));
                });


        // Bind sub-routes
        $controllers->get('/', array($this, 'overview'));
        $controllers->get('/success', array($this, 'success'));
        return $controllers;

    }
        
    public function overview(Application $app) {
        return $app['twig']->render('contact/overview.twig');
    }
    public function success(Application $app) {
        return $app['twig']->render('contact/success.twig');
    }
}