<?php 
namespace Affiliates\Site\Controllers;

class Dashboard extends \Dsc\Controller 
{    
    public function index()
    {
    	$this->app->set('meta.title', 'Affiliate Dashboard');
    	
    	echo $this->view->renderTheme('Affiliates/Site/Views::dashboard.php');
    }
}