<?php 
?>
<style>
body::-webkit-scrollbar { width: 0.2em; }
.status-pending { color: #6c757d; }
.status-installing { color: #5c90d2; }
.status-success { color: #46c37b; }
.status-failed { color: #d26a5c; }
.batch-stats { display: flex; gap: 15px; margin-bottom: 20px; }
.batch-stat { 
    flex: 1; padding: 15px; text-align: center; 
    background: #f9f9f9; border-radius: 4px; border: 1px solid #eee;
}
.batch-stat .num { font-size: 28px; font-weight: 700; line-height: 1; }
.batch-stat .lbl { font-size: 11px; text-transform: uppercase; color: #999; margin-top: 5px; letter-spacing: 0.5px; }
.batch-stat.s-total .num { color: #5c90d2; }
.batch-stat.s-success .num { color: #46c37b; }
.batch-stat.s-failed .num { color: #d26a5c; }
</style>

<div class="block block-rounded">
    <div class="block-header block-header-default">
        <h3 class="block-title"><i class="si si-layers text-primary mr-5"></i> Batch Philips TV Installer</h3>
        <div class="block-options">
            <a class="btn btn-sm btn-secondary" href="<?= $baseUrl ?>">
                <i class="fa fa-arrow-left mr-5"></i> Single Device
            </a>
        </div>
    </div>
    <div class="block-content">
        <form id="batchForm">
            <div class="row push">
                <div class="col-lg-8">
                    <div class="form-group">
                        <label>APK to Install</label>
                        <select class="form-control" id="latest_apk" name="latest_apk" required>
                            <?php
                            if(count($latestgroupapk) > 0) {
                                foreach ($latestgroupapk as $valueoption) {
                                    echo "<option value='{$valueoption['id']}'>{$valueoption['app_id']} v{$valueoption['version_name']} ({$valueoption['version_code']})</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div class="custom-control custom-checkbox mt-10">
                            <input type="checkbox" class="custom-control-input" id="simulate_mode">
                            <label class="custom-control-label" for="simulate_mode">Simulation Mode</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Device List <small class="text-muted">— one per line: IP Address, Device Name</small></label>
                <textarea class="form-control" id="device_list" rows="5" required
                    style="font-family: 'SFMono-Regular', Consolas, monospace; font-size: 13px;"
                    placeholder="172.19.1.101, TV-Lobby-01
172.19.1.102, TV-Room-101
172.19.1.103, TV-Room-102"></textarea>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary" id="btnStart">
                    <i class="fa fa-play mr-5"></i> Start Batch Install
                </button>
                <button type="button" class="btn btn-danger" id="btnStop" disabled>
                    <i class="fa fa-stop mr-5"></i> Stop
                </button>
            </div>
        </form>
    </div>
</div>

<div class="block block-rounded" id="progressBox" style="display:none;">
    <div class="block-header block-header-default">
        <h3 class="block-title"><i class="si si-graph text-muted mr-5"></i> Progress</h3>
    </div>
    <div class="block-content">
        <div class="batch-stats">
            <div class="batch-stat s-total"><div class="num" id="statTotal">0</div><div class="lbl">Total</div></div>
            <div class="batch-stat s-success"><div class="num" id="statSuccess">0</div><div class="lbl">Success</div></div>
            <div class="batch-stat s-failed"><div class="num" id="statFailed">0</div><div class="lbl">Failed</div></div>
        </div>
        <div class="progress push" style="height: 6px;">
            <div id="overallProgress" class="progress-bar bg-success" style="width: 0%"></div>
        </div>
        <table class="table table-sm table-striped table-vcenter" id="deviceTable">
            <thead>
                <tr>
                    <th style="width:40px" class="text-center">#</th>
                    <th>Device</th>
                    <th style="width:140px">IP</th>
                    <th style="width:110px" class="text-center">Status</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody id="deviceTableBody"></tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    var baseurl = "<?=$baseUrl?>", devices = [], currentIndex = 0, stopRequested = false;

    function parseDevices() {
        devices = $('#device_list').val().trim().split('\n')
            .map(line => line.trim())
            .filter(line => line && line.includes(','))
            .map(line => {
                const [ip, name] = line.split(',').map(s => s.trim());
                return { ip, name, status: 'pending', details: '' };
            });
        return devices.length > 0;
    }

    function buildTable() {
        $('#deviceTableBody').html(devices.map((d, i) => 
            `<tr><td class="text-center">${i+1}</td><td>${d.name}</td><td><code>${d.ip}</code></td>
             <td id="st-${i}" class="text-center status-pending"><i class="fa fa-clock-o text-muted"></i> Pending</td>
             <td id="dt-${i}" class="text-muted">—</td></tr>`
        ).join(''));
        $('#statTotal').text(devices.length);
        updateProgress();
    }

    function updateProgress() {
        let success = 0, failed = 0;
        devices.forEach(d => { if(d.status==='success') success++; if(d.status==='failed') failed++; });
        const done = success + failed;
        $('#overallProgress').css('width', (devices.length ? (done/devices.length*100) : 0) + '%');
        $('#statSuccess').text(success);
        $('#statFailed').text(failed);
    }

    function setStatus(i, status, details) {
        devices[i].status = status;
        const icons = { 
            pending: '<i class="fa fa-clock-o text-muted"></i>', 
            installing: '<i class="fa fa-spinner fa-spin text-info"></i>', 
            success: '<i class="fa fa-check text-success"></i>', 
            failed: '<i class="fa fa-times text-danger"></i>' 
        };
        const labels = { pending: 'Pending', installing: 'Installing...', success: 'Success', failed: 'Failed' };
        $('#st-'+i).removeClass().addClass('text-center status-'+status).html((icons[status]||'') + ' ' + (labels[status]||status));
        $('#dt-'+i).html(status==='failed' ? `<span class="text-danger">${details}</span>` : details);
        updateProgress();
    }

    function installDevice(i) {
        if (stopRequested || i >= devices.length) {
            $('#btnStart').prop('disabled', false);
            $('#btnStop').prop('disabled', true);
            Codebase.helpers('notify', { align:'right', from:'top', type: stopRequested?'warning':'success', 
                icon: 'fa fa-'+(stopRequested?'exclamation':'check')+' mr-5', 
                message: stopRequested ? 'Stopped' : 'Completed!' });
            return;
        }
        setStatus(i, 'installing', 'Processing...');
        $.post(baseurl + '/ajax_install_single', {
            ip_address: devices[i].ip,
            device_name: devices[i].name,
            latest_apk: $('#latest_apk').val(),
            simulate: $('#simulate_mode').is(':checked') ? '1' : '0'
        }).done(res => {
            const data = typeof res === 'string' ? JSON.parse(res) : res;
            setStatus(i, data.success ? 'success' : 'failed', data.success ? 'OK' : (data.error || 'Failed'));
            installDevice(i + 1);
        }).fail((xhr, status, err) => {
            setStatus(i, 'failed', 'Network: ' + err);
            installDevice(i + 1);
        });
    }

    $('#batchForm').submit(function(e) {
        e.preventDefault();
        if (!parseDevices()) { alert('Enter at least one device'); return; }
        $('#progressBox').show();
        buildTable();
        stopRequested = false;
        $('#btnStart').prop('disabled', true);
        $('#btnStop').prop('disabled', false);
        installDevice(0);
    });

    $('#btnStop').click(function() {
        if (confirm('Stop batch installation?')) {
            stopRequested = true;
            $(this).prop('disabled', true);
        }
    });
});
</script>
