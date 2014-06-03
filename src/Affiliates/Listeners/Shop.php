<?php
namespace Affiliates\Listeners;

class Shop extends \Dsc\Singleton
{
    public function afterCreateAffiliatesModelsReferrals( $event )
    {
        $referral = $event->getArgument('model');
    
        // is there a defined amount of store credit the affiliate earns for a referral?
        $settings = \Affiliates\Models\Settings::fetch();
        if ($credit = (float) $settings->{'shop.store_credit_per_referral'}) 
        {
        	$affiliate = $referral->affiliate();
        	$referral_user = $referral->referral();
        	if (!empty($affiliate->id)) 
        	{
        	    try {
        	        $credit = (new \Shop\Models\Credits)->bind(array(
        	            'user_id'=>$affiliate->id,
        	            'amount'=>$credit,
        	            'referral_id'=>$referral->id,
        	            'message'=>'For referral #' . $referral->id . ' - ' . $referral_user->fullName()
        	        ))->set('__issue_to_user', true)->save();
        	    } 
        	    catch (\Exception $e) {
        	        $referral->log('Could not save store credit: ' . (string) $e );
        	    }
        	}
        }
    }
        
    public function afterShopCheckout( $event )
    {
        $checkout = $event->getArgument('checkout');
        $cart = $checkout->cart();
        $order = $checkout->order();
        
        // is there a defined amount of store credit the affiliate earns for a referral's checkout amount? 
        // is this user a referral?  
    }
}