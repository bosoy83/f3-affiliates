<?php
namespace Affiliates\Listeners;

class Users extends \Dsc\Singleton
{
    public function afterCreateUsersModelsUsers( $event )
    {
        $user = $event->getArgument('model');
        
        $app = \Base::instance();
        $cookie_affiliate_id = \Dsc\Cookie::get('affiliate_id');
        $cookie_invite_id = \Dsc\Cookie::get('invite_id');
        // TODO Also check the session
        
        // If there is an affiliate_id in a cookie or the session,
        // add it to this user record for processing after the user's email is validated
        if (!empty($cookie_affiliate_id)) 
        {
            $user->{'affiliates.validation_triggers_referral'} = $cookie_affiliate_id;
            $user = $user->store(); 
        } 
        
        elseif (!empty($cookie_invite_id)) 
        {
            $user->{'affiliates.validation_triggers_referral_invite'} = $cookie_invite_id;
            $user = $user->store();            
        }        
    }
        
    public function beforeSaveUsersModelsUsers( $event )
    {
        $user = $event->getArgument('model');
        
        if (!empty($user->id)) 
        {
            $db_user = (new \Users\Models\Users)->load(array('_id'=> new \MongoId( (string) $user->id ) ));
            if (!$db_user->active && $user->active)
            {
                // the user record has been validated, so trigger any pending referrals after the user is saved
                if (!empty($db_user->{'affiliates.validation_triggers_referral'}))
                {
                    $this->db_user = $db_user;
                }
                elseif(!empty($db_user->{'affiliates.validation_triggers_referral_invite'}))
                {
                    $this->db_user = $db_user;
                }
            }            
        }
    }
    
    public function afterSaveUsersModelsUsers( $event )
    {
        $identity = $event->getArgument('model');
    
        // if the active status of the user record from the DB (grabbed in beforeSave above)
        // differs from the active status of the $user, and $user->active = true
        // grant the affiliate a referral credit
        if (!empty($this->db_user)) 
        {
            if ($affiliate_id = $this->db_user->{'affiliates.validation_triggers_referral'}) 
            {
                // Make the user into a referral for affiliate_id
                // \Dsc\System::addMessage('Making you into a referral for an Affiliate ID');
                try
                {
                    $referral = \Affiliates\Models\Referrals::isUser($identity->id);
                    if (empty($referral->id))
                    {
                        // is this a valid affiliate?
                        $affiliate = (new \Users\Models\Users)->load(array('_id'=> new \MongoId((string)$affiliate_id)));
                        if (!empty($affiliate->id))
                        {
                            // make them into a referral
                            $referral = new \Affiliates\Models\Referrals;
                            $referral->bind(array(
                                'referral_user_id' => $identity->id,
                                'referral_name' => $identity->fullName(),
                                'referral_email' => $identity->email,
                                'referral_fingerprints' => (array) $identity->{'affiliates.fingerprints'},
                                'affiliate_fingerprints' => (array) $affiliate->{'affiliates.fingerprints'},
                                'affiliate_id' => $affiliate_id
                            ))->save();
                
                            /*
                             \Affiliates\Models\Referrals::createCommission($referral->id);
                            */
                            \Dsc\Queue::task('\Affiliates\Models\Referrals::createCommission', array('id'=>$referral->id), array(
                                'title' => 'Verify and create commission for referral: ' . $referral->referral_email
                            ));
                        }
                    }
                
                    // referral created, clear it from the user
                    $identity->{'affiliates.validation_triggers_referral'} = null;
                    $identity->{'affiliates.validation_triggers_referral_invite'} = null;
                    $identity = $identity->store();
                
                }
                catch (\Exception $e)
                {
                    // Log the failure in the system logger
                    $identity->log('Could not create referral for affiliate ' . $affiliate_id . ' ('. $affiliate->email .') for referral of user ' . $identity->id . ' ('. $identity->email .') because of error ' . $e->getMessage() );
                    $identity->{'affiliates.validation_triggers_referral'} = null;
                    $identity->{'affiliates.validation_triggers_referral_invite'} = null;
                    $identity = $identity->store();
                }                
            }
            
            elseif ($invite_id = $this->db_user->{'affiliates.validation_triggers_referral_invite'}) 
            {
                try
                {
                    if ($invite = \Affiliates\Models\Invites::idValid($invite_id))
                    {
                        $affiliate = (new \Users\Models\Users)->load(array('_id'=> new \MongoId((string)$invite->affiliate_id)));
                        if (!empty($affiliate->id))
                        {
                            // make them into a referral
                            $referral = new \Affiliates\Models\Referrals;
                            $referral->bind(array(
                                'referral_user_id' => $identity->id,
                                'referral_name' => $identity->fullName(),
                                'referral_email' => $invite->recipient_email,
                                'referral_fingerprints' => (array) $identity->{'affiliates.fingerprints'},
                                'affiliate_fingerprints' => (array) $affiliate->{'affiliates.fingerprints'},
                                'affiliate_id' => $invite->affiliate_id,
                                'affiliate_email' => $affiliate->email,
                                'invite_id' => $invite_id,
                            ))->save();
                
                            /*
                             \Affiliates\Models\Referrals::createCommission($referral->id);
                            */
                            \Dsc\Queue::task('\Affiliates\Models\Referrals::createCommission', array('id'=>$referral->id), array(
                                'title' => 'Verify and create commission for referral: ' . $referral->referral_email
                            ));
                
                        }
                    
                        // referral created, clear it from the user
                        $identity->{'affiliates.validation_triggers_referral'} = null;
                        $identity->{'affiliates.validation_triggers_referral_invite'} = null;
                        $identity = $identity->store();
                    }
                }
                catch (\Exception $e)
                {
                    // Log the failure in the system logger
                    $identity->log('Could not create referral for invite ' . $invite_id . ' for referral of user ' . $identity->id . ' ('. $identity->email .') because of error ' . $e->getMessage() );
                    $identity->{'affiliates.validation_triggers_referral'} = null;
                    $identity->{'affiliates.validation_triggers_referral_invite'} = null;
                    $identity = $identity->store();
                }
            }
        }
    }    
}