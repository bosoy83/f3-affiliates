<?php 
namespace Affiliates\Admin\Controllers;

class Referrals extends \Dsc\Controller 
{
    use \Dsc\Traits\Controllers\AdminList;
    
    protected $list_route = '/admin/affiliates/referrals';
    
    protected function getModel()
    {
        $model = new \Affiliates\Models\Referrals;
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
    
        $this->app->set('meta.title', 'Referrals | Affiliates');
    
        echo $this->theme->render('Affiliates/Admin/Views::referrals/list.php');
    }
}