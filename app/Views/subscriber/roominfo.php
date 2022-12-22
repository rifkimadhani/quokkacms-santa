<?php 
  defined('BASEPATH') OR exit('No direct script access allowed');
//  $currentctrl = $this->router->fetch_class();

$router = service('router');
$currentctrl = $router->controllerName();
?>
<div class="box" style="top:0px;">
    <div class="box-header with-border">
      <h2 class="box-title">ROOM INFORMATION</h2>
      <div class="box-tools pull-right">
        <div class="col-xs-12">
            <?php if($room_info['status_checkin'] === 'checkin'):?>
              <a href="<?php echo base_url($currentctrl.'/checkout/'.$subscriber_id.'/'.$room_id); ?>" class="btn btn-danger pull-right checkout-dialog" style="margin-right: 5px;">
                <i class="fa fa-download"></i> Check Out
              </a>
            <?php else:?>
              <a href="javascript:;" type="button" class="btn btn-success pull-right" style="margin-right: 5px;">
                <i class="fa fa-check"></i> Sudah Check Out
              </a>
            <?php endif;?>
        </div>
      </div>
    </div>
    <div class="box-body">
        <form id="roomInfoForm" action="<?= base_url($currentctrl.'/updateroominfo') ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label class='col-form-label'><b>Nama Guest</b></label>
                <input type=hidden name='subscriber_id' value="<?php echo $subscriber_id; ?>" required>
                <input type=hidden name='room_id' value="<?php echo $room_id; ?>" required>
                <input type=text minlength='0' maxlength='100' name='name_guest' value="<?php echo $name_guest; ?>" placeholder='Masukkan Nama Guest Disini' class=form-control  required>
            </div>
            <div class="form-group">
                <label class='col-form-label'><b>Security PIN</b></label>
                <input type=text minlength='6' maxlength='6' name='security_pin' value="<?php echo $room_info['security_pin']; ?>" placeholder='Masukkan Room PIN Disini' class=form-control  required>
            </div>
            <button type="submit" class="btn btn-block btn-primary">Update</button>
        </form>
    </div>
</div>
<?php $index = 1;$total = 0;foreach($billings as $item): ?>
  <?php $total = $total + $item['subtotal'];?>
<?php endforeach;?>
  <?php if($total > 0): ?> 
    <div class="box" style="top:0px;">
        <div class="box-body">
          <div class="row">
            <div class="col-xs-12">
              <h2 class="page-header">
                <i class="fa fa-globe"></i> BILLING INFORMATION
                <!-- <small class="pull-right">Date: 2/10/2014</small> -->
              </h2>
            </div>
          </div>
          <div class="row">
            <div class="box-body">
              <input type="checkbox" name="package"> Package 
              <input type="checkbox" name="order_date"> Order Date
              <input type="checkbox" name="expired_date"> Expired Date
            </div>
            <div class="col-xs-12 table-responsive">
              <table class="table table-striped" id="biling">
                <thead>
                <tr>
                    <th>No.</th>
                    <th>Product</th>
                    <th class="package">Pakcage</th>
                    <th class="order_date">Order Date</th>
                    <th class="expired_date">Expired Date</th>
                    <th>Duration (Hour)</th>
                    <th>Price</th>
                    <th>SC (%)</th>
                    <th>SC Amount</th>
                    <th>Tax (%)</th>
                    <th>Tax Amount</th>
                    <th>Delivery Fee</th>
                    <th>Subtotal</th>
                </tr>
                </thead>
                <tbody>
                <?php $index = 1;$total = 0;foreach($billings as $item): ?>
                  <tr>
                    <td><?php echo $index; $index++; $total = $total + $item['subtotal'];?></td>
                    <td><?php echo $item['product'];?></td>
                    <td class="package"><?php echo $item['package'];?></td>
                    <td class="order_date"><?php echo $item['order_date'];?></td>
                    <td class="expired_date"><?php echo $item['expired_date'];?></td>
                    <td style="text-align:center"><?php echo $item['duration'];?></td>
                    <td style="text-align:right"><?php echo number_format($item['purchase_amount']);?></td>
                    <td style="text-align:center"><?php echo $item['percent_service_charge'];?></td>
                    <td style="text-align:right"><?php echo number_format($item['service_charge']);?></td>
                    <td style="text-align:center"><?php echo number_format($item['percent_tax']);?></td>
                    <td style="text-align:right"><?php echo number_format($item['tax']);?></td>
                    <td style="text-align:right"><?php echo number_format($item['delivery_fee']);?></td>
                    <td style="text-align:right"><?php echo $item['currency_sign'].number_format($item['subtotal']);?></td>
                  </tr>
                <?php endforeach;?>                
                  <tr>
                  <td><b>Total</b></td>
                      <td></td>
                      <td class="package"></td>
                      <td class="order_date"></td>
                      <td class="expired_date"></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                    <?php if(isset($billings[0]['currency_sign'])): ?>
                      <td style="text-align:right"><b><?php echo $billings[0]['currency_sign'].number_format($total);?></b></td>
                    <?php else: ?>
                      <td style="text-align:right"><b>0</b></td>
                    <?php endif; ?>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        
            <div class="row no-print">
              <div class="col-xs-12">

              </div>
            </div>
          
        </div>
    </div>
  <?php endif;?>

<script>
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
</script>
