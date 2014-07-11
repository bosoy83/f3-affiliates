<?php 
namespace Affiliates\Admin\Controllers;

class Commission extends \Admin\Controllers\BaseAuth
{
    use \Dsc\Traits\Controllers\CrudItemCollection;

    protected $list_route = '/admin/affiliates/commissions';
    protected $create_item_route = '/admin/affiliates/commission/create';
    protected $get_item_route = '/admin/affiliates/commission/read/{id}';
    protected $edit_item_route = '/admin/affiliates/commission/edit/{id}';
    
    protected function getModel()
    {
        $model = new \Affiliates\Models\Commissions;
        return $model;
    }
    
    protected function getItem()
    {
        $id = $this->inputfilter->clean( $this->app->get('PARAMS.id'), 'alnum' );
        $model = $this->getModel()
        ->setState('filter.id', $id);
    
        try {
            $item = $model->getItem();
        } catch ( \Exception $e ) {
            \Dsc\System::instance()->addMessage( "Invalid Item: " . $e->getMessage(), 'error');
            $this->app->reroute( $this->list_route );
            return;
        }
    
        return $item;
    }
    
    protected function displayCreate()
    {
        $this->app->set('meta.title', 'Commission | Affiliates');
    
        $this->theme->event = $this->theme->trigger( 'onDisplayAffiliatesCommissionsEdit', array( 'item' => $this->getItem(), 'tabs' => array(), 'content' => array() ) );
        echo $this->theme->render('Affiliates/Admin/Views::commissions/create.php');
    }
    
    protected function displayEdit()
    {
        $this->app->set('meta.title', 'Commission | Affiliates');
    
        $this->theme->event = $this->theme->trigger( 'onDisplayAffiliatesCommissionsEdit', array( 'item' => $this->getItem(), 'tabs' => array(), 'content' => array() ) );
        echo $this->theme->render('Affiliates/Admin/Views::commissions/edit.php');
    }    
    
    protected function displayRead() 
    {
        $this->app->set('meta.title', 'Commission | Affiliates');
        
        $this->theme->event = $this->theme->trigger( 'onDisplayAffiliatesCommissions', array( 'item' => $this->getItem(), 'tabs' => array(), 'content' => array() ) );
        echo $this->theme->render('Affiliates/Admin/Views::commissions/read.php');    	
    }
    
    public function refreshTotals()
    {
        $commission = $this->getItem();
        
        if (empty($commission->id)) 
        {
            \Dsc\System::addMessage('Invalid ID', 'error');
            $this->app->reroute('/admin/affiliates/commissions');
        }
        
        $commission->{'affiliates.total_spent'} = $commission->totalSpent(true);
        $commission->{'affiliates.orders_count'} = $commission->ordersCount(true);
        
        try 
        {
            $commission->save();
            $commission->checkCampaigns();
            
            \Dsc\System::addMessage('Totals refreshed', 'success');
        }
        
        catch (\Exception $e) 
        {
            \Dsc\System::addMessage($e->getMessage(), 'error');
        }
        
        $this->app->reroute('/admin/affiliates/commission/read/' . $commission->id);
    }
    
    /**
     *
     * @throws \Exception
     */
    public function issue()
    {
        try {
            $item = $this->getItem();
            if (empty($item->id)) {
                throw new \Exception('Invalid Item');
            }
            $item->issue();
            \Dsc\System::addMessage('Issued', 'success');
        }
        catch(\Exception $e) {
            \Dsc\System::addMessage('Issuing failed.', 'error');
            \Dsc\System::addMessage($e->getMessage(), 'error');
        }
    
        $id = $this->inputfilter->clean( $this->app->get('PARAMS.id'), 'alnum' );
        $this->app->reroute('/admin/affiliates/commission/read/' . $id);
    }
    
    public function revoke()
    {
        try {
            $item = $this->getItem();
            if (empty($item->id)) {
                throw new \Exception('Invalid Item');
            }
            $item->revoke();
            \Dsc\System::addMessage('Revoked', 'success');
        }
        catch(\Exception $e) {
            \Dsc\System::addMessage('Revoke failed.', 'error');
            \Dsc\System::addMessage($e->getMessage(), 'error');
        }
    
        $id = $this->inputfilter->clean( $this->app->get('PARAMS.id'), 'alnum' );
        $this->app->reroute('/admin/affiliates/commission/read/' . $id);
    
    }    
}