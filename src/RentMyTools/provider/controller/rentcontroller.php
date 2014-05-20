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
            $controllers->match('/', array($this, 'overview'))
                        ->before(array($this, 'forceFilter'))
                        ->method('GET|POST');
            $controllers->get('/{itemId}', array($this, 'detail'))->assert('itemId', '\d+');
            
            return $controllers;

    }
        
        public function overview(Application $app) {
            //get the current page number
            $currentPage = $app['request']->get('p');
            //make filter form
            $filterform = $builder = $app['form.factory'];
            $filterform = $builder->createNamed('filterform', 'form', $this->getFilter($app));
            //town
            $filterform->add('town', 'text');
            //postal code
            $filterform->add('postalcode', 'text');
            //province
            $filterform->add('province', 'text');
            //startdate
            $filterform->add('startdate', 'date', array('data' => new \DateTime('2009-02-20')));
            //enddate
            $filterform->add('enddate', 'date', array('data' => new \DateTime('2009-02-20')));
            //price
            $filterform->add('dayprice', 'text');
            // Form was submitted: process the filter
            if ('POST' == $app['request']->getMethod()) {
                $filterform->bind($app['request']);
                // Form is valid
                if ($filterform->isValid()) {
                    // Extract data from form
                    $filter = $filterform->getData();
                    //process raw data to interact with db and filter
                    $filter['postalcode'] = intval($filter['postalcode']);
                    $filter['startdate'] = $filter['startdate']->format('Y/m/d');
                    $filter['enddate'] = $filter['enddate']->format('Y/m/d');
                    $filter['dayprice'] = (double)$filter['dayprice'];
                    $this->setFilter($app, $filter);
                    //find the filtered tools
                    $toolsCount = $app['tools']->getCountFiltered($filter);
                    $pages = ceil($toolsCount['total'] / 5);
                    if($currentPage == null){
                        $currentPage = 1;
                    }
                    $itemDetails = $app['tools']->getToolsFiltered($filter,($currentPage * 5) - 4,$currentPage * 5);
                }
            }
            else{
                $toolsCount = $app['tools']->getCount();
                $pages = ceil($toolsCount['total'] / 5);
                if($currentPage == null){
                    $currentPage = 1;
                }
                $itemDetails = $app['tools']->getTools(($currentPage * 5) - 4,$currentPage * 5);
            }
            for($i = 0; $i < sizeof($itemDetails); $i++){
                //cut off the description after 120 characters
                $itemDetails[$i]['description'] = substr(strip_tags($itemDetails[$i]['description']),0,120) . '...';
                // get the first image path per item
                $directory = $app['paths']['web']  . '/files/' . $itemDetails[$i]['userID'] . '/' . $itemDetails[$i]['id'] . '/';
                $itemDetails[$i]['imagePath'] = $itemDetails[$i]['userID'] . '/' . $itemDetails[$i]['id'] . '/' . getAllImages($directory)[0];
            }
            return $app['twig']->render('rent/overview.twig', array('filterform' => $filterform->createView(),'itemDetails' => $itemDetails, 'currentPage' => $currentPage, 'pages' => $pages));
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


    public function forceFilter(Request $request, Application $app) {
        if ($app['session']->get('filter_tools') == null) {
            //add dummy data
            $app['session']->set('filter_tools', array(
                'town' => '',
                'postalcode' => null,
                'province' => '',
                'startdate' => new \DateTime('2009-02-20'),
                'enddate' => new \DateTime('2009-02-20'),
                'dayprice' => ''
            ));
        }
    }

    public function getFilter(Application $app) {
        return $app['session']->get('filter_tools');
    }

    public function setFilter(Application $app, $filter) {
        $app['session']->set('filter_tools', $filter);
    }
}