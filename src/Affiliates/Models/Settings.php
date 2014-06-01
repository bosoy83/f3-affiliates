<?php 
namespace Affiliates\Models;

class Settings extends \Dsc\Mongo\Collections\Settings
{
    protected $__type = 'affiliates.settings';
    
    public $general = array(
    	'default_message' => null
    );
}