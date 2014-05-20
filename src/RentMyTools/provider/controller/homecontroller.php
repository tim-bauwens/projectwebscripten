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
            $controllers->get('/search', array($this, 'search'));
            return $controllers;

	}
        
    public function overview(Application $app) {
        return $app['twig']->render('home.twig');
	}

    public function search(Application $app) {

        $keyword = $app['request']->get('search');
        $keywordSQL = '%'. $keyword .'%';

        $toolsCount = $app['tools']->getToolsBySearchCount($keywordSQL);
        $pages = ceil($toolsCount[0]['total'] / 5);
        

        $currentPage = $app['request']->get('p');
        if($currentPage != null){
            $itemDetails = $app['tools']->getToolsBySearch($keywordSQL,($currentPage * 5) - 4,$currentPage * 5);
        }
        else{
            $itemDetails = $app['tools']->getToolsBySearch($keywordSQL,1,5);
            $currentPage = 1;
        }
        
        for($i = 0; $i < sizeof($itemDetails); $i++){
            //cut off the description after 120 characters
            $itemDetails[$i]['description'] = substr(strip_tags($itemDetails[$i]['description']),0,120) . '...';
            // get the first image path per item
            $directory = $app['paths']['web']  . '/files/' . $itemDetails[$i]['userID'] . '/' . $itemDetails[$i]['id'] . '/';
            $itemDetails[$i]['imagePath'] = $itemDetails[$i]['userID'] . '/' . $itemDetails[$i]['id'] . '/' . getAllImages($directory)[0];
        }
        return $app['twig']->render('search.twig', array('itemDetails' => $itemDetails, 'currentPage' => $currentPage, 'pages' => $pages, 'keyword' => $keyword));
    }
}