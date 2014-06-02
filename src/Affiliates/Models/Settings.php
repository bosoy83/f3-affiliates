<?php 
namespace Affiliates\Models;

class Settings extends \Dsc\Mongo\Collections\Settings
{
    protected $__type = 'affiliates.settings';
    
    public $general = array(
    	'default_message' => null
    );
    
    public $social = array(
        'providers' => array(
            'Facebook' => array(
                'enabled' => 1,
                'keys' => array(
                    'id' => null,
                )
            ),
            'Twitter' => array(
                'enabled' => 1,
                'default_message' => null,
            ),
            'Google' => array(
                'enabled' => 1,
            ),
            'LinkedIn' => array(
                'enabled' => 1,
                'default_title' => null,
                'default_message' => null,
            ),
        )
    );
    
    public function isSocialProviderEnabled($provider=null)
    {
        $result = false;
        switch ($provider)
        {
        	case 'facebook':
        	    $result = $this->{'social.providers.Facebook.enabled'} && $this->{'social.providers.Facebook.keys.id'};
        	    break;
        	case 'twitter':
        	    $result = $this->{'social.providers.Twitter.enabled'};
        	    break;
        	case 'linkedin':
        	    $result = $this->{'social.providers.LinkedIn.enabled'};
        	    break;
        	case 'google':
        	    $result = $this->{'social.providers.Google.enabled'};
        	    break;
        	case null:
        	    // are ANY of the social providers enabled?
        	    $enabled = $this->enabledSocialProviders();
        	    if (!empty($enabled)) {
        	        $result = true;
        	    }
        	    break;
        	default:
        	    break;
        }
    
        return $result;
    }
    
    public function enabledSocialProviders()
    {
        $providers = array();
        foreach ((array) $this->{'social.providers'} as $network => $opts)
        {
            if ($this->isSocialProviderEnabled(strtolower($network)))
            {
                $providers[] = $network;
            }
        }
        return $providers;
    }
}