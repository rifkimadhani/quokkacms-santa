<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php if(isset($field_list)): ?>
    <?php foreach ($field_list as $field): ?>
        <?php $field = snaketonormaltext($field); ?>
        <td><b><?php echo $field;?></b></td>
    <?php endforeach;?>
    <td><b>Action</b></td>
<?php endif; ?>