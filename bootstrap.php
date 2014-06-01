<?php
class AffiliatesBootstrap extends \Dsc\Bootstrap
{
    protected $dir = __DIR__;
    protected $namespace = 'Affiliates';
    
    protected function preAdmin()
    {
        parent::preAdmin();
        
        \Modules\Factory::registerPositions( array('left-invite-friend', 'right-invite-friend', 'above-invite-friend', 'below-invite-friend') );
    }
    
    protected function runSite()
    {
        parent::runSite();

        /**
         * Handle the affiliate tracking!
         */
        \Affiliates\Models\Referrals::handle();
    }
}
$app = new AffiliatesBootstrap();