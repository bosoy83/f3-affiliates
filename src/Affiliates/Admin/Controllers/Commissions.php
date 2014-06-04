<?php 
namespace Affiliates\Admin\Controllers;

class Commissions extends \Dsc\Controller 
{
    use \Dsc\Traits\Controllers\AdminList;
    
    protected $list_route = '/admin/affiliates/commissions';
    
    protected function getModel()
    {
        $model = new \Affiliates\Models\Commissions;
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
    
        $this->app->set('meta.title', 'Commissions | Affiliates');
    
        echo $this->theme->render('Affiliates/Admin/Views::commissions/list.php');
    }
}