<?php
namespace Affiliates\Models;

class Referrals extends \Dsc\Mongo\Collections\Nodes
{
    public $invite_id;
    public $affiliate_id;
    public $affiliate_email;
    public $referral_user_id;        // user_id of the referral 
    public $referral_email;
    public $referral_name;          // used in the email to the affiliate that thanks them for the referral
    
    protected $__collection_name = 'affiliates.referrals';

    protected $__type = 'referral';

    protected $__config = array(
        'default_sort' => array(
            'metadata.created.time' => -1
        )
    );
    
    protected function fetchConditions()
    {
        parent::fetchConditions();
        
        $filter_invite_id = $this->getState('filter.invite_id');
        if (strlen($filter_invite_id))
        {
            $this->setCondition('invite_id', new \MongoId( (string) $filter_invite_id ) );
        }
        
        $filter_affiliate_id = $this->getState('filter.affiliate_id');
        if (strlen($filter_affiliate_id))
        {
            $this->setCondition('affiliate_id', new \MongoId( (string) $filter_affiliate_id ) );
        }
        
        $filter_referral_user_id = $this->getState('filter.referral_user_id');
        if (strlen($filter_referral_user_id))
        {
            $this->setCondition('referral_user_id', new \MongoId( (string) $filter_referral_user_id ) );
        }
        
        return $this;
    }

    /**
     * This handles the affiliate tracking,
     * placing a cookie when necessary,
     * otherwise putting the referral in the database
     *
     */
    public static function handle()
    {
        static::handleInviteId();
    	static::handleAffiliateId();
    	
    	return true;
    }    
    
    /**
     * This handles the affiliate tracking for a generic affiliate_id,
     * placing a cookie when necessary, 
     * otherwise putting the referral in the database
     *  
     */
    public static function handleAffiliateId()
    {
        $app = \Base::instance();
        $identity = \Dsc\System::instance()->get('auth')->getIdentity();
        $request_affiliate_id = \Dsc\System::instance()->get('input')->get('affiliate_id');
        $cookie_affiliate_id = $app->get('COOKIE.affiliate_id');
        
        // is there an affiliate ID in the request?
        if (!empty($request_affiliate_id))
        {
            // \Dsc\System::addMessage('Tracking Affiliate ID in the request: ' . $request_affiliate_id);
            // If the user is not logged in, set a cookie.
            if (empty($identity->id))
            {
                $app->set('COOKIE.affiliate_id', $request_affiliate_id, 31536000); // == 1 year == (86400*365)
            }
            
            // if the user IS logged in and is already a referral, just clear any cookies
            elseif (static::isUser($identity->id)) 
            {
                $app->clear('COOKIE.affiliate_id');
            }
            
            // If the user IS logged in and is not a referral, make them into a referral for affiliate_id
            // and kill any cookies
            else
            {
                try
                {
                    (new static)->bind(array(
                        'referral_user_id' => $identity->id,
                        'referral_name' => $identity->fullName(),
                        'referral_email' => $identity->email,
                        'affiliate_id' => $request_affiliate_id
                    ))->set('__send_email', true)->save();
                    
                    $app->clear('COOKIE.affiliate_id');
                }
                catch (\Exception $e)
                {
                    // TODO Log the failure in the system logger
                    return false;
                }
            }
        }
        
        // or is there an affiliate ID in a cookie and the user is not logged in?
        elseif (empty($identity->id) && !empty($cookie_affiliate_id))
        {
            // Extend the life of the cookie
            // \Dsc\System::addMessage('Extending the life of the cookie for the Affiliate ID');
            $app->set('COOKIE.affiliate_id', $cookie_affiliate_id, 31536000); // == 1 year == (86400*365)
        }
        
        // or is there an affiliate ID in a cookie and the user IS logged in
        elseif (!empty($identity->id) && !empty($cookie_affiliate_id))
        {
            // Make the user into a referral for affiliate_id and kill the cookie
            // \Dsc\System::addMessage('Making you into a referral for an Affiliate ID');
            try
            {
                (new static)->bind(array(
                    'referral_user_id' => $identity->id,
                    'referral_name' => $identity->fullName(),
                    'referral_email' => $identity->email,
                    'affiliate_id' => $cookie_affiliate_id
                ))->set('__send_email', true)->save();
                
                $app->clear('COOKIE.affiliate_id');
            }
            catch (\Exception $e)
            {
                // TODO Log the failure in the system logger
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * This handles the affiliate tracking for an invite_id,
     * placing a cookie when necessary,
     * otherwise putting the referral in the database
     *
     */
    public static function handleInviteId()
    {
        $app = \Base::instance();
        $identity = \Dsc\System::instance()->get('auth')->getIdentity();
        $request_invite_id = \Dsc\System::instance()->get('input')->get('invite_id');
        $cookie_invite_id = $app->get('COOKIE.invite_id');
    
        // is there an invite ID in the request?
        if (!empty($request_invite_id))
        {
            // \Dsc\System::addMessage('Tracking Invite ID in the request: ' . $request_invite_id);
            // If the user is not logged in, set a cookie.
            if (empty($identity->id))
            {
                // Validate the $request_invite_id
                if (\Affiliates\Models\Invites::idValid($request_invite_id)) 
                {
                    $app->set('COOKIE.invite_id', $request_invite_id, 31536000); // == 1 year == (86400*365)
                }                
            }
    
            // if the user IS logged in and is already a referral, just clear any cookies
            elseif (static::isUser($identity->id))
            {
                $app->clear('COOKIE.invite_id');
            }
    
            // If the user IS logged in and is not a referral, make them into a referral for invite_id
            // and kill any cookies
            else
            {
                try
                {
                    if ($invite = \Affiliates\Models\Invites::idValid($request_invite_id)) 
                    {
                        (new static)->bind(array(
                            'referral_user_id' => $identity->id,
                            'referral_name' => $identity->fullName(),
                            'referral_email' => $invite->recipient_email,
                            'invite_id' => $request_invite_id,
                            'affiliate_id' => $invite->affiliate_id,
                            'affiliate_email' => $invite->sender_email,                            
                        ))->set('__send_email', true)->save();
                    }
    
                    $app->clear('COOKIE.invite_id');
                }
                catch (\Exception $e)
                {
                    // TODO Log the failure in the system logger
                    return false;
                }
            }
        }
    
        // or is there an affiliate ID in a cookie and the user is not logged in?
        elseif (empty($identity->id) && !empty($cookie_invite_id))
        {
            // Extend the life of the cookie
            // \Dsc\System::addMessage('Extending the life of the cookie of the Invite ID');
            $app->set('COOKIE.invite_id', $cookie_invite_id, 31536000); // == 1 year == (86400*365)
        }
    
        // or is there an affiliate ID in a cookie and the user IS logged in
        elseif (!empty($identity->id) && !empty($cookie_invite_id))
        {
            // Make the user into a referral for invite_id and kill the cookie
            // \Dsc\System::addMessage('Making you into a referral for an Invite ID');
            try
            {
                if ($invite = \Affiliates\Models\Invites::idValid($cookie_invite_id))
                {
                    (new static)->bind(array(
                        'referral_user_id' => $identity->id,
                        'referral_name' => $identity->fullName(),
                        'referral_email' => $invite->recipient_email,
                        'invite_id' => $cookie_invite_id,
                        'affiliate_id' => $invite->affiliate_id,
                        'affiliate_email' => $invite->sender_email,
                    ))->set('__send_email', true)->save();
                }
    
                $app->clear('COOKIE.invite_id');
            }
            catch (\Exception $e)
            {
                // TODO Log the failure in the system logger
                return false;
            }
        }
    
        return true;
    }
    
    /**
     * Checks whether or not the user is already claimed as a referral
     * 
     * @param unknown $user_id
     * @return boolean
     */
    public static function isUser( $user_id ) 
    {
    	$model = (new static)->setState('filter.referral_user_id', $user_id)->getItem();
    	if (!empty($model->id)) 
    	{
    		return $model;
    	}
    	
    	return false;
    }
    
    /**
     * Gets the affiliate's user object
     *
     * @return unknown
     */
    public function affiliate()
    {
        if (empty($this->affiliate_id)) 
        {
        	return new \Users\Models\Users;
        }
        
        $user = (new \Users\Models\Users)->load(array('_id'=> new \MongoId((string)$this->affiliate_id)));
    
        return $user;
    }
    
    /**
     * Gets the referral's user object
     *
     * @return unknown
     */
    public function referral()
    {
        if (empty($this->referral_user_id))
        {
            return new \Users\Models\Users;
        }
    
        $user = (new \Users\Models\Users)->load(array('_id'=> new \MongoId((string)$this->referral_user_id)));
    
        return $user;
    }
    
    public function validate()
    {
        if (!empty($this->invite_id)) {
        	$this->invite_id = new \MongoId((string) $this->invite_id);
        }
        if (!empty($this->affiliate_id)) {
            $this->affiliate_id = new \MongoId((string) $this->affiliate_id);
        }        
        
        // ensure required fields
        if (empty($this->affiliate_id))
        {
            $this->setError('An Affiliate ID is required');
        }
        
        $affiliate = $this->affiliate();
        if (empty($affiliate->id)) 
        {
            $this->setError('Invalid Affiliate ID');
        }
        if (empty($this->affiliate_email)) 
        {
            $this->affiliate_email = $affiliate->email;
        }
        
        if (empty($this->referral_user_id))
        {
            $this->setError('The User ID of the referral is required');
        }
        
        $referral = $this->referral();
        if (empty($referral->id))
        {
            $this->setError('Invalid Referral User ID');
        }
        if (empty($this->referral_email))
        {
            $this->referral_email = $referral->email;
        }        
        if (empty($this->referral_name))
        {
            $this->referral_name = $referral->fullName();
        }        
                
        if (static::isUser($this->referral_user_id)) 
        {
        	$this->setError('Only one referral per user');
        }
        
        return parent::validate();
    }
    
    protected function beforeCreate()
    {
        // if invite_id is empty but referral_email exists, try to lookup the affiliate's invite_id to match this referral to it
        if (empty($this->invite_id) && !empty($this->referral_email)) 
        {
        	$invite = (new \Affiliates\Models\Invites)->setState('filter.affiliate_id', $this->affiliate_id)->setState('filter.recipient_email', $this->referral_email)->getItem();
        	if (!empty($invite->id)) 
        	{
        		$this->invite_id = $invite->id;
        	}
        }
          
        return parent::beforeCreate();
    }
    
    protected function afterCreate()
    {
        // create the commission
        $this->createReferralCommission();
        
        // Update invite if invite_id exists.  Mark it as converted.
        if (!empty($this->invite_id)) 
        {
            $invite = (new \Affiliates\Models\Invites)->setState('filter.id', $this->invite_id)->getItem();
            if (!empty($invite->id))
            {
                $invite->status = 'joined';
                $invite->save();
            }
        }
        
        if (!empty($this->affiliate_email))
        {
            $this->sendEmailNewReferral();
        }
        
        $this->calcAffiliateTotals( $this->affiliate_id );
        
        parent::afterCreate();
    }
    
    protected function afterDelete()
    {
        $this->calcAffiliateTotals( $this->affiliate_id );
        
        parent::afterDelete();
    }
    
    /**
     * A complete recount, not just incremental 
     * 
     * @param unknown $affiliate_id
     */
    public static function calcAffiliateTotals( $affiliate_id ) 
    {
        $user = (new \Users\Models\Users)->load(array('_id'=> new \MongoId((string)$affiliate_id)));
        if (!empty($user->id)) 
        {
            // get the referrals count and put it in the users object
            $referrals_count = (new static)->collection()->count( array(
                'affiliate_id' => new \MongoId( (string) $affiliate_id )
            ));
             
            // get the invitations count and put it in the users object
            $invites_count = (new \Affiliates\Models\Invites)->collection()->count( array(
                'affiliate_id' => new \MongoId( (string) $affiliate_id )
            ));
            
            // get the invited-but-not-joined count and put it in the users object
            $invites_not_joined_count = (new \Affiliates\Models\Invites)->collection()->count( array(
                'affiliate_id' => new \MongoId( (string) $affiliate_id ),
                'status' => 'invited'
            ));
            
            $user->{'affiliate.referrals_count'} = $referrals_count;
            $user->{'affiliate.invites_count'} = $invites_count;
            $user->{'affiliate.invites_not_joined_count'} = $invites_not_joined_count;
            
            try {
                $user->save();
            } 
            catch (\Exception $e) {
                $user->log( (string) $e, 'ERROR', __CLASS__.'::'.__FUNCTION__);
            }
            
        }
    }
    
    /**
     * Send out new referral email,
     * letting the affiliate know that they earned a referral
     *
     */
    public function sendEmailNewReferral()
    {
        \Base::instance()->set('referral', $this);
        \Base::instance()->set('settings', \Affiliates\Models\Settings::fetch());

        $html = \Dsc\System::instance()->get( 'theme' )->renderView( 'Affiliates/Views::emails_html/referral.php' );
        $text = \Dsc\System::instance()->get( 'theme' )->renderView( 'Affiliates/Views::emails_text/referral.php' );

        $subject = 'Thanks for the referral!';
    
        $this->__sendEmailNewReferral = \Dsc\System::instance()->get('mailer')->send($this->affiliate_email, $subject, array($html, $text) );
    
        return $this;
    }
    
    /**
     * Creates a commission for this referral, if applicable
     * 
     * @return \Affiliates\Models\Referrals
     */
    public function createReferralCommission()
    {
        $settings = \Affiliates\Models\Settings::fetch();
        
        if ($settings->{'commissions.for_referral'}) 
        {
            $auto_issue = (bool) $settings->{'commissions.auto_issue'};
            
            try {
            	$commission = (new \Affiliates\Models\Commissions)->bind(array(
            		'affiliate_id' => $this->affiliate_id,
            	    'affiliate_name' => $this->affiliate()->fullName(),
            	    'referral_id' => $this->id,
            	    'referral_name' => $this->referral()->fullName(),
            	    'type' => \Affiliates\Models\Commissions::TYPE_REFERRAL
            	))->set('__issue', $auto_issue)->save();
            }
            catch (\Exception $e) {
            	
            }
        }
        
        return $this;
    }
    
    /**
     * Does this referral trigger a conversion commission?
     * 
     * @return boolean
     */
    public function triggersConversionCommission() 
    {
    	$settings = \Affiliates\Models\Settings::fetch();
    	if (!$settings->{'commissions.for_conversion'})
    	{
    		return false;
    	}

    	// How long does the affiliate earn conversion commissions?
    	// does this affiliate still get to earn conversion commissions for this referral?
    	    	
    	$conversion_number = $settings->{'shop.conversion_number'};
    	$conversion_period = $settings->{'shop.conversion_period'};
    	
    	if ($conversion_period == 'forever') 
    	{
    		return true;
    	}
    	
    	if ($conversion_period == 'order') 
    	{
    	    // is the count of created commissions for this referral < the $conversion_number?    	    
    	    $commissions_count = \Affiliates\Models\Commissions::collection()->count(array(
    	    	'referral_id' => $this->id,
    	        'type' => \Affiliates\Models\Commissions::TYPE_CONVERSION
    	    ));
    	    
    	    // if so, $return = true;
    	    if ($commissions_count < $conversion_number) 
    	    {
    	    	return true;
    	    }
    		
    	}
    	else 
    	{
    	    // $conversion_period == month | year
    	    // $period_expiration_date = $referral_created_date + ($conversion_number $conversion_period)

    	    $period_expiration_date = date( 'Y-m-d', strtotime( $this->{'metadata.created.local'} . "+ $conversion_number $conversion_period" ) );
    	    if (date('Y-m-d') < $period_expiration_date) 
    	    {
    	    	return true;
    	    }
    	}
    	
    	return false;
    }
    
    /**
     * 
     * @param unknown $shop_order_number
     * @param unknown $shop_order_amount
     * @return \Affiliates\Models\Referrals
     */
    public function createConversionCommission( $shop_order_number, $shop_order_amount )
    {
    	return $this;
    }
}