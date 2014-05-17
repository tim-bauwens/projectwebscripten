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
            $pages = ceil($toolsCount['total'] / 10);
            if(isset($_GET['p'])){
                $currentPage = $_GET['p'];
                $itemDetails = $app['tools']->getTools(($currentPage * 10) - 9,$currentPage * 10);
            }
            else{
                $itemDetails = $app['tools']->getTools(1,10);
                $currentPage = 1;
            }
                        // get the first image path per item
            for($i = 0; $i < sizeof($itemDetails); $i++){
                  $directory = $app['paths']['web']  . '/files/' . $itemDetails[$i]['userID'] . '/' . $itemDetails[$i]['id'] . '/';
                  $itemDetails[$i]['imagePath'] = $itemDetails[$i]['userID'] . '/' . $itemDetails[$i]['id'] . '/' . getAllImages($directory)[0];
            }
            return $app['twig']->render('rent/overview.twig', array('itemDetails' => $itemDetails, 'currentPage' => $currentPage, 'pages' => $pages));
    }
        public function detail(Application $app, $itemId) {
            $itemDetail = $app['tools']->find($itemId);
            if (!$itemDetail) {
                    $app->abort(404, 'Item $id does not exist');
            }
            return $app['twig']->render('rent/detail.twig', array('itemDetail' => $itemDetail));
    }
}