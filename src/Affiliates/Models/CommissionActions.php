<?php
namespace Affiliates\Models;

class CommissionActions extends \Dsc\Models
{
    public $amount;             // float
    public $type;               // the type of the action, e.g. shop.credit or shop.coupon
    public $issued;             // boolean
}