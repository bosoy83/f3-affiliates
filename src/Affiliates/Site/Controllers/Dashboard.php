<?php 
namespace Affiliates\Site\Controllers;

class Dashboard extends \Dsc\Controller 
{
    public function beforeRoute()
    {
        $this->requireIdentity();
    }
    
    public function index()
    {
    	$this->app->set('meta.title', 'Affiliate Dashboard');
    	
    	echo $this->theme->renderTheme('Affiliates/Site/Views::dashboard.php');
    }
}