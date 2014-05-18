<?php

namespace RentMyTools\Provider\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class RentController implements ControllerProviderInterface {
    
    public function connect(Application $app) {


            //@note $app['controllers_factory'] is a factory that returns a new instance of ControllerCollection when used.
            //@see http://silex.sensiolabs.org/doc/organizing_controllers.html
            $controllers = $app['controllers_factory'];
            
            // Bind sub-routes
            $controllers->get('/', array($this, 'overview'));
            $controllers->get('/{itemId}', array($this, 'detail'))->assert('itemId', '\d+');
            
            return $controllers;

    }
        
        public function overview(Application $app) {
            $toolsCount = $app['tools']->getCount();
            $pages = ceil($toolsCount['total'] / 5);
            if(isset($_GET['p'])){
                $currentPage = $_GET['p'];
                $itemDetails = $app['tools']->getTools(($currentPage * 5) - 4,$currentPage * 5);
            }
            else{
                $itemDetails = $app['tools']->getTools(1,5);
                $currentPage = 1;
            }
            
            for($i = 0; $i < sizeof($itemDetails); $i++){
                //cut off the description after 120 characters
                $itemDetails[$i]['description'] = substr(strip_tags($itemDetails[$i]['description']),0,120) . '...';
                // get the first image path per item
                $directory = $app['paths']['web']  . '/files/' . $itemDetails[$i]['userID'] . '/' . $itemDetails[$i]['id'] . '/';
                $itemDetails[$i]['imagePath'] = $itemDetails[$i]['userID'] . '/' . $itemDetails[$i]['id'] . '/' . getAllImages($directory)[0];
            }
            return $app['twig']->render('rent/overview.twig', array('itemDetails' => $itemDetails, 'currentPage' => $currentPage, 'pages' => $pages));
    }
        public function detail(Application $app, $itemId) {
            $itemDetail = $app['tools']->find($itemId);
            if (!$itemDetail) {
                    $app->abort(404, 'Item with id ' . $itemId . ' does not exist');
            }
            //get images
            $directory = $app['paths']['web']  . '/files/' . $itemDetail['userID'] . '/' . $itemDetail['id'] . '/';
            $images = getAllImages($directory);

            return $app['twig']->render('rent/detail.twig', array('itemDetail' => $itemDetail, 'images' => $images));
    }
}