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
        $settings = \Affiliates\Models\Settings::fetch();
        
        $flash_filled = \Dsc\System::instance()->getUserState('invite_friends.email.flash_filled');
        if (!$flash_filled) {
            $this->flash->store(array(
                'sender_name' => $this->auth->getIdentity()->fullName(),
                'sender_email' => $this->auth->getIdentity()->email,
                'message' => $settings->{'general.default_message'},
            ));
        }
        \Dsc\System::instance()->setUserState('invite_friends.email.flash_filled', false);

        $invites = (new \Affiliates\Models\Invites)->setState('list.limit', 10)->setState('filter.affiliate_id', $this->auth->getIdentity()->id)->getItems();
        $this->app->set('invites', $invites);
        
    	$this->app->set('meta.title', 'Affiliate Dashboard');
    	
    	echo $this->theme->renderTheme('Affiliates/Site/Views::dashboard.php');
    }
}