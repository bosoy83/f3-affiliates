<div class="container order-history">

    <ol class="breadcrumb">
        <li>
            <a href="./user">My Account</a>
        </li>    
        <li>
            <a href="./affiliate/dashboard">My Affiliate Account</a>
        </li>
        <li class="active">My Invite History</li>
    </ol>

    <?php if (empty($paginated->items)) { ?>
        <h2>You have sent no invitations. <a href="./affiliate/invite-friends"><small>Invite Friends</small></a></h2>
    <?php } else { ?>
    
        <form action="./affiliate/invite-history" method="post">     
           
            <div class="well well-sm search">
                <div class="input-group">
                    <input class="form-control" type="text" name="filter[keyword]" placeholder="Search..." maxlength="200" value="<?php echo $state->get('filter.keyword'); ?>"> 
                    <span class="input-group-btn">
                        <input class="btn btn-primary" type="submit" onclick="this.form.submit();" value="Search" />
                        <button class="btn btn-danger" type="button" onclick="Dsc.resetFormFilters(this.form);">Reset</button>
                    </span>
                </div>
            </div>
        
        </form>
        
        <div class="row form-group">
            <div class="col-xs-12 col-sm-5 col-md-3 col-lg-3">

            </div>    
            <div class="col-xs-12 col-sm-7 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">
                <div class="pull-right">
                    <?php if (!empty($paginated->total_pages) && $paginated->total_pages > 1) { ?>
                        <?php echo $paginated->serve(); ?>
                    <?php } ?>
                </div>           
            </div>
        </div>        
        
        <div class="list-group">
            <div class="list-group-item">
                <div class="row ">
                    <div class="col-md-5">
                        Friend
                    </div>
                    <div class="col-md-4">
                        Invited
                    </div>              
                    <div class="col-md-3">
                        Status
                    </div>              
                </div>
            </div>            
            
            <?php foreach ($paginated->items as $item) { ?>
            
                <div class="list-group-item">
                    <div class="row">
                        <div class="col-md-5">
                            <small><?php echo $item->recipient_email; ?></small>
                        </div>
                        <div class="col-md-4">
                            <small><?php echo date('Y-m-d', $item->{'metadata.created.time'} ); ?></small>
                        </div>              
                        <div class="col-md-3">
                            <small><?php echo $item->status; ?></small>
                        </div>              
                    </div>
                </div>
    
            <?php } ?>
        
        </div>
        
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
                <?php if (!empty($paginated->total_pages) && $paginated->total_pages > 1) { ?>
                    <?php echo $paginated->serve(); ?>
                <?php } ?>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                <div class="datatable-results-count pull-right">
                    <span class="pagination">
                        <?php echo (!empty($paginated->total_pages)) ? $paginated->getResultsCounter() : null; ?>
                    </span>
                </div>
            </div>
        </div>        
            
    <?php } ?>
</div>