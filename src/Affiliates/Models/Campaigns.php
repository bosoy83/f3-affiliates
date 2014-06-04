<?php
namespace Affiliates\Models;

class Campaigns extends \Dsc\Mongo\Collections\Nodes
{
    protected $__collection_name = 'affiliates.campaigns';

    protected $__type = 'campaign';

    protected $__config = array(
        'default_sort' => array(
            'metadata.created.time' => -1
        )
    );
}