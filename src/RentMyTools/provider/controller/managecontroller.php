<?php

namespace RentMyTools\Provider\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

include 'functions.php';

class ManageController implements ControllerProviderInterface {
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function connect(Application $app) {
            //@note $app['controllers_factory'] is a factory that returns a new instance of ControllerCollection when used.
            //@see http://silex.sensiolabs.org/doc/organizing_controllers.html
            $controllers = $app['controllers_factory'];

            //add sub-controllers
            $controllers->get('/', array($this, 'overview'))
                        ->before(array($this, 'checkLogin'));
            $controllers->get('/delete', array($this, 'delete'))
                        ->before(array($this, 'checkLogin'));
            $controllers->match('/update', array($this, 'update'))
                        ->method('GET|POST')
                        ->before(array($this, 'checkLogin'));
            $controllers->match('/add', array($this, 'add'))
                        ->method('GET|POST')
                        ->before(array($this, 'checkLogin'));
            return $controllers;
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////

      public function overview(Application $app){
            $user = $app['manage']->findId($app['session']->get('user'));
            $toolsCount = $app['manage']->getCountByUser($user['id']);
            $pages = ceil($toolsCount['total'] / 5);
            $currentPage = $app['request']->get('p');
            if($currentPage != null){
                $currentPage = $_GET['p'];
                $itemDetails = $app['manage']->getToolsByUser($user['id'], (($currentPage * 5) - 4), ($currentPage * 5));
            }
            else{
                $itemDetails = $app['manage']->getToolsByUser($user['id'], 1,5);
                $currentPage = 1;
            } 
            // get the first image path per item
            for($i = 0; $i < sizeof($itemDetails); $i++){
                  //cut off the description after 120 characters
                  $itemDetails[$i]['description'] = substr(strip_tags($itemDetails[$i]['description']),0,120) . '...';
                  //find the first image
                  $directory = $app['paths']['web']  . '/files/' . $itemDetails[$i]['userID'] . '/' . $itemDetails[$i]['id'] . '/';
                  $itemDetails[$i]['imagePath'] = $itemDetails[$i]['userID'] . '/' . $itemDetails[$i]['id'] . '/' . getAllImages($directory)[0];
            }
            return $app['twig']->render('manage/overview.twig', array('itemDetails' => $itemDetails, 'currentPage' => $currentPage, 'pages' => $pages));
      }
      public function delete(Application $app) {
            $id = $app['request']->get('id');
            $idArray = array(
                "id" => $id
            );

            $userId = $app['manage']->findUserIdByItem($id);
            $username = $app['manage']->getUser($userId);

            $directory = $app['paths']['web']  . '/files/' . $userId['userid']  . '/' . $id;
            //delete the images
            $allFiles = glob($directory  . '/*.*');

            //user can only delete his own items
            if($username['username'] == ($app['session']->get('user')))
            {
                  // Delete the blogpost
                  $app['manage']->deleteItem($idArray);

                  for($i = 0; $i < sizeof($allFiles); $i++){
                        unlink($allFiles[$i]);
                  }
                  //delete the images folder
                  rmdir($directory);
            }
            return $app->redirect('../manage');
      }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
      public function update(Application $app) {
            $id = $app['request']->get('id');
            $data = $app['manage']->find($id);

            $data['startdate'] = new \dateTime($data['startdate']);
            $data['enddate']= new \dateTime($data['enddate']);

            $userId = $app['manage']->findId($app['session']->get('user'));

            $directory = $app['paths']['web']  . '/files/' . $userId['id'] . '/' . $id . '/';
            $images = getAllImages($directory);
            //build form
            $builder = $app['form.factory'];
            $updateform = $builder->createNamed('updateform', 'form', $data);
            $updateform->add('title', 'text', array(
                  'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 5)))
            ));
            $updateform->add('description', 'textarea', array(
                  'constraints' => array(new Assert\NotBlank())
            ));
            $updateform->add('dayprice', 'text', array(
                  'constraints' => array(new Assert\NotBlank())
            ));
            $updateform->add('startdate', 'date', array(
                  'constraints' => array(new Assert\NotBlank())
            ));
            $updateform->add('enddate', 'date', array(
                  'constraints' => array(new Assert\NotBlank())
            ));
            
            $updateform->add('images', 'file', array(
                  'constraints' => array(new Assert\NotBlank()),
                  'attr' => array(
                              'accept' => 'image/*',
                              'multiple' => 'multiple'
                              )
            ));
            //add any images there are to the form.
            $imageViews = array();
            for($i = 0; $i < sizeof($images); $i++){
                  $updateform->add('image' . $i, 'checkbox', array(
                      'label' => $images[$i]
                  ));
                  $imageViews['image'.$i] = array(
                        'view' => $updateform->get('image'.$i)->createView(),
                        'imagepath' => $userId['id'] . '/' . $id .'/'. $images[$i]
                        );
            }
            // Form was submitted: process it
            if ('POST' == $app['request']->getMethod()) {
                  $updateform->bind($app['request']);

                  $id = $app['request']->get('id');
                  //user can only update his own items
                  $userId = $app['manage']->findUserIdByItem($id);
                  $username = $app['manage']->getUser($userId);
                  if($username['username'] == ($app['session']->get('user')))
                  {
                        // Form is valid
                        if ($updateform->isValid()) {
                              // Extract data from form
                              $data = $updateform->getData();
                              date_default_timezone_set('Europe/Brussels');
                              // Inject extra fields into data
                              $data['dateAdded'] = date('Y/m/d h-i-s', time());
                              $userId = $app['manage']->findId($app['session']->get('user'));
                              $data['userId'] = $userId['id'];
                              $data['dayprice'] = (double)$data['dayprice'];
                              $data['startdate'] = $data['startdate']->format('Y/m/d');
                              $data['enddate'] = $data['enddate']->format('Y/m/d');
                              unset($data['images']);
                              //delete checked images
                              for($i = 0; $i< sizeof($images); $i++){
                                    //delete $data['image'.$i] if true
                                    if($data['image'.$i] == true){
                                          unlink($directory . $images[$i]);
                                    }
                                    unset($data['image'.$i]);
                              }
                              unset($data['username']);
                              // Update data in DB
                              $app['manage']->updateItem($data,array('id' => $id));
                              //upload image
                              $files = $app['request']->files->get($updateform->getName());
                              if($files['images'][0] !== null){
                                    for($i = 0; $i < sizeof($files['images']); $i++) {
                                          $filename = $data['dateAdded'] . '-' .($files['images'][$i]->getClientOriginalName());
                                          $files['images'][$i]->move( $app['paths']['web']  . '/files/' . $data['userId'] . '/' . $id , $filename);
                                    }
                              }
                        }
                  }
                  // Redirect to overview
                  return $app->redirect('../manage');
            }
            return $app['twig']->render('manage/update.twig', array(
                  'updateform' => $updateform->createView(),
                  'id' => $id,
                  'imageViews' => $imageViews
            ));
      }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
      public function add(Application $app) {
            // Build the form
            $builder = $app['form.factory'];
                        $addform = $builder->createNamed('addform', 'form');
                        $addform->add('title', 'text', array(
                              'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 5)))
                        ));
                        $addform->add('description', 'textarea', array(
                              'constraints' => array(new Assert\NotBlank())
                        ));
                        $addform->add('dayprice', 'text', array(
                              'constraints' => array(new Assert\NotBlank())
                        ));
                        $addform->add('startdate', 'date', array(
                              'constraints' => array(new Assert\NotBlank())
                        ));
                        $addform->add('enddate', 'date', array(
                              'constraints' => array(new Assert\NotBlank())
                        ));
                        $addform->add('images', 'file', array(
                              'constraints' => array(new Assert\NotBlank()),
                              'attr' => array(
                                          'accept' => 'image/*',
                                          'multiple' => 'multiple'
                                          )
                        ));

            // Form was submitted: process it
            if ('POST' == $app['request']->getMethod()) {
                  $addform->bind($app['request']);
                  // Form is valid
                  if ($addform->isValid()) {
                        // Extract data from form
                        $data = $addform->getData();
                        date_default_timezone_set('Europe/Brussels');
                        // Inject extra fields into data
                        $data['dateAdded'] = date('Y/m/d h-i-s', time());
                        $id = $app['manage']->findId($app['session']->get('user'));
                        $data['userId'] = $id['id'];
                        $data['rented'] = 0;
                        $data['startdate'] = $data['startdate']->format('Y/m/d');
                        $data['enddate'] = $data['enddate']->format('Y/m/d');
                        $data['dayprice'] = (double)$data['dayprice'];
                        unset($data['images']);
                        //Insert data in DB
                        $app['manage']->insertItem($data);
                        $insertId = $app['db']->lastInsertId();
                        //upload image
                        $files = $app['request']->files->get($addform->getName());
                        for($i = 0; $i < sizeof($files['images']); $i++) {
                              $filename = $data['dateAdded'] . '-' .($files['images'][$i]->getClientOriginalName());
                              $files['images'][$i]->move( $app['paths']['web']  . '/files/' . $data['userId'] . '/' . $insertId , $filename);
                        }
                        // Redirect to overview
                        return $app->redirect('../manage');
                  }

            }

            // Render the template with the form
            return $app['twig']->render('manage/add.twig', array(
                  'addform' => $addform->createView()
            ));
      }

//////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
      public function checkLogin(\Symfony\Component\HttpFoundation\Request $request, Application $app) {
            if (!$app['session']->get('user')) {
                 return $app->redirect('../login');
            }
      }

}