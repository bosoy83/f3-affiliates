<div class="container">
    <ol class="breadcrumb">
        <li>
            <a href="./affiliate/dashboard">My Affiliate Account</a>
        </li>
        <li>
            <a href="./affiliate/invite-friends">Invite Friends</a>
        </li>        
        <li class="active">By Sharing Your Invite Link</li>
    </ol>
    
    <h3>
        Share your personal invite link
    </h3>
    
    <hr/>
    
    <div class="row">
        <div class="col-md-8">
            <ol>
                <li>
                    <p>Copy your personal invite link below.</p>
                    <p><input type="text" value="<?php echo $SCHEME . '://' . $HOST . $BASE . '/affiliate/' . $this->auth->getIdentity()->id; ?>" class="form-control"> 
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
    
    <a class="btn btn-link" href="./affiliate/invite-friends"><i class="fa fa-chevron-left"></i> Back</a>
    
</div>