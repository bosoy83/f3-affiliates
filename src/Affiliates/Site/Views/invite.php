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
            <ul class="list-group">
                <li class="list-group-item">
                    <a class="btn btn-default" href="./affiliate/invite-friends/email"><i class="fa fa-envelope"></i> <span>Send an email</span></a>
                </li>
                <li class="list-group-item">
                    <a class="btn btn-default" href="./affiliate/invite-friends/link"><i class="fa fa-external-link"></i> Share your personal invite link</a>
                </li>
                <li class="list-group-item">
                    <a class="btn btn-default" href="./affiliate/invite-friends/facebook"><i class="fa fa-facebook"></i> Post on Facebook</a>
                </li>
                <li class="list-group-item">
                    <a class="btn btn-default" href="./affiliate/invite-friends/twitter"><i class="fa fa-twitter"></i> Post on Twitter</a>
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