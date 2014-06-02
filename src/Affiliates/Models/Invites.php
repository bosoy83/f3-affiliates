<?php 
namespace Affiliates\Models;

class Invites extends \Dsc\Mongo\Collections\Nodes 
{
    public $affiliate_id;
    public $sender_email;
    public $sender_name;
    public $recipient_email;
    public $message;
    public $status = 'invited';         // invited / joined
    
    protected $__collection_name = 'affiliates.invites';
    protected $__type = 'email';
    
    protected $__config = array(
        'default_sort' => array(
            'metadata.created.time' => -1
        ),
    );
    
    protected function fetchConditions()
    {
        parent::fetchConditions();
    
        $filter_affiliate_id = $this->getState('filter.affiliate_id');
        if (strlen($filter_affiliate_id))
        {
            $this->setCondition('affiliate_id', new \MongoId( (string) $filter_affiliate_id ) );
        }
    
        $filter_recipient_email = $this->getState('filter.recipient_email');
        if (strlen($filter_recipient_email))
        {
            $this->setCondition('recipient_email', $filter_recipient_email );
        }
    
        return $this;
    }
    
    public function beforeCreate()
    {
        // Prevent the same email from receiving multiple requests from this affiliate
        $invited = (new static)->setState('filter.recipient_email', $this->recipient_email)->setState('filter.affiliate_id', $this->affiliate_id)->getItem();
        if (!empty($invited->id)) 
        {
        	$this->setError('An invite has already been sent to this recipient');
        }
        
        return parent::beforeCreate();
    }
    
    protected function afterCreate()
    {
        parent::afterCreate();
        
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

    /**
     * Checks whether or not the invite_id is valid
     *
     * @param unknown $invite_id
     * @return boolean
     */
    public static function idValid( $invite_id )
    {
        $model = (new static)->setState('filter.id', $invite_id)->getItem();
        if (!empty($model->id))
        {
            return $model;
        }
         
        return false;
    }
}