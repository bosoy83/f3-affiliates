<link href="//cdnjs.cloudflare.com/ajax/libs/select2/3.4.8/select2.css" type="text/css" rel="stylesheet">
<link href="//cdnjs.cloudflare.com/ajax/libs/select2/3.4.8/select2-bootstrap.css" type="text/css" rel="stylesheet">
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/3.4.8/select2.min.js"></script>

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
        <li class="active">By Email</li>
    </ol>
    
    <h3>
        Send an email invitation
    </h3>
    
    <hr/>
    
    <div class="row">
        <div class="col-md-4">
            <form method="post">
                <div class="form-group">
                    <label>Your Name</label>
                    <input class="form-control" name="sender_name" placeholder="Your Name" value="<?php echo $this->flash->old('sender_name'); ?>" type="text" required />
                </div>
                
                <div class="form-group">
                    <label>Your Email Address</label>
                    <input class="form-control" name="sender_email" placeholder="Your Email Address" value="<?php echo $this->flash->old('sender_email'); ?>" type="email" required />
                </div>
        
                <div class="form-group">
                    <label>Recipient Email Addresses (10 max)</label>
                    <input id="recipients" class="select2 form-control" data-maximum="10" name="recipients" placeholder="Recipient Email Addresses" value="<?php echo $this->flash->old('recipients'); ?>" />
                    <p class="help-block">Separate multiple emails with commas</p>
                </div>
        
                <div class="form-group">
                    <label>Personal Message</label>
                    <textarea rows="10" required class="form-control" name="message"><?php echo $this->flash->old('message'); ?></textarea>
                </div>
                
                <p class="help-block"><b>Note:</b> We will automatically add your affiliate URL to the bottom of the invitation.</p>
                
                <div class="form-group">
                    <button class="btn btn-lg btn-primary" type="submit">Send</button>
                    <a class="btn btn-link" href="./affiliate/invite-friends">Cancel</a>
                </div>
            </form>        
        </div>
        
        <div class="col-md-4">
            
        </div>
        
    </div>
    
</div>

<script>
jQuery(document).ready(function() {
    
    jQuery("#recipients").select2({
        allowClear: true, 
        placeholder: "Recipient Email Addresses",
        multiple: true,
        maximumSelectionSize: 10,
        minimumInputLength: 3,
        tokenSeparators: [",", ";", " "],
        tags: []
    });

});
</script>