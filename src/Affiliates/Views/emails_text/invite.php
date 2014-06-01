<?php $link = $SCHEME . '://' . $HOST . $BASE . '/sign-in?affiliate_id=' . $invite->affiliate_id; ?>

<?php echo $invite->sender_name; ?> has sent you an invitation. 

<?php echo $invite->message; ?> 

Visit by opening this link in your browser: <?php echo $link; ?> 
