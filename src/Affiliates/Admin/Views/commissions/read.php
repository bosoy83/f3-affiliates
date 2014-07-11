<div class="row">
	<div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
		<h1 class="page-title txt-color-blueDark">
			<i class="fa fa-table fa-fw "></i> 
				<a href="./admin/affiliates/commissions">Commissions</a> 
			<span> > 
				<?php echo $item->id; ?>
			</span>
		</h1>
	</div>
	<div class="col-xs-12 col-sm-5 col-md-5 col-lg-8">
        <ul id="sparks" class="list-actions list-unstyled list-inline">
            <li>
                <a class="btn btn-default" href="./admin/affiliates/commissions">Close</a>
            </li>
        </ul>
	</div>
</div>

<hr />

<div class="row">
    <div class="col-md-9">
        <div class="well">
        
        <div class="row">
            <div class="col-md-3">
                <h2 class="text-center"><span class="label-lg label <?php echo $item->issued ? 'label-success' : 'label-warning'; ?>"><?php echo $item->issued ? 'Issued' : 'Not Issued'; ?></span></h2>
            </div>            
            <div class="col-md-3">
                <div class="well well-sm well-light text-center"><h5><small>Amount</small><br/><?php echo \Shop\Models\Currency::format( $item->amount ); ?></h5></div>
            </div>
            <div class="col-md-3">
                <div class="well well-sm bg-color-darken txt-color-white text-center"><h5><small>Balance before:</small><br/><?php echo \Shop\Models\Currency::format( $item->balance_before ); ?></h5></div>
            </div>
            <div class="col-md-3">
                <div class="well well-sm bg-color-darken txt-color-white text-center"><h5><small>Balance after:</small><br/><?php echo \Shop\Models\Currency::format( $item->balance_after ); ?></h5></div>
            </div>
        </div>
        
        <hr/>
        
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">Affiliate</div>
                    <div class="panel-body">
                        <p><?php echo $item->affiliateName(); ?></p>
                        <p><?php echo $item->affiliate()->email; ?></p>
                    </div>            
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Referral
                        <span>[link]</span>                    
                    </div>
                    <div class="panel-body">
                        <p><?php echo $item->referral()->fullName(); ?></p>
                        <p><?php echo $item->referral()->email; ?></p>
                    </div>            
                </div>            
            </div>            
        </div>        
    
        <div class="panel panel-default">
            <div class="panel-heading">History</div>
            <div class="panel-body">
                <ul class="list-group">
                <?php foreach ($item->history as $history) { ?>
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-md-2">
                                <?php echo \Dsc\ArrayHelper::get( $history, 'created.local' ); ?>
                            </div>
                            <div class="col-md-10">
                                <?php $dump = $history; unset( $dump['created'] ); ?>
                                <?php echo \Dsc\Debug::dump( $dump ); ?>
                            </div>
                        </div>
                    </li>
                <?php } ?>
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-md-2">
                                <?php echo (new \DateTime($item->{'metadata.created.local'}))->format('F j, Y g:ia'); ?>
                            </div>
                            <div class="col-md-10">
                                Created
                            </div>
                        </div>
                    </li>                    
                </ul>            
            </div>            
        </div>        
    
        <?php /* ?>
        <div class="panel panel-default">
            <div class="panel-heading">Campaigns</div>
            <div class="panel-body">
                <?php $item->checkCampaigns(); ?>
                <div class="list-group">
                <?php foreach ((array) $item->{'shop.active_campaigns'} as $active_campaign) { ?>
                    <div class="list-group-item">
                        <?php echo \Dsc\ArrayHelper::get( $active_campaign, 'title'); ?> 
                        as of <span class="label label-default"><?php echo date( 'Y-m-d', \Dsc\ArrayHelper::get( $active_campaign, 'activated.time') ); ?></span>
                        expiring on <span class="label label-default"><?php echo date( 'Y-m-d', \Dsc\ArrayHelper::get( $active_campaign, 'expires.time') ); ?></span>  
                    </div>    
                <?php } ?>
                </div>
            </div>
        </div>
        
        <div class="panel panel-default">
            <div class="panel-heading">[List of Customer Orders?]</div>
            <div class="panel-body">
                List of X recent orders with a link to complete orders list filtered for user
            </div>            
        </div>
        
        <div class="panel panel-default">
            <div class="panel-heading">[Admin-only notes about customer]</div>
            <div class="panel-body">
                Add notes to customer
            </div>            
        </div>        

        <div class="panel panel-default">
            <div class="panel-heading">[Groups]</div>
            <div class="panel-body">
                Add groups to customer
            </div>
        </div>
                
        <div class="panel panel-default">
            <div class="panel-heading">[Tags]</div>
            <div class="panel-body">
                Add tags to customer
            </div>
        </div>
        */ ?>
        
        </div>
    </div>
    
    <div class="col-md-3">
        <?php if (empty($item->issued)) { ?>
        <p>
            <a class="btn btn-success" href="./admin/affiliates/commission/issue/<?php echo $item->id; ?>">Issue</a>
        </p>
        <?php } else { ?>
        <p>
            <a class="btn btn-warning" href="./admin/affiliates/commission/revoke/<?php echo $item->id; ?>">Revoke</a>
        </p>           
        <?php } ?> 
        <p>
            <a class="btn btn-danger" href="./admin/affiliates/commission/delete/<?php echo $item->id; ?>">Delete record</a>
        </p>
    
        <?php /* ?>
        <div class="panel panel-default">
            <div class="panel-heading">Details</div>
            <div class="panel-body">
                <div class="list-group">
                    <div class="list-group-item list-group-item-success">
                        <label>Total spent:</label> <b><?php echo \Shop\Models\Currency::format( $item->totalSpent() ); ?></b>
                    </div>
                    <div class="list-group-item list-group-item-info">
                        <label>Last 365 days:</label> <b><?php echo \Shop\Models\Currency::format( $item->fetchTotalSpent( date('Y-m-d', strtotime( 'today -1 year') ) ) ); ?></b>
                    </div>                    
                    <div class="list-group-item">
                        <label>Total orders:</label> <?php echo (int) $item->ordersCount(); ?>
                    </div>
                    <div class="list-group-item"><label>Total credit:</label> <?php echo \Shop\Models\Currency::format( $item->{'shop.credits.balance'} ); ?></div>
                    <div class="list-group-item"><label>Last Visit:</label> <?php echo date( 'Y-m-d', $item->{'last_visit.time'} ); ?></div>                    
                    <div class="list-group-item"><label>Registered:</label> <?php echo date( 'Y-m-d', $item->{'metadata.created.time'} ); ?></div>
                </div>
            </div>
            <div class="panel-footer">
                <a class="btn btn-xs btn-warning" href="./admin/shop/customer/refreshtotals/<?php echo $item->id; ?>">Refresh Totals</a>
            </div>
        </div>
            
        <div class="panel panel-default">
            <div class="panel-heading">Contact Info</div>
            <div class="panel-body">
                <ul class="list-unstyled">
                    <?php if ($address = $item->primaryAddress()) { ?>
                    <li>
                        <address>
                            <?php echo $address; ?>
                        </address>
                    </li>
                    <li><i class="fa fa-phone"></i> <?php echo $address->phone_number; ?></li>
                    <?php } ?>
                    <li><i class="fa fa-envelope-o"></i> <?php echo $item->email; ?></li>
                    
                </ul>
            </div>
        </div>
        */ ?>
    </div>
</div>

<?php if ($this->event->getArgument('tabs')) { ?>
    <hr />
    
    <ul class="nav nav-tabs">
        <?php foreach ((array) $this->event->getArgument('tabs') as $key => $title ) { ?>
        <li class="<?php if (empty($key)) { echo "active"; } ?>">
            <a href="#tab-<?php echo $key; ?>" data-toggle="tab"> <?php echo $title; ?> </a>
        </li>
        <?php } ?>
    </ul>
    
    <div class="tab-content">
        
        <?php foreach ((array) $this->event->getArgument('content') as $key => $content ) { ?>
        <div class="tab-pane <?php if (empty($key)) { echo "active"; } ?>" id="tab-<?php echo $key; ?>">
            <?php echo $content; ?>
        </div>
        <?php } ?>
        
    </div>
    <!-- /.tab-content -->
    
<?php } ?>