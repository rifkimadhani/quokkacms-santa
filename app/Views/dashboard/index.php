<!-- Row #1 -->
<div class="row gutters-tiny">
    <div class="col-md-6">
        <a class="block block-link-shadow" href="<?= base_url('stbdevices'); ?>">
            <div class="block-content block-content-full" style="background-color: #f7f7f7;">
                <i class="si si-feed fa-2x text-pulse"></i>
                <div class="row py-20 text-center">
                    <div class="col-6 border-r">
                        <div class="font-size-h3 font-w600 text-info"><?php echo $datastb['online']; ?></div>
                        <div class="font-size-sm font-w600 text-uppercase text-muted">Device Online</div>
                    </div>
                    <div class="col-6">
                        <div class="font-size-h3 font-w600 text-warning"><?php echo $datastb['offline']; ?></div>
                        <div class="font-size-sm font-w600 text-uppercase text-muted">Device Offline</div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6">
        <a class="block block-link-shadow" href="<?= base_url('subscriber'); ?>">
            <div class="block-content block-content-full" style="background-color: #f7f7f7;">
                <div class="text-right">
                    <i class="si si-users fa-2x text-success"></i>
                </div>
                <div class="row py-20 text-center">
                    <div class="col-6 border-r">
                        <div class="font-size-h3 font-w600 text-info"><?php echo $dataguest['checkin']; ?></div>
                        <div class="font-size-sm font-w600 text-uppercase text-muted">Guest Check-In</div>
                    </div>
                    <div class="col-6">
                        <div class="font-size-h3 font-w600 text-warning"><?php echo $dataguest['checkout']; ?></div>
                        <div class="font-size-sm font-w600 text-uppercase text-muted">Guest Check-Out</div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>
<!-- Row #2 -->
<div class="row gutters-tiny" style="padding-bottom: 1rem;">
    <div class="col-md-6">
        <a class="block block-link-shadow text-right" href="<?= base_url('emergencyhistory'); ?>">
            <div class="block-content block-content-full clearfix bg-warning">
                <div class="float-left mt-10">
                    <i class="si si-info fa-3x text-body-bg-dark"></i>
                </div>
                <div class="font-size-h3 font-w600 text-white"><?php echo $dataemergency['emergency_count']; ?></div>
                <div class="font-size-sm font-w600 text-uppercase text-light">Emergency Today</div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-xl-3">
        <a class="block block-link-shadow text-right" href="<?= base_url('room'); ?>">
            <div class="block-content block-content-full clearfix bg-info">
                <div class="float-left mt-10">
                    <i class="si si-user fa-3x text-body-bg-dark"></i>
                </div>
                <div class="font-size-h3 font-w600 text-white"><?php echo $dataroom['occupied']; ?></div>
                <div class="font-size-sm font-w600 text-uppercase text-light">Room Occupied</div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-xl-3">
        <a class="block block-link-shadow text-left" href="<?= base_url('room'); ?>">
            <div class="block-content block-content-full clearfix bg-success">
                <div class="float-right mt-10">
                    <i class="si si-ghost fa-3x text-body-bg-dark"></i>
                </div>
                <div class="font-size-h3 font-w600 text-white"><?php echo $dataroom['vacant']; ?></div>
                <div class="font-size-sm font-w600 text-uppercase text-light">Room Vacant</div>
            </div>
        </a>
    </div>
</div>