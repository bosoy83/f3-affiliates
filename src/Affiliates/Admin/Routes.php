<?php
namespace Affiliates\Admin;

class Routes extends \Dsc\Routes\Group
{
    public function initialize()
    {
        $this->setDefaults( array(
            'namespace' => '\Affiliates\Admin\Controllers',
            'url_prefix' => '/admin/affiliates' 
        ) );
        
        $this->addSettingsRoutes();
    }
}