<?php
class AffiliatesBootstrap extends \Dsc\Bootstrap
{

    protected $dir = __DIR__;

    protected $namespace = 'Affiliates';

    protected function preAdmin()
    {
        parent::preAdmin();
        
        \Modules\Factory::registerPositions(array(
            'left-invite-friend',
            'right-invite-friend',
            'above-invite-friend',
            'below-invite-friend',
            'above-affiliate-dashboard',
            'right-affiliate-dashboard',
        ));
        
        \Dsc\System::instance()->getDispatcher()->addListener(\Affiliates\Listeners\Shop::instance());
    }
    
    protected function preSite()
    {
        parent::preSite();
        
        \Dsc\System::instance()->getDispatcher()->addListener(\Affiliates\Listeners\Shop::instance());
    }

    protected function postSite()
    {
        /**
         * Handle the affiliate tracking!
         */
        \Affiliates\Models\Referrals::handle();
    }
}
$app = new AffiliatesBootstrap();