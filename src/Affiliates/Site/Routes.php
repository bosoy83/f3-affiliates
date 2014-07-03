<?php
namespace Affiliates\Site;

class Routes extends \Dsc\Routes\Group
{
    public function initialize()
    {
        $f3 = \Base::instance();
        
        $this->setDefaults( array(
            'namespace' => '\Affiliates\Site\Controllers',
            'url_prefix' => '/affiliate' 
        ) );

        $f3->route('GET /affiliate/@affiliate_id', function($f3){
        	\Dsc\System::instance()->get('input')->set('affiliate_id', $f3->get('PARAMS.affiliate_id'));
        	\Affiliates\Models\Referrals::handle();
        	\Dsc\System::addMessage('Please sign in or register with us so we can complete the referral. Thanks!');
        	$f3->reroute('/');
        });
        
        $f3->route('GET /invite/@invite_id', function($f3){
            \Dsc\System::instance()->get('input')->set('invite_id', $f3->get('PARAMS.invite_id'));
            \Affiliates\Models\Referrals::handle();
            \Dsc\System::addMessage('Please sign in or register with us so we can complete the referral. Thanks!');
            $f3->reroute('/');
        });
        
        $f3->route('GET /affiliate/share/thanks', function($f3){
            echo "Thanks!";
        });
        
        $this->add( '/dashboard', 'GET', array(
            'controller' => 'Dashboard',
            'action' => 'index' 
        ) );
        
        $this->add( '/invite-friends', 'GET', array(
            'controller' => 'Invite',
            'action' => 'index'
        ) );

        $this->add( '/invite-friends/email', 'GET', array(
            'controller' => 'Invite',
            'action' => 'email'
        ) );
        
        $this->add( '/invite-friends/email', 'POST', array(
            'controller' => 'Invite',
            'action' => 'emailSubmit'
        ) );
        
        $this->add( '/invite-friends/link', 'GET', array(
            'controller' => 'Invite',
            'action' => 'link'
        ) );
        
        $this->add( '/invite-history', 'GET|POST', array(
            'controller' => 'Invites',
            'action' => 'index'
        ) );

        $this->add( '/invite-history/page/@page', 'GET', array(
            'controller' => 'Invites',
            'action' => 'index'
        ) );
        
        $this->add( '/fp/@id [ajax]', 'GET|POST', array(
            'controller' => 'Fingerprint',
            'action' => 'index'
        ) );        
    }
}