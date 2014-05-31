<?php 
namespace Affiliates\Site\Controllers;

class Dashboard extends \Dsc\Controller 
{    
    public function index()
    {
    	$this->app->set('meta.title', 'Invite Friends | Affiliates');
    	
    	echo $this->view->renderTheme('Affiliates/Site/Views::invite-friends.php');
    }
}