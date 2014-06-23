<?php 
namespace Affiliates\Site\Controllers;

class Invites extends \Dsc\Controller 
{
    public function beforeRoute()
    {
        $this->requireIdentity();
    }
    
    public function index()
    {
        $identity = $this->getIdentity();
        
        $model = new \Affiliates\Models\Invites;
        $model->emptyState()->populateState()
            ->setState('list.limit', 30 )
            ->setState('filter.affiliate_id', (string) $identity->id )
        ;
        $state = $model->getState();
                
        try {
            $paginated = $model->paginate();
        } catch ( \Exception $e ) {
            \Dsc\System::instance()->addMessage( $e->getMessage(), 'error');
            $f3->reroute( '/' );
            return;
        }

        \Base::instance()->set('state', $state );
        \Base::instance()->set('paginated', $paginated );

    	$this->app->set('meta.title', 'My Invite History');
    	
    	echo $this->theme->renderTheme('Affiliates/Site/Views::invites/index.php');
    }
}