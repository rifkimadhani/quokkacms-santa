<?php 
//  defined('BASEPATH') OR exit('No direct script access allowed');
//  $currentctrl = $this->router->fetch_class();

$router = service('router');
$currentctrl = $router->controllerName();
?>
<div class="row">
  <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-3" style="margin-bottom:20px;">
    <div id="jstree_demo"></div>
  </div>
  <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9 col-9" style="display:none" id="jstreeformNew" >
    <div class="box" style="top:0px;">
      <div class="box-header with-border"><h2 class="box-title">UPDATE INFORMATION</h2>
        <div class="box-tools pull-right">
            <a href="<?php echo base_url($currentctrl.'/checkout/'.$subscriber_id); ?>" class="btn btn-danger pull-right checkout-dialog" style="margin-right: 5px;">
              <i class="fa fa-download"></i> Check Out
            </a>
          </div>
      </div>
      <div class="box-body">
        <?=view('util/editform',['entity'=>$entity,'metadata'=>$metadata]);?>
      </div>
    </div>
    <?php $total = 0;$checkin_counter=0;$index=1;foreach($summarys as $summary): ?>
      <?php $subtotal=0;$subtotal=$subtotal + $summary['billings']['room_service']['subtotal'] + $summary['billings']['karaoke']['subtotal'] +$summary['billings']['vod']['subtotal'] + $summary['billings']['livetv']['subtotal']; ?>
      <?php $total = $total + $subtotal;?>
    <?php endforeach; ?>
    <?php if($total > 0): ?>
    <div class="box" style="top:0px;">
      <div class="box-header">
        <h3 class="box-title">BILING SUMMARY </h3>

        <div class="box-tools pull-right">
          
        </div>
      </div>
      <div class="box-body" style="">
        <div class="row">
          <div class="col-xs-12 table-responsive">
            <table class="table table-striped" id="biling">
              <thead>
              <tr>
                <th>No.</th>
                <th>Room</th>
                <th>Guest Name</th>
                <th>Room Service</th>
                <th>Karaoke</th>
                <th>Video On Demand</th>
                
                <th>Live TV</th>
                <th style="text-align:right;">Subtotal</th>
                <!-- <th style="text-align:right;">Actions</th> -->
              </tr>
              </thead>
              <tbody>
              <?php $total = 0;$checkin_counter=0;$index=1;foreach($summarys as $summary): ?>
              <?php $subtotal=0;$subtotal=$subtotal + $summary['billings']['room_service']['subtotal'] + $summary['billings']['karaoke']['subtotal'] +$summary['billings']['vod']['subtotal'] + $summary['billings']['livetv']['subtotal']; ?>
                <?php if($subtotal > 0): ?>
                <tr>
                  <td><?php echo $index; $index++;$total = $total + $subtotal;?></td>
                  <td><?php echo $summary['room_name'];?></td>
                  <td><?php echo $summary['name_guest'];?></td>
                  <td><?php echo (isset($summary['billings']['room_service']['currency_sign'])?$summary['billings']['room_service']['currency_sign']:''). number_format($summary['billings']['room_service']['subtotal']);?></td>
                  <td><?php echo (isset($summary['billings']['karaoke']['currency_sign'])?$summary['billings']['karaoke']['currency_sign']:'').number_format($summary['billings']['karaoke']['subtotal']);?></td>
                  <td><?php echo (isset($summary['billings']['vod']['currency_sign'])?$summary['billings']['vod']['currency_sign']:'').number_format($summary['billings']['vod']['subtotal']);?></td>
                  <td><?php echo (isset($summary['billings']['livetv']['currency_sign'])?$summary['billings']['livetv']['currency_sign']:'').number_format($summary['billings']['livetv']['subtotal']);?></td>
                  <td style="text-align:right;"><?php echo number_format($subtotal);?></td>
                  <!-- <td style="text-align:right;">
                    <?php if($summary['status_checkin'] === 'checkin'):?>
                      <?php $checkin_counter++; ?>
                      <a href="<?php echo base_url($currentctrl.'/checkout/'.$subscriber_id.'/'.$summary['room_id']); ?>"  type="button" class="btn btn-primary pull-right checkout-dialog" style="margin-right: 5px;">
                        <i class="fa fa-download"></i> Check Out
                      </a>
                    <?php else:?>
                    <?php endif;?>
                  </td> -->
                </tr>
                <?php endif;?>
              <?php endforeach; ?>

                <tr>
                  <td><b>Total</b></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td style="text-align:right;"><b><?php echo number_format($total);?></b></td>
                  <td></td>
                </tr>                
              </tbody>
            </table>
          </div>
        </div>
        <?php if($checkin_counter != 0):?>
          <div class="row no-print">
            <div class="col-xs-12">

            </div>
          </div>
        <?php endif;?>
      </div>

    </div>
    <?php endif;?>
  </div>

  <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9 col-9" style="margin-bottom:20px;display:none" id="roomInfo" >

  </div>

</div>

<div class="modal fade modal-checkout" style="min-width:400px" role="dialog" aria-labelledby="oneChekout" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title">Confirm Guest Check Out</h4>
            </div>
            <form id="checkoutForm" action="" method="get">
                <div class="modal-body">
                  <p>Are You Sure To Check Out This Guest?</p>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary">Yes,I'm Sure</button>
                </div>
            </form>
        </div>
    </div>
</div>




<script>
  var urljstreesubscriber   = "<?php echo base_url($currentctrl.'/jstreesubscribers') ?>";
  var url_roominfo          = "<?php echo base_url($currentctrl.'/jstreeroominfo') ?>";
  var package_id;
  $('document').ready(function()
  {
    $('#jstree_demo').bind("loaded.jstree", function(e, data) 
    {
      $(this).jstree("open_all");
      $('#jstree_demo').jstree('select_node', 'ul > li:first');
    })

    $('#jstree_demo').jstree({
      'core' : 
      {
        'data' : {'url' : urljstreesubscriber,"dataType" : "json"},
        'check_callback' : true,
        "themes" : {"responsive":true,"stripes" :false ,"icons":true},
        "animation" : 0,
      },
      
      'plugins' : ['themes','state','contextmenu'],
      "contextmenu":{         
            "items": function($node) {
              if ($node.parent == 0)
              {
                return {
                    "Edit Package Name": {
                        "separator_before": false,
                        "separator_after": false,
                        "label": "Edit Package Name",
                        "action": function (obj) 
                        { 
                          jQuery('#jstreeformUpdate').show();
                          jQuery('#jstreeformNew').hide();
                          jQuery('#jstreeShowLiveTV').hide();
                          jQuery('#editForm input[name=package_id]').val($node.id);
                          jQuery('#editForm input[name=name]').val($node.text);
                        }
                    },
                    "Remove Package": {
                          "separator_before": false,
                          "separator_after": false,
                          "label": "Remove Package",
                          "action": function (obj) 
                          { 
                              if($node.children.length > 0)
                              {
                                alert("Tidak Dapat Menghapus.Hapus Children Terlebih Dahulu.")
                              }
                              else
                              {
                                window.location.href = urldelete + '/' + $node.id;
                              }
                          }
                    }
                };
              }
            }
      }
    })

    $('#jstree_demo').on("select_node.jstree", function (e, data) 
    {
      var datasubscriber = data.node.data;
      if(data.node.id != 0)
      {
        var dataroom = data.node.original.data_room;
        jQuery('#jstreeformNew').hide();
        jQuery('#roomInfo').show();
        jQuery('#overlay-loader-indicator').show();
        $.ajax({url: url_roominfo,type: "POST",dataType : 'text',encode: true,data: dataroom})
        .done(function(response)
        {
          $('#roomInfo').html(response);
          $("input:checkbox:not(:checked)").each(function() 
          {
              var column = "table ." + $(this).attr("name");
              $(column).hide();
          });
        })
        .always(function() 
        {
            jQuery('#overlay-loader-indicator').hide();
        });
      }
      else
      {
        jQuery('#jstreeformNew').show();
        jQuery('#roomInfo').hide();
      }
    });
    jQuery('#editForm #room_id').select2(
    {
      placeholder: "Pilih Room Disini",
      allowClear: true,
      tags:false,
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
    jQuery('.editModal').removeAttr('tabindex');
  
    $("input:checkbox:not(:checked)").each(function() 
    {
        var column = "table ." + $(this).attr("name");
        $(column).hide();
    });

    $("input:checkbox").click(function()
    {
        var column = "table ." + $(this).attr("name");
        $(column).toggle();
    });

    $(document.body).on( 'click', '.checkout-dialog', function (event)
    {
      event.preventDefault();
      var urlchekout = $(this).attr('href');
      $('#checkoutForm').attr('action', urlchekout);
      $('.modal-checkout').modal(); 
    });

    $(".hidecol").click(function()
    {
        var id = this.id;
        var splitid = id.split("_");
        var colno = splitid[1];
        var checked = true;
        
        // Checking Checkbox state
        if($(this).is(":checked"))
        {
            checked = true;
        }else
        {
            checked = false;
        }
        setTimeout(function()
        {
          if(checked)
          {
            $('#biling td:nth-child('+colno+')').hide();
            $('#biling th:nth-child('+colno+')').hide();
          }
          else
          {
            $('#biling td:nth-child('+colno+')').show();
            $('#biling th:nth-child('+colno+')').show();
          }
        }, 0);
      });
  });
</script>

