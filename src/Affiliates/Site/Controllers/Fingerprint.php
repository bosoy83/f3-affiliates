<?php 
namespace Affiliates\Site\Controllers;

class Fingerprint extends \Dsc\Controller 
{
    public function index()
    {
        $fingerprint = $this->inputfilter->clean($this->app->get('PARAMS.id'), 'alnum');
        
        $identity = $this->getIdentity();
        if (!empty($identity->id)) 
        {
            $identity = $identity->reload();
            $fingerprints = (array) $identity->{'affiliates.fingerprints'};
            
            // is this a new fingerprint?
            if (!in_array($fingerprint, $fingerprints)) 
            {
                $fingerprints[] = $fingerprint;
                $identity->{'affiliates.fingerprints'} = $fingerprints;
                $identity->save();
                
                // is the user an affiliate?  if so, add their fingerprint to all of their referrals
                $update = \Affiliates\Models\Referrals::collection()->update(array(
                    'affiliate_id' => $identity->id
                ), array(
                    '$addToSet' => array( 'affiliate_fingerprints' => $fingerprint )
                ), array(
                    'multiple' => true
                ));
                
                \Dsc\Queue::task('\Affiliates\Models\Referrals::checkFingerprints', array('id'=>$identity->id), array(
                    'title' => 'Checking browser fingerprints in referrals from affiliate: ' . $identity->fullName()
                ));

                // is the user a referral?  if so, add the fingerprint to their referral record
                if ($referral = \Affiliates\Models\Referrals::isUser( $identity->id ))
                {
                    $fingerprints = (array) $referral->referral_fingerprints;
                    if (!in_array($fingerprint, $fingerprints))
                    {
                        $fingerprints[] = $fingerprint;
                        $referral->referral_fingerprints = $fingerprints;
                        try
                        {
                            $referral->save();
                        }
                        catch (\Exception $e)
                        {
                            $referral->log( $e->getMessage(), 'ERROR', 'Affiliates\Site\Controllers\Fingerprint.saveReferral');
                        }
                    }
                }
            }
        }
        
        // the user not yet identified, so we don't care about the fingerprint.  
        // we only use it to ensure commissions are not granted for fake referrals 
        else 
        {
            
        }
        
    }
}