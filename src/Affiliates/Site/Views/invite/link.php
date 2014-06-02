<div class="container">
    <ol class="breadcrumb">
        <li>
            <a href="./user">My Account</a>
        </li>    
        <li>
            <a href="./affiliate/dashboard">My Affiliate Account</a>
        </li>
        <li>
            <a href="./affiliate/invite-friends">Invite Friends</a>
        </li>        
        <li class="active">By sharing your affiliate URL</li>
    </ol>
    
    <h3>
        Share your affiliate URL
        <a class="btn btn-link pull-right" href="./affiliate/invite-friends"><i class="fa fa-chevron-left"></i> Back</a>
    </h3>
    
    <hr/>
    
    <div class="row">
        <div class="col-md-8">
            <ol>
                <li>
                    <p>Copy your affiliate URL below.</p>
                    <p><input type="text" value="<?php echo $SCHEME . '://' . $HOST . $BASE . '/affiliate/' . $this->auth->getIdentity()->id; ?>" class="form-control"></p> 
                </li>
                <li>
                    <p>Paste it into an email, Tweet, blog entry, or Facebook post and get your friends to use the link to create an account on our site.</p>
                </li>
                <li>
                    <p>That's it!</p>
                </li>
            </ol>      
        </div>        
    </div>
    
</div>