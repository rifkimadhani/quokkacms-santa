<?php if(isset($gridorlist)): ?>
    <?php if($gridorlist == 'grid'): ?>
        <a href="javascript:;" type="button" class="btn btn-default btn-grid-or-list" style-value="list">
            <i class="fa fa-bars"></i>
        </a>
    <?php else: ?>
        <a href="javascript:;" type="button" class="btn btn-grid-or-list" style-value="grid">
            <i class="fa fa-th-large"></i>
        </a>
    <?php endif; ?>
<?php endif; ?>