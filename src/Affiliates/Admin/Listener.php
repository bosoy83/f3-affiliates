<?php
namespace Affiliates\Admin;

class Listener extends \Prefab
{

    public function onSystemRebuildMenu($event)
    {
        if ($model = $event->getArgument('model'))
        {
            $root = $event->getArgument('root');
            $pages = clone $model;
            
            $pages->insert(array(
                'type' => 'admin.nav',
                'priority' => 40,
                'title' => 'Affiliates',
                'icon' => 'fa fa-file-text',
                'is_root' => false,
                'tree' => $root,
                'base' => '/admin/pages'
            ));
            
            $children = array(
                array(
                    'title' => 'Affiliates',
                    'route' => './admin/pages/pages',
                    'icon' => 'fa fa-list'
                ),
                array(
                    'title' => 'Categories',
                    'route' => './admin/pages/categories',
                    'icon' => 'fa fa-folder'
                )
            );
            
            $pages->addChildren($children, $root);
            
            \Dsc\System::instance()->addMessage('Affiliates added its admin menu items.');
        }
    }

    public function onAdminNavigationGetQuickAddItems($event)
    {
        $items = $event->getArgument('items');
        $tree = $event->getArgument('tree');
        
        $item = new \stdClass();
        $item->title = 'Affiliates';
        $item->form = \Affiliates\Admin\Controllers\MenuItemQuickAdd::instance()->page($event);
        $items[] = $item;
        
        $item = new \stdClass();
        $item->title = 'Affiliates Category';
        $item->form = \Affiliates\Admin\Controllers\MenuItemQuickAdd::instance()->category($event);
        $items[] = $item;
        
        $event->setArgument('items', $items);
    }
}