<div class="row">
    <div class="col-md-6">
        <div class="info-box bg-green">
        <span class="info-box-icon" style="height:112px;line-height:112px;">
            <a href="<?= base_url('stbdevices'); ?>"><i class="fa fa-wifi"></i></a>
        </span>
        <div class="info-box-content">
            <span class="info-box-text">STB ONLINE</span>
            <span class="info-box-number"><?php echo $datastb['online']; ?> STB</span>

            <div class="progress">
                <!-- <div class="progress-bar" style="width: 50%"></div> -->
            </div>
            <span class="info-box-text">STB OFLINE</span>
            <span class="info-box-number"><?php echo $datastb['offline']; ?> STB</span>
        </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="info-box bg-yellow">
        <span class="info-box-icon" style="height:112px;line-height:112px;">
            <a href="<?= base_url('subscriber'); ?>"><i class="fa fa-group"></i></a>
        </span>
        <div class="info-box-content">
            <span class="info-box-text">GUEST CHEK-IN TODAY</span>
            <span class="info-box-number"><?php echo $dataguest['checkin'];?> GUEST</span>

            <div class="progress">
                <!-- <div class="progress-bar" style="width: 50%"></div> -->
            </div>
            <span class="info-box-text">GUEST CHECK-OUT TODAY</span>
            <span class="info-box-number"><?php echo $dataguest['checkout'];?> GUEST</span>
        </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="info-box bg-red">
        <span class="info-box-icon" style="height:112px;line-height:112px;">
            <a href="<?= base_url('emergencyhistory'); ?>"><i class="fa fa-bullhorn"></i></a>
        </span>
        <div class="info-box-content">
            <span class="info-box-text">&nbsp;</span>
            <span class="info-box-number">EMERGENCY TODAY</span>

            <div class="progress">
                <!-- <div class="progress-bar" style="width: 50%"></div> -->
            </div>
            <span class="info-box-text" style="font-size:32px;"><?php echo $dataemergency['emergency_count'];?> TIMES</span>
        </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="info-box bg-aqua">
        <span class="info-box-icon" style="height:112px;line-height:112px;">
            <a href="<?= base_url('room'); ?>"><i class="fa fa-bed"></i></a>
        </span>
        <div class="info-box-content">
            <span class="info-box-text">ROOM OCCUPIED</span>
            <span class="info-box-number"><?php echo $dataroom['occupied'];?> ROOM</span>

            <div class="progress">
                <!-- <div class="progress-bar" style="width: 50%"></div> -->
            </div>
            <span class="info-box-text">ROOM VACANT</span>
            <span class="info-box-number"><?php echo $dataroom['vacant'];?> ROOM</span>
        </div>
        </div>
    </div>
</div>