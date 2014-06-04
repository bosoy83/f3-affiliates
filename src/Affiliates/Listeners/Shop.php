<?php
namespace Affiliates\Listeners;

class Shop extends \Dsc\Singleton
{
    public function onShopAcceptOrder( $event )
    {
        $order = $event->getArgument('order');
 
        $settings = \Affiliates\Models\Settings::fetch();
        
        // are conversion commissions enabled?
        if ($settings->{'commissions.for_conversion'})
        {
            // is the order customer a referral?
            $referral = (new \Affiliates\Models\Referrals)->setState('filter.referral_user_id', $order->user_id)->getItem(); 
            if (!empty($referral->id)) 
            {
            	$affiliate = $referral->affiliate();
            	
            	// How long does the affiliate earn conversion commissions?
            	// does this affiliate still get to earn conversion commissions for this referral?
            	$can_earn_commission = $referral->triggersConversionCommission();
            	
            	if ($can_earn_commission) 
            	{
            	    $auto_issue = (bool) $settings->{'commissions.auto_issue'};
            	     
            	    try {
            	        $commission = (new \Affiliates\Models\Commissions)->bind(array(
            	            'referral_id' => $referral->id,
            	            'referral_name' => $referral->referral()->fullName(),
            	            'affiliate_id' => $affiliate->id,            	            
            	            'affiliate_name' => $affiliate->fullName(),
            	            'shop_order_id' => $order->id,
            	            'shop_order_amount' => (float) $order->grand_total,
            	            'type' => \Affiliates\Models\Commissions::TYPE_CONVERSION
            	        ))->set('__issue', $auto_issue)->save();
            	    }
            	    catch (\Exception $e) {
            	    
            	    }
            	}
            }
        }        
    }
}