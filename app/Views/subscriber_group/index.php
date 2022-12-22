<?php
use App\Libraries\FormBuilder;
use App\Models\SubscriberGroupForm;
use App\Models\SubscriberGroupModel;

$router = service('router');
$controllerName = $router->controllerName();

$group = new SubscriberGroupModel();
$data = $group->get(1);

$metadata = new SubscriberGroupForm();
$builder = new FormBuilder();

$urlAction = base_url() . '/subscribergroup/new';

$html = $builder->renderDialog('Edit', 'xxxForm', $metadata, $urlAction, $data);
?>

<input type="button" onclick="onClick()" value="CLICK">

<?=$html?>


<script>
    function onClick() {
        //show dialog
        $('.dialogxxxForm').modal();
    }
</script>
