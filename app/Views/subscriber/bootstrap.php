<?php 
//  defined('BASEPATH') OR exit('No direct script access allowed');
//  $currentctrl = $this->router->fetch_class();

$router = service('router');
$currentctrl = $router->controllerName();
?>
<div class="box">
  <div class="box-header">
      <div class="row">
        <div class="col-xl-2 col-lg-3 col-md-3 col-sm-3 col-4">
          <?php if($usemodal): ?>
            <a href="javascript:;" role="button" class="btn btn-primary showNewModal">
              CREATE GUEST <?php echo ucfirst($headertitle) ?>
            </a>
          <?php endif; ?>
        </div>
        <div class="col-xl-8 col-lg-6 col-md-5 col-sm-4 col-8">
          <?=view('util/header');?>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-5 col-12 text-right">
          <a href="<?php echo base_url($currentctrl.'/history');?>" role="button" class="btn btn-primary">
            GUEST HISTORY
          </a>
          <a href="javascript:;" role="button" class="btn btn-success showOptionsModal">
            OPTIONS
          </a>
        </div>
      </div>
    </div>
  <!-- /.box-header -->
  <div class="box-body table-responsive padding">
      <?php if(sizeof($subscribers) > 0): ?>
      <table id="datalist" class="table table-bordered table-hover" >
        <thead>
          <tr>
            <?php foreach ($subscribers[0] as $key=>$field): ?>
              <th><?php echo $key;?></th>
            <?php endforeach;?>
          </tr>
          </thead>
          <tbody>
            <?php foreach ($subscribers as $subscriber): ?>
                <tr data-subscriber="<?php echo $subscriber->{'Subscriber ID'};?>">
                  <?php foreach ($subscriber as $key=>$item): ?>
                    <?php if($key === 'Room'):?>
                     <td>
                        <span class="pull-left-container">
                        <?php $arrayroom = explode(",",$item); ?>
                        <?php foreach($arrayroom as $room):?>
                         <button class="btn btn-block btn-info btn-xs"><?php echo $room;?></button>
                        <?php endforeach;?>
                      </span>
                     </td>
                    <?php else:?>
                      <?php if($key === 'Create Date' || $key === 'create_date' || $key === 'Update Date' || $key === 'update_date' || $key === 'Checkin Date' || $key === 'Checkout Date')$item = getReadebleDateTime($item); ?>
                      <td><?php echo $item; ?></td>
                    <?php endif;?>
                  <?php endforeach;?>
                </tr>
            <?php endforeach;?>
          </tbody>
      </table>
      <?php else: ?>
          <div class="alert alert-danger alert-dismissible">
            <h4>Ops!</h4>
            <h4>Guest Are Empty. To Add New Guest, Click Button Create Guest</h4>
        </div>
      <?php endif;?>
    </div>
</div>

<script>
  var base_url         = "<?php echo base_url($currentctrl) ?>"
  var urlssp           = "<?php echo base_url($currentctrl.'/ssprole') ?>";
  var urledit          = "<?php echo base_url($currentctrl.'/edit/') ?>";
  var globalPrimaryKey = "<?php echo $primary; ?>";
$('document').ready(function()
{
  globalTableList = $('#datalist').DataTable(
    {
      "oSearch": {"sSearch": "" },
      "order": [['0','desc']],
      pageLength: 100,
      columnDefs: [
        {targets: [7,8,9],visible: false,searchable: false},
      ]
    });
  $('.showNewModal').click(function()
  {  
    $('.newModal').modal();
    jQuery('.newModal #room_id').select2(
    {
      placeholder: "Pilih Room Yang Masih Kosong Disini",
      allowClear: false,
      tags:false,
      "language": {"noResults": function(){return "None Of The Rooms Are Empty";}},
      escapeMarkup: function (markup) 
      {
          return markup;
      },
    })
    .on('select2:unselecting', function() 
    {
        $(this).data('unselecting', true);
    })
    .on('select2:opening', function(e) 
    {
      if ($(this).data('unselecting')) 
      {
        $(this).removeData('unselecting');
        e.preventDefault();
      }
    });

    jQuery('.newModal #diet').select2(
    {
      placeholder: "Pilih Room Yang Masih Kosong Disini",
      allowClear: false,
      tags:false,
      "language": {"noResults": function(){return "None Of The Rooms Are Empty";}},
      escapeMarkup: function (markup) 
      {
          return markup;
      },
    })
    .on('select2:unselecting', function() 
    {
        $(this).data('unselecting', true);
    })
    .on('select2:opening', function(e) 
    {
      if ($(this).data('unselecting')) 
      {
        $(this).removeData('unselecting');
        e.preventDefault();
      }
    });
  });

  $(document.body).on( 'click', 'tr', function (event)
  {  
    var subscriber_id    = $(this).attr("data-subscriber");
    window.location.href = base_url +"/detail/"+ subscriber_id;
  });
  $(document.body).on( 'click', '.showDeleteModal', function (event)
  {     
    event.stopPropagation();
    $(".deleteModal .modal-body h5").empty();
    var data = JSON.parse(jQuery(this).parent().attr('data-array'));
    console.log(data);
    var $columnName     = globalPrimaryKey;
    var $columnValue    = data['Subscriber ID'];
    $('.deleteModal').modal();
    $('#deleteForm input[name=columnName]').val($columnName);
    $('#deleteForm input[name=columnValue]').val($columnValue);
    $( "<h5>Are You Sure To Delete Subscriber With Name "+data.Name+" ?</h5>" ).insertBefore( "#deleteForm" );
  });
});

</script>



