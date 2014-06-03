<?php 
namespace Affiliates\Admin\Controllers;

class Campaigns extends \Dsc\Controller 
{
    public function index()
    {
    	$this->app->set('meta.title', 'Campaigns | Affiliates');
    	
    	echo $this->theme->renderTheme('Affiliates/Admin/Views::campaigns/list.php');
    }
}