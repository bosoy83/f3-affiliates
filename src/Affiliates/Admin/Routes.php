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
        
        $this->add( '', 'GET', array(
            'controller' => 'Dashboard',
            'action' => 'index'
        ) );
        
        $this->add( '/campaigns', 'GET', array(
            'controller' => 'Campaigns',
            'action' => 'index'
        ) );
        
        $this->addCrudGroup( 'Referrals', 'Referral' );
        $this->addCrudGroup( 'Commissions', 'Commission' );
    }
}