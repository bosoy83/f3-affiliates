<?php 
namespace Affiliates\Admin\Controllers;

class Dashboard extends \Dsc\Controller 
{
    public function index()
    {
    	$this->app->set('meta.title', 'Affiliate Dashboard');
    	
    	echo $this->theme->renderTheme('Affiliates/Admin/Views::dashboard.php');
    }
}