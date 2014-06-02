<?php 
namespace Affiliates\Site\Controllers;

class Invite extends \Dsc\Controller 
{
    public function beforeRoute()
    {
        $this->requireIdentity();
    }
        
    public function index()
    {
    	$this->app->set('meta.title', 'Invite Friends | Affiliates');
    	
    	echo $this->theme->renderTheme('Affiliates/Site/Views::invite.php');
    }
    
    public function email()
    {
        $settings = \Affiliates\Models\Settings::fetch();
        
        $flash_filled = \Dsc\System::instance()->getUserState('invite_friends.email.flash_filled');
        if (!$flash_filled) {
            $this->flash->store(array(
            	'sender_name' => $this->auth->getIdentity()->fullName(),
                'sender_email' => $this->auth->getIdentity()->email,
                'message' => $settings->{'general.default_message'},
            ));
        }
        \Dsc\System::instance()->setUserState('invite_friends.email.flash_filled', false);
        
        $this->app->set('meta.title', 'Invite Friends by Email | Affiliates');
         
        echo $this->theme->renderTheme('Affiliates/Site/Views::invite/email.php');
    }
    
    public function emailSubmit()
    {
        // Validate the form inputs
        // for each email address, send the email
        // track that this user sent these invitations
        // redirect back to /invite-friends/email
        $recip_input = $this->app->split( $this->input->get( 'recipients', null, 'string' ) );
        $recipients = array();
        foreach ($recip_input as $recip) 
        {
            $recip = trim( strtolower( $recip ) );
            if (!empty($recip) && \Mailer\Factory::instance()->sender()->isEmailAddress($recip)) 
            {
                $recipients[] = $recip; 
            }
        }
            
        $data = array(
            'sender_name' => $this->input->get( 'sender_name', null, 'string' ),
            'sender_email' => trim( strtolower( $this->input->get( 'sender_email', null, 'string' ) ) ),
            'recipients' => $recipients,
            'message' => $this->input->get( 'message', null, 'string' ),
        );
        
        try
        {
            if (empty($data['sender_email']) || !\Mailer\Factory::instance()->sender()->isEmailAddress($data['sender_email']))
            {
                throw new \Exception('Your email address is invalid');
            }
            
            if (empty($data['sender_name']))
            {
                throw new \Exception('Your name is invalid');
            }

            if (empty($data['recipients']))
            {
                throw new \Exception('Invalid recipient email(s)');
            }
            
            if (empty($data['message']))
            {
                throw new \Exception('Invalid message');
            }

            foreach ($data['recipients'] as $key=>$recipient) 
            {
                try {
                    (new \Affiliates\Models\Invites)->bind(array(
                        'affiliate_id' => $this->getIdentity()->id,
                        'sender_email' => $data['sender_email'],
                        'sender_name' => $data['sender_name'],
                        'recipient_email' => $recipient,
                        'message' => $data['message'],
                    ))->set('__send_email', true)->save();
                     
                    unset($data['recipients'][$key]);
                     
                    \Dsc\System::addMessage( 'Invitation sent to ' . $recipient, 'success' );                	
                }
                catch (\Exception $e) 
                {
                    \Dsc\System::addMessage( 'Invitation not sent to ' . $recipient, 'warning' );
                    \Dsc\System::addMessage( $e->getMessage(), 'warning' );
                }
            }
        
        }
        catch( \Exception $e )
        {
            \Dsc\System::addMessage( 'Failed to send invitation(s)', 'error' );
            \Dsc\System::addMessage( $e->getMessage(), 'error' );
             
            \Dsc\System::instance()->setUserState('invite_friends.email.flash_filled', true);
            $this->flash->store($data);
             
            $this->app->reroute('/affiliate/invite-friends/email');
        }        
        
        $this->flash->store(array());
        
        $this->app->reroute('/affiliate/invite-friends/email');
    }
}