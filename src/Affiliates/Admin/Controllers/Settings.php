<?php 
namespace Affiliates\Admin\Controllers;

class Settings extends \Admin\Controllers\BaseAuth 
{
	use \Dsc\Traits\Controllers\Settings;
	
	protected $layout_link = 'Affiliates/Admin/Views::settings/default.php';
	protected $settings_route = '/admin/affiliates/settings';
    
    protected function getModel()
    {
        $model = new \Affiliates\Models\Settings;
        return $model;
    }
}