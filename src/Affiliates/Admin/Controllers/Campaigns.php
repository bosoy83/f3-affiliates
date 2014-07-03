<?php 
namespace Affiliates\Admin\Controllers;

class Campaigns extends \Admin\Controllers\BaseAuth 
{
    use \Dsc\Traits\Controllers\AdminList;
    
    protected $list_route = '/admin/affiliates/campaigns';
    
    protected function getModel()
    {
        $model = new \Affiliates\Models\Campaigns;
        return $model;
    }
    
    public function index()
    {
        $model = $this->getModel();
        $state = $model->emptyState()->populateState()->getState();
        \Base::instance()->set('state', $state );
        $paginated = $model->paginate();
        \Base::instance()->set('paginated', $paginated );
        \Base::instance()->set('selected', 'null' );
    
        $this->app->set('meta.title', 'Campaigns | Affiliates');
    
        echo $this->theme->render('Affiliates/Admin/Views::campaigns/list.php');
    }
}