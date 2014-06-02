<div class="container">
    <ol class="breadcrumb">
        <li>
            <a href="./affiliate/dashboard">My Affiliate Account</a>
        </li>
        <li class="active">Invite Friends</li>
    </ol>
    
    <h3>
        Invite friends
    </h3>
    
    <hr/>

    <tmpl type="modules" name="above-invite-friend" />
    
    <?php 
    $left = \Modules\Factory::render( 'left-invite-friend', \Base::instance()->get('PARAMS.0') ); 
    $right = \Modules\Factory::render( 'right-invite-friend', \Base::instance()->get('PARAMS.0') );
    $width = '12';
    if ($left && $right) {
    	$width = '4';
    } elseif (($left && !$right) || ($right && !$left)) {
    	$width = '8';
    }
    ?>
    
    <div class="row">
        <?php if ($left) { ?>
        <div class="col-md-4">
            <?php echo $left; ?>
        </div>
        <?php } ?>
        <div class="col-md-<?php echo $width; ?>">
            <h4>Your affiliate URL is: </h4>
            <div class="well well-sm"><?php echo $link = $SCHEME . '://' . $HOST . $BASE . '/affiliate/' . $this->auth->getIdentity()->id; ?></div>
            
            <?php $encoded_link = urlencode($link); ?>
            <ul class="list-group">
                <li class="list-group-item">
                    <a class="btn btn-default" href="./affiliate/invite-friends/email"><i class="fa fa-envelope"></i> <span>Send an email</span></a>
                </li>
                <li class="list-group-item">
                    <a class="btn btn-default" href="./affiliate/invite-friends/link"><i class="fa fa-external-link"></i> Share your affiliate URL</a>
                </li>
                <?php $fb_app_id = '145634995501895'; ?>
                <?php $fb_redirect_uri = $SCHEME . '://' . $HOST . $BASE . '/affiliate/share/thanks'; ?>
                <li class="list-group-item">
                    <a class="btn btn-default" href="javascript:void(0);" onclick="window.open('https://www.facebook.com/dialog/share?app_id=<?php echo $fb_app_id; ?>&display=popup&href=<?php echo $encoded_link; ?>&redirect_uri=<?php echo $fb_redirect_uri; ?>', '_blank', 'width=520,height=570'); return false;"><i class="fa fa-facebook"></i> Invite your Facebook friends</a>
                </li>
                <li class="list-group-item">
                    <script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>
                    <a class="btn btn-default" target="_blank" href="https://twitter.com/intent/tweet?url=<?php echo $encoded_link; ?>"><i class="fa fa-twitter"></i> Invite your Twitter followers</a>
                </li>
                <li class="list-group-item">
                    <a class="btn btn-default" href="javascript:void(0);" onclick="window.open('http://www.linkedin.com/shareArticle?mini=true&amp;url=<?php echo $encoded_link; ?>', '_blank', 'width=520,height=570'); return false;"><i class="fa fa-linkedin"></i> Invite your LinkedIn connections</a>
                </li>
                <li class="list-group-item">
                    <a class="btn btn-default" href="https://plus.google.com/share?url=<?php echo $encoded_link; ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600'); return false;"><i class="fa fa-google"></i> Invite your Google+ followers</a>
                </li>                
            </ul>
        </div>
        <?php if ($right) { ?>
        <div class="col-md-4">
            <?php echo $right; ?>
        </div>
        <?php } ?>
    </div>
    
    <tmpl type="modules" name="below-invite-friend" />
</div>