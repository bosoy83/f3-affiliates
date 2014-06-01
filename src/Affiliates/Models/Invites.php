<?php 
namespace Affiliates\Models;

class Invites extends \Dsc\Mongo\Collections\Nodes 
{
    public $affiliate_id;
    public $sender_email;
    public $sender_name;
    public $recipient_email;
    public $message;
    
    protected $__collection_name = 'affiliates.invites';
    protected $__type = 'email';
    
    protected $__config = array(
        'default_sort' => array(
            'metadata.created.time' => -1
        ),
    );
    
    protected function afterSave()
    {
        parent::afterSave();
        
        if (!empty($this->__send_email)) 
        {
        	$this->sendEmailNewInvite();
        }
    }
    
    /**
     * Send out new invite email
     * 
     */
    public function sendEmailNewInvite()
    {
        \Base::instance()->set('invite', $this);
        \Base::instance()->set('settings', \Affiliates\Models\Settings::fetch());
    
        $html = \Dsc\System::instance()->get( 'theme' )->renderView( 'Affiliates/Views::emails_html/invite.php' );
        $text = \Dsc\System::instance()->get( 'theme' )->renderView( 'Affiliates/Views::emails_text/invite.php' );

        $subject = $this->sender_name . ' has sent you an invitation';
    
        $this->__sendEmailNewInvite = \Dsc\System::instance()->get('mailer')->send($this->recipient_email, $subject, array($html, $text) );
    
        return $this;
    }    
}