<?php if(isset($sort)): ?>
<select name="sorting" type="button" class="form-control btn btn-secondary sort" style="display:inline-block;width:auto;">
    <option value='asc' <?php if($sort == 'asc')echo 'selected'; ?> >ASC</option>
    <option value='desc' <?php if($sort == 'desc')echo 'selected'; ?>>DES</option>
</select>
<?php endif; ?>
<?php if(isset($sort)): ?>
<select name="limit" type="button" class="form-control btn btn-secondary limit" style="display:inline-block;width:auto;">
    <option value='10' <?php if($limit == 10)echo 'selected'; ?> >10</option>
    <option value='20' <?php if($limit == 20)echo 'selected'; ?> >20</option>
    <option value='30' <?php if($limit == 30)echo 'selected'; ?> >30</option>
    <option value='40' <?php if($limit == 40)echo 'selected'; ?> >40</option>
    <option value='50' <?php if($limit == 50)echo 'selected'; ?> >50</option>
</select>
<?php endif; ?>