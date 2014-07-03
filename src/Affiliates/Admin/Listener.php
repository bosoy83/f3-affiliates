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
                'priority' => 30,
                'title' => 'Affiliates',
                'icon' => 'fa fa-share-alt',
                'is_root' => false,
                'tree' => $root,
                'base' => '/admin/affiliates'
            ));
            
            $children = array(
                /*
                array(
                    'title' => 'Dashboard',
                    'route' => './admin/affiliates',
                    'icon' => 'fa fa-share-alt'
                ),
                */
                /*
                array(
                    'title' => 'Campaigns',
                    'route' => './admin/affiliates/campaigns',
                    'icon' => 'fa fa-bullhorn'
                ),
                */
                array(
                    'title' => 'Referrals',
                    'route' => './admin/affiliates/referrals',
                    'icon' => 'fa fa-puzzle-piece'
                ),
                array(
                    'title' => 'Commissions',
                    'route' => './admin/affiliates/commissions',
                    'icon' => 'fa fa-money'
                ),
                array(
                    'title' => 'Settings',
                    'route' => './admin/affiliates/settings',
                    'icon' => 'fa fa-cogs'
                )
            );
            
            $pages->addChildren($children, $root);
            
            \Dsc\System::instance()->addMessage('Affiliates added its admin menu items.');
        }
    }
}