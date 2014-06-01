<?php
namespace Affiliates\Models;

class Referrals extends \Dsc\Mongo\Collections\Nodes
{
    public $invite_id;
    public $affiliate_id;
    public $affiliate_email;
    public $referral_user_id;        // user_id of the referral 
    public $referral_email;
    
    protected $__collection_name = 'affiliates.referrals';

    protected $__type = 'referral';

    protected $__config = array(
        'default_sort' => array(
            'metadata.created.time' => -1
        )
    );

    /**
     * This handles the affiliate tracking,
     * placing a cookie when necessary, 
     * otherwise putting the referral in the database
     *  
     */
    public static function handle()
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
            
            // If the user IS logged in, make them into a referral for affiliate_id (but only if they aren't a referral already)
            // and kill any cookies
            else
            {
                try
                {
                    (new static)->bind(array(
                        'user_id' => $identity->id,
                        'affiliate_id' => $request_affiliate_id
                    ))->save();
                    
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
        elseif (empty($identity->id)&&!empty($cookie_affiliate_id))
        {
            // Extend the life of the cookie
            // \Dsc\System::addMessage('Extending the life of the cookie');
            $app->set('COOKIE.affiliate_id', $cookie_affiliate_id, 31536000); // == 1 year == (86400*365)
        }
        
        // or is there an affiliate ID in a cookie and the user IS logged in
        elseif (!empty($identity->id)&&!empty($cookie_affiliate_id))
        {
            // TODO Make the user into a referral for affiliate_id and kill the cookie
            // \Dsc\System::addMessage('Making you into a referral');
            try
            {
                (new static)->bind(array(
                    'user_id' => $identity->id,
                    'affiliate_id' => $cookie_affiliate_id
                ))->save();
                
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
    
    protected function afterSave()
    {
        parent::afterSave();
    
        if (!empty($this->__send_email))
        {
            //$this->sendEmailNewReferral();
        }
    }
    
    /**
     * Send out new referral email,
     * letting the affiliate know that they earned a referral
     *
     */
    public function sendEmailNewReferral()
    {
        \Base::instance()->set('invite', $this);
        \Base::instance()->set('settings', \Affiliates\Models\Settings::fetch());
    
        $html = \Dsc\System::instance()->get( 'theme' )->renderView( 'Affiliates/Views::emails_html/referral.php' );
        $text = \Dsc\System::instance()->get( 'theme' )->renderView( 'Affiliates/Views::emails_text/referral.php' );
    
        $subject = 'Thanks for the referral!';
    
        $this->__sendEmailNewReferral = \Dsc\System::instance()->get('mailer')->send($this->recipient_email, $subject, array($html, $text) );
    
        return $this;
    }
}