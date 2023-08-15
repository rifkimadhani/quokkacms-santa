<?php
    $currentctrl = $baseUrl;//$this->router->fetch_class();
//  $currentctrl = $this->router->fetch_class();
?>
<?php if(isset($order_status)):?>
    <?php $btncolor = "btn-primary";?>
    <div class="stepwizard">
        <div class="stepwizard-row setup-panel">
            <div class="stepwizard-step col-xs-2"> 
                <a href="#connectdevices" type="button" class="btn <?php echo $btncolor;?> btn-circle connectdevices">1</a>
                <p><small>Connect To Devices</small></p>
            </div>
            <div class="stepwizard-step col-xs-1"> 
                <a href="#makedir" type="button" class="btn <?php echo $btncolor;?> btn-circle makedir">2</a>
                <p><small>Make Dir</small></p>
            </div>
            <div class="stepwizard-step col-xs-2"> 
                <a href="#changepermission" type="button" class="btn <?php echo $btncolor;?> btn-circle changepermission">3</a>
                <p><small>Change Permission</small></p>
            </div>
            <div class="stepwizard-step col-xs-2"> 
                <a href="#pushapk" type="button" class="btn <?php echo $btncolor;?> btn-circle pushapk">4</a>
                <p><small>Push APK</small></p>
            </div>
            <div class="stepwizard-step col-xs-2"> 
                <a href="#injectconfig" type="button" class="btn <?php echo $btncolor;?> btn-circle injectconfig">5</a>
                <p><small>Inject Config</small></p>
            </div>
            <div class="stepwizard-step col-xs-1"> 
                <a href="#installapk" type="button" class="btn <?php echo $btncolor;?> btn-circle installapk">6</a>
                <p><small>Install</small></p>
            </div>
            <div class="stepwizard-step col-xs-1"> 
                <a href="#runapk" type="button" class="btn <?php echo $btncolor;?> btn-circle runapk">7</a>
                <p><small>Run</small></p>
            </div>
            <div class="stepwizard-step col-xs-1"> 
                <a href="#disconnect" type="button" class="btn <?php echo $btncolor;?> btn-circle disconnect">8</a>
                <p><small>Disconnect</small></p>
            </div>

        </div>
    </div>
<?php endif;?>