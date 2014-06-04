<?php
namespace Affiliates\Models;

class Commissions extends \Dsc\Mongo\Collections\Nodes
{
    public $affiliate_id = null;                // MongoId of affiliate.  affiliate_id = user_id
    public $affiliate_name = null;
    public $referral_id = null;                 // MongoId of referral
    public $referral_name = null;                 
    
    public $issued = false;                     // bool, has this been issued
    public $amount = null;                      // float, can be negative, is the sum of each ->actions.amount
    public $balance_before;                     // float
    public $balance_after;                      // float
    public $message;                            // string
    public $history = array();
    public $actions = array();                  // array of CommissionAction objects
    
    public $shop_order_id = null;               // MongoId of an order for which this commission is granted, if this was a conversion type commission
    public $shop_order_amount = null;           // float
    
    public $__issue = false;                    // set this to true if you want the commission immediately issued to the user after it is created
    
    protected $__collection_name = 'affiliates.commissions';
    protected $__type = self::TYPE_GENERAL;

    protected $__config = array(
        'default_sort' => array(
            'metadata.created.time' => -1
        )
    );
    
    const TYPE_GENERAL = "general";
    const TYPE_REFERRAL = "referral";
    const TYPE_CONVERSION = "conversion";    
    
    protected function fetchConditions()
    {
        parent::fetchConditions();
    
        $filter_user_id = $this->getState('filter.user_id');
        if (strlen($filter_user_id))
        {
            $this->setCondition('affiliate_id', new \MongoId( (string) $filter_user_id ) );
        }
    }
    
    public function validate()
    {
        if (empty($this->affiliate_id))
        {
            $this->setError('Commissions must be issued to an affiliate');
        }
    
        return parent::validate();
    }
    
    protected function beforeSave()
    {
        // Loop through all the actions and calculate the amount of this commission
        $this->amount = 0;
        foreach ($this->actions as $action) 
        {
            if (!empty($action['amount'])) {
                $this->amount += $action['amount'];
            }        	
        }
        
        $this->issued = (bool) $this->issued;
        $this->amount = (float) $this->amount;
        $this->balance_before = (float) $this->balance_before;
        $this->balance_after = (float) $this->balance_after;
        $this->affiliate_id = new \MongoId( (string) $this->affiliate_id );
        if (!empty($this->referral_id)) {
            $this->referral_id = new \MongoId( (string) $this->referral_id );
        }        
        if (!empty($this->shop_order_id)) {
            $this->shop_order_id = new \MongoId( (string) $this->shop_order_id );
        }
    
        return parent::beforeSave();
    }
    
    protected function afterSave()
    {
        if (!empty($this->__issue))
        {
            $this->issue();
        }
    
        parent::afterSave();
    }
    
    protected function beforeCreate()
    {
        if (class_exists('\Shop\Models\Orders')) 
        {
            // shop exists, do the integration
            $settings = \Affiliates\Models\Settings::fetch();
            
            $referral_credit = (float) $settings->{'shop.store_credit_per_referral'};
            if ($referral_credit && $this->type == self::TYPE_REFERRAL)
            {
                // affiliate earns a shop.credit for the referral, so push it into the actions stack
                $this->actions[] = (new \Affiliates\Models\CommissionActions)->bind(array(
                	'amount' => $referral_credit,
                    'type' => 'shop.credit',
                    'issued' => false,
                ))->cast(); 
            }
            
            $conversion_credit = (float) $settings->{'shop.store_credit_per_conversion'};
            $conversion_credit_type = (float) $settings->{'shop.store_credit_per_conversion_type'}; // flat-rate or percentage
            if ($conversion_credit && $this->type == self::TYPE_CONVERSION)
            {
                $amount = ($conversion_credit_type == 'flat-rate') ? $conversion_credit : (($conversion_credit / 100) * $this->shop_order_amount);
                 
                // affiliate earns a shop.credit for the referral, so push it into the actions stack
                $this->actions[] = (new \Affiliates\Models\CommissionActions)->bind(array(
                    'amount' => (float) $amount,
                    'type' => 'shop.credit',
                    'issued' => false,
                ))->cast();
            }            
        }
        
        parent::beforeCreate();
    }
    
    /**
     * Gets the associated user object
     *
     * @return unknown
     */
    public function affiliate()
    {
        $user = (new \Users\Models\Users)->load(array('_id'=>$this->affiliate_id));
    
        return $user;
    }
    
    /**
     *
     * @return unknown
     */
    public function affiliateName()
    {
        $name = $this->affiliate_name;
        if (empty($name)) {
            $name = $this->affiliate()->fullName();
        }
    
        return $name;
    }
    
    /**
     * Issues a commission, updating the user's balance appropriately
     *
     * @return \Affiliates\Models\Commissions
     */
    public function issue()
    {
        if (!$this->issued)
        {
            $user = $this->affiliate();
            if (empty($user->id)) {
                throw new \Exception('Invalid Affiliate');
            }
             
            $this->balance_before = (float) $user->{'affiliate.commission.balance'};
            $this->balance_after = $this->balance_before + (float) $this->amount;
            // Add to the history
            $this->history[] = array(
                'created' => \Dsc\Mongo\Metastamp::getDate('now'),
                'subject' => \Dsc\System::instance()->get('auth')->getIdentity()->fullName(),
                'verb' => 'issued',
                'object' => (float) $this->amount
            );
            $user->{'affiliate.commission.balance'} = (float) $this->balance_after;
            $user->save();
    
            // Handle all the actions, 
            foreach ($this->actions as $key=>$action) 
            {
            	switch ($action['type']) 
            	{
            		case "shop.credit":
            		    if (class_exists('\Shop\Models\Orders') && empty($action['issued'])) 
            		    {
            		    	// Issue the credit
            		        try {
                                $credit = (new \Shop\Models\Credits)->bind(array(
                                    'user_id'=>$this->affiliate_id,
                                    'amount'=>$action['amount'],
                                    'referral_id'=>$this->referral_id,
                                    'commission_id'=>$this->id,
                                    'message'=>'For referral #' . $this->referral_id . ' - Commission #' . $this->id
                                ))->set('__issue_to_user', true)->save();
                                
                                $this->actions[$key]['credit_id'] = $credit->id;
                                $this->actions[$key]['issued'] = true;
                            }
                            catch (\Exception $e) {
                                $this->log('Could not save store credit: ' . (string) $e );
                            }            		    	
            		    }
            		    break;
            		default:
            		    break;
            	}
            }
            
            // trigger Listener event so they can handle them
            // listeners can loop through the actions themselves and evaluate $action['type'] and $action['issued']
            $this->__issue_event = \Dsc\System::instance()->trigger( 'onAffiliatesIssueCommission', array(
                'commission' => $this
            ) );
            
            $this->issued = true;
            $this->save();
        }
    
        return $this;
    }
    
    /**
     * Revoke an issued commission, updating the user's balance appropriately
     *
     * @return \Affiliates\Models\Commissions
     */
    public function revoke()
    {
        if ($this->issued)
        {
            $user = $this->affiliate();
            if (empty($user->id)) {
                throw new \Exception('Invalid Affiliate');
            }
             
            $this->balance_before = (float) $user->{'affiliate.commission.balance'};
            $this->balance_after = $this->balance_before - (float) $this->amount;
            // Add to the history
            $this->history[] = array(
                'created' => \Dsc\Mongo\Metastamp::getDate('now'),
                'subject' => \Dsc\System::instance()->get('auth')->getIdentity()->fullName(),
                'verb' => 'revoked',
                'object' => (float) $this->amount
            );
             
            $user->{'affiliate.commission.balance'} = (float) $this->balance_after;
            $user->save();
            
            // Undo all the actions,
            foreach ($this->actions as $key=>$action)
            {
                switch ($action['type'])
                {
                	case "shop.credit":
                	    if (class_exists('\Shop\Models\Orders') && !empty($action['issued']) && !empty($action['credit_id']))
                	    {
                	        // Revoke the credit
                	        try {
                	            $credit = (new \Shop\Models\Credits)->setState('filter.id', $action['credit_id'])->getItem();
                	            if (!empty($credit->id)) {
                	            	$credit->revoke();
                	            }
                	            $this->actions[$key]['issued'] = false;
                	        }
                	        catch (\Exception $e) {
                	            $this->log('Could not revoke credit: ' . (string) $e );
                	        }
                	    }
                	    break;
                	default:
                	    break;
                }
            }            

            // trigger Listener event so they can handle them
            // listeners can loop through the actions themselves and evaluate $action['type'] and $action['issued']
            $this->__revoke_event = \Dsc\System::instance()->trigger( 'onAffiliatesRevokeCommission', array(
                'commission' => $this
            ) );
            
    
            $this->issued = false;
            $this->save();
        }
    
        return $this;
    }
}