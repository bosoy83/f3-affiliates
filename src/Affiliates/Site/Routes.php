<?php
namespace Affiliates\Site;

class Routes extends \Dsc\Routes\Group
{
    public function initialize()
    {
        $this->setDefaults( array(
            'namespace' => '\Affiliates\Site\Controllers',
            'url_prefix' => '/affiliate' 
        ) );
        
        $this->add( '/dashboard', 'GET', array(
            'controller' => 'Dashboard',
            'action' => 'index' 
        ) );
        
        $this->add( '/invite-friends', 'GET', array(
            'controller' => 'Invite',
            'action' => 'index'
        ) );        
    }
}