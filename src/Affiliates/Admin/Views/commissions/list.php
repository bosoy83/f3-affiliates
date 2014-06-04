<div class="row">
	<div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
		<h1 class="page-title txt-color-blueDark">
			<i class="fa fa-table fa-fw "></i> 
				Commissions 
			<span> > 
				List
			</span>  
		</h1>
	</div>
	<div class="col-xs-12 col-sm-5 col-md-5 col-lg-8">
        <ul id="sparks" class="list-actions list-unstyled list-inline">
            <li>
                <a class="btn btn-success" href="./admin/affiliates/commission/create">Add New</a>
            </li>        
        </ul>            	
	</div>
</div>

<form method="post" action="./admin/affiliates/commissions">

        <div class="row">
            <div class="col-xs-12 col-sm-5 col-md-5 col-lg-8">

                <ul class="list-filters list-unstyled list-inline">
                    <li>
                        
                    </li>                
                    <li>
                        
                    </li>                
				</ul>    

            </div>
            <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
                <div class="form-group">
                    <div class="input-group">
                        <input class="form-control" type="text" name="filter[keyword]" placeholder="Search..." maxlength="200" value="<?php echo $state->get('filter.keyword'); ?>"> 
                        <span class="input-group-btn">
                            <input class="btn btn-primary" type="submit" onclick="this.form.submit();" value="Search" />
                            <button class="btn btn-danger" type="button" onclick="Dsc.resetFormFilters(this.form);">Reset</button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <ul class="list-filters list-unstyled list-inline">
                    <li>
                    </li>
                    <li>
                    </li>
                </ul>            
            </div>
            
            <div class="col-xs-12 col-sm-6">
                <div class="text-align-right">
                <ul class="list-filters list-unstyled list-inline">
                    <li>
                        <?php if (!empty($paginated->items)) { ?>
                        <?php echo $paginated->getLimitBox( $state->get('list.limit') ); ?>
                        <?php } ?>
                    </li>                
                </ul>    
                </div>
            </div>
        </div>
        
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-lg-3">
                        <span class="pagination">
                        </span>
                    </div>    
                    <div class="col-xs-12 col-sm-6 col-lg-6 col-lg-offset-3">
                        <div class="text-align-right">
                            <?php if (!empty($paginated->total_pages) && $paginated->total_pages > 1) { ?>
                                <?php echo $paginated->serve(); ?>
                            <?php } ?>
                        </div>            
                    </div>
                </div>
            </div>
            <div class="panel-body">

            <?php if (!empty($paginated->items)) { ?>
                <ul class="list-group">
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-sm-2">
                                <b>Date</b>
                            </div>
                            <div class="col-sm-6">
                                <b>Affiliate</b>
                            </div>
                            <div class="col-sm-2">
                                <b>Amount</b>
                            </div>                            
                            <div class="col-sm-2">
                                <b>Status</b>
                            </div>
                        </div>
                    </li>
                    
                <?php foreach($paginated->items as $key=>$item) { ?>
                    <li class="list-group-item" data-id="<?php echo $item->id; ?>">
                        <div class="row">
                            <div class="col-sm-2">
                                <?php echo date('Y-m-d g:ia', $item->{'metadata.created.time'} ); ?>
                            </div>
                            <div class="col-sm-6">
                                <h5>
                                    <?php echo $item->affiliate_name; ?>
                                </h5>
                            </div>
                            <div class="col-sm-2">
                                <?php echo $item->amount; ?>
                            </div>
                            <div class="col-sm-2">
                                <?php echo $item->issued ? 'Issued' : 'Pending'; ?>
                            </div>
                        </div>
                    </li>
                <?php } ?>
                </ul>
            
            <?php } else { ?>
                <p>No items found.</p>
            <?php } ?>
        
            </div>
            
            <div class="panel-footer">
                <div class="row">
                    <div class="col-sm-10">
                        <?php if (!empty($paginated->total_pages) && $paginated->total_pages > 1) { ?>
                            <?php echo $paginated->serve(); ?>
                        <?php } ?>
                    </div>
                    <div class="col-sm-2">
                        <div class="datatable-results-count pull-right">
                            <span class="pagination">
                                <?php echo (!empty($paginated->total_pages)) ? $paginated->getResultsCounter() : null; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
                    
        </div>        
        
        

</form>