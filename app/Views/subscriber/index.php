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
            CREATE <?php echo ucfirst($this->headertitle) ?>
          </a>
        <?php endif; ?>
      </div>
      <div class="col-xl-8 col-lg-6 col-md-5 col-sm-4 col-8">
      </div>
      <div class="col-xl-2 col-lg-3 col-md-4 col-sm-5 col-12 text-right">
        <a href="javascript:;" role="button" class="btn btn-success showOptionsModal">
          OPTIONS
        </a>
      </div>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
    <table id="datalist" class="table table-bordered table-hover" style="width:100%">
      <thead>
        <tr>
          <?php 
              foreach ($field_list as $field)
              {
                  echo "<td>{$field}</td>";
              }
          ?>
          <td>Action</td>
        </tr>
      </thead>
    </table>
  </div>
</div>

<script>
  var urlssp           = "<?php echo base_url($currentctrl.'/ssprole') ?>";
  var urledit          = "<?php echo base_url($currentctrl.'/edit/') ?>";
  var urldetail        = "<?php echo base_url($currentctrl.'/detail/') ?>";
  var globalPrimaryKey = "<?php echo $primary; ?>";
  var lengthcolumn     =  "<?php echo count($field_list); ?>";
$('document').ready(function()
{
  globalTableList = $('#datalist').DataTable
  ({
      responsive: true,
      "scrollX":true,
      pageLength: 100,
      "order": [['0','asc']],
      "processing": true,
      "serverSide": true,
      "language": {processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '},
      "ajax": urlssp,
      columnDefs: [
        {targets: [],visible: false,searchable: false},
        {
          targets: parseInt(lengthcolumn),
          data: null,
          className: "center",
          defaultContent: '<a class="showEditModal" href="javascript:;" ><i class="fa fa-edit fa-2x"></i></a><a class="showDeleteModal" href="javascript:;"> <i class="fa fa-trash fa-2x"></i></a>'
        }
      ]
  });
  $('.showNewModal').click(function()
  {  
    $('.newModal').modal(); 
  });

  $('#datalist tbody').on( 'click', '.showEditModal', function (event)
  {  
    event.stopPropagation();
    var data = globalTableList.row( $(this).parents('tr') ).data();
    var $columnName     = globalPrimaryKey;
    var $columnValue    = data[0];
    var url             = urledit + $columnValue + '/' + $columnName;
    jQuery('#overlay-loader-indicator').show();
    $.ajax({url: url}).done(function(result)
    {
      $('.editModal').html(result);
      $('.editModal').modal();
    })
    .always(function() 
    {
      jQuery('#overlay-loader-indicator').hide();
    });
  });
  $('#datalist tbody').on( 'click', '.showDeleteModal', function (event)
  {     
    event.stopPropagation();
    $(".deleteModal .modal-body h5").empty();
    var data = globalTableList.row( $(this).parents('tr') ).data();
    var $columnName     = globalPrimaryKey;
    var $columnValue    = data[0];
    $('.deleteModal').modal();
    $('#deleteForm input[name=columnName]').val($columnName);
    $('#deleteForm input[name=columnValue]').val($columnValue);
    $( "<h5>Are You Sure To Delete Subscriber With Name "+data[2]+" ?</h5>" ).insertBefore( "#deleteForm" );
  });
});

</script>



