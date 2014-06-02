<?php $link = $SCHEME . '://' . $HOST . $BASE . '/affiliate/' . $this->auth->getIdentity()->id; ?>
<?php $settings = \Affiliates\Models\Settings::fetch(); ?>
<?php $encoded_link = urlencode($link); ?>
            
<div class="container">
    <ol class="breadcrumb">
        <li>
            <a href="./user">My Account</a>
        </li>
        <li class="active">My Affiliate Account</li>
    </ol>
    
    <h2>
        <small>Hello, <?php echo $this->auth->getIdentity()->username; ?><br/></small>
        Your Affiliate Account
        <a href="./affiliate/invite-friends" class="btn btn-primary pull-right">Invite Friends</a>
    </h2>
    
    <hr/>
    
    <div><?php echo $settings->{'dashboard_header'}; ?></div>
    
    <div><h4>Your earnings: <?php echo \Shop\Models\Currency::format( 0 ); ?></h4></div>
    
    <tmpl type="modules" name="above-affiliate-dashboard" />
    
    <div class="row">
        <div class="col-md-4">
            <div class="well well-sm well-light text-center"><h5>Referrals:<br/><?php echo 0; ?></h5></div>
        </div>
        <div class="col-md-4">
            <div class="well well-sm well-light text-center"><h5>Invitations Sent:<br/><?php echo 0; ?></h5></div>
        </div>
        <div class="col-md-4">
            <div class="well well-sm well-light text-center"><h5>Invites who haven't joined:<br/><?php echo 0; ?></h5></div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
        
            <h4><a href="./affiliate/invite-friends">Invite your friends:</a></h4>

            <ul class="list-group">
                <li class="list-group-item">
                    <a class="btn btn-default" href="./affiliate/invite-friends/email"><i class="fa fa-envelope"></i> <span>Send an email</span></a>
                </li>
                <li class="list-group-item">
                    <a class="btn btn-default" href="./affiliate/invite-friends/link"><i class="fa fa-external-link"></i> Share your affiliate URL</a>
                </li>
                
                <?php if ($settings->isSocialProviderEnabled('facebook') ) { ?>
                <?php $fb_app_id = $settings->{'social.providers.Facebook.keys.id'}; ?>
                <?php $fb_redirect_uri = $SCHEME . '://' . $HOST . $BASE . '/affiliate/share/thanks'; ?>
                <li class="list-group-item">
                    <a class="btn btn-default" href="javascript:void(0);" onclick="window.open('https://www.facebook.com/dialog/share?app_id=<?php echo $fb_app_id; ?>&display=popup&href=<?php echo $encoded_link; ?>&redirect_uri=<?php echo $fb_redirect_uri; ?>', '_blank', 'width=520,height=570'); return false;"><i class="fa fa-facebook"></i> Invite your Facebook friends</a>
                </li>
                <?php } ?>
                
                <?php if ($settings->isSocialProviderEnabled('twitter') ) { ?>
                <?php $default_message = $settings->{'social.providers.Twitter.default_message'}; ?>
                <li class="list-group-item">
                    <script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>
                    <a class="btn btn-default" target="_blank" href="https://twitter.com/intent/tweet?url=<?php echo $encoded_link; if ($default_message) { echo '&text=' . $default_message; } ?>"><i class="fa fa-twitter"></i> Invite your Twitter followers</a>
                </li>
                <?php } ?>
                
                <?php if ($settings->isSocialProviderEnabled('linkedin') ) { ?>
                <?php $default_title = urlencode( trim($settings->{'social.providers.LinkedIn.default_title'}) ); ?>
                <?php $default_message = urlencode( trim($settings->{'social.providers.LinkedIn.default_message'}) ); ?>
                <li class="list-group-item">
                    <a class="btn btn-default" href="javascript:void(0);" onclick="window.open('http://www.linkedin.com/shareArticle?mini=true&amp;url=<?php echo $encoded_link; if ($default_title) { echo '&title=' . $default_title; } if ($default_message) { echo '&summary=' . $default_message; }?>', '_blank', 'width=520,height=570'); return false;"><i class="fa fa-linkedin"></i> Invite your LinkedIn connections</a>
                </li>
                <?php } ?>
                
                <?php if ($settings->isSocialProviderEnabled('google') ) { ?>
                <li class="list-group-item">
                    <a class="btn btn-default" href="https://plus.google.com/share?url=<?php echo $encoded_link; ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600'); return false;"><i class="fa fa-google"></i> Invite your Google+ followers</a>
                </li>
                <?php } ?>                
            </ul>   
                 
        </div>
        
        <div class="col-md-8">
            <h4>Your affiliate URL is: </h4>
            <div class="well well-sm"><?php echo $link; ?></div>
            
            <tmpl type="modules" name="right-affiliate-dashboard" />
            
        </div>        
        
    </div>
</div>