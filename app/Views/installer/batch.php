<?php 
?>
<style>
body::-webkit-scrollbar {
    width: 0.2em;
}
.device-status {
    font-size: 1.2em;
}
.status-pending { color: #6c757d; }
.status-installing { color: #007bff; }
.status-success { color: #28a745; }
.status-failed { color: #dc3545; }
.progress-container {
    margin-bottom: 20px;
}
</style>

<div class="box" style="top:0px;">
    <div class="box-header with-border">
        <h3 class="box-title">Batch Philips TV Installer</h3>
    </div>
    <div class="box-body">
        <form id="batchForm">
            <div class="form-group">
                <label class='col-form-label'><b>APK to Install</b></label>
                <select class='form-control' id='latest_apk' name='latest_apk' required>
                    <?php
                    if(count($latestgroupapk)> 0 )
                    {
                        foreach ($latestgroupapk as $key => $valueoption) 
                        {
                            echo "<option value='{$valueoption['id']}'>{$valueoption['app_id']} version {$valueoption['version_name']} ({$valueoption['version_code']})</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label class='col-form-label'><b>Device List</b></label>
                <small class="text-muted d-block">Enter one device per line: IP Address, Device Name</small>
                <textarea class='form-control' id='device_list' name='device_list' rows='8' 
                    placeholder="172.19.1.101, TV-Room-101
172.19.1.102, TV-Room-102
172.19.1.103, TV-Room-103" required></textarea>
            </div>

            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="simulate_mode" name="simulate_mode">
                    <label class="form-check-label" for="simulate_mode">
                        <b>Simulation Mode</b> <small class="text-muted">(Test without real devices)</small>
                    </label>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-lg" id="btnStart">
                    <i class="fa fa-play"></i> START BATCH INSTALL
                </button>
                <button type="button" class="btn btn-secondary" id="btnStop" disabled>
                    <i class="fa fa-stop"></i> STOP
                </button>
            </div>
        </form>
    </div>
</div>

<div class="box" id="progressBox" style="display:none;">
    <div class="box-header with-border">
        <h3 class="box-title">Installation Progress</h3>
    </div>
    <div class="box-body">
        <div class="progress-container">
            <div class="progress" style="height: 25px;">
                <div id="overallProgress" class="progress-bar progress-bar-striped progress-bar-animated" 
                    role="progressbar" style="width: 0%">0 / 0</div>
            </div>
        </div>
        
        <table class="table table-bordered table-hover" id="deviceTable">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Device Name</th>
                    <th>IP Address</th>
                    <th style="width: 120px;">Status</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody id="deviceTableBody">
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    var baseurl = "<?=$baseUrl?>";
    var devices = [];
    var currentIndex = 0;
    var isRunning = false;
    var stopRequested = false;

    // Parse device list from textarea
    function parseDevices() {
        var lines = $('#device_list').val().trim().split('\n');
        devices = [];
        
        for (var i = 0; i < lines.length; i++) {
            var line = lines[i].trim();
            if (line === '') continue;
            
            var parts = line.split(',');
            if (parts.length >= 2) {
                devices.push({
                    ip: parts[0].trim(),
                    name: parts[1].trim(),
                    status: 'pending',
                    details: ''
                });
            }
        }
        
        return devices.length > 0;
    }

    // Build device table
    function buildTable() {
        var html = '';
        for (var i = 0; i < devices.length; i++) {
            html += '<tr id="device-row-' + i + '">';
            html += '<td>' + (i + 1) + '</td>';
            html += '<td>' + devices[i].name + '</td>';
            html += '<td>' + devices[i].ip + '</td>';
            html += '<td id="device-status-' + i + '" class="device-status status-pending">⏳ Pending</td>';
            html += '<td id="device-details-' + i + '">-</td>';
            html += '</tr>';
        }
        $('#deviceTableBody').html(html);
        updateProgress();
    }

    // Update progress bar
    function updateProgress() {
        var completed = 0;
        var success = 0;
        var failed = 0;
        
        for (var i = 0; i < devices.length; i++) {
            if (devices[i].status === 'success') {
                completed++;
                success++;
            } else if (devices[i].status === 'failed') {
                completed++;
                failed++;
            }
        }
        
        var percent = devices.length > 0 ? Math.round((completed / devices.length) * 100) : 0;
        $('#overallProgress')
            .css('width', percent + '%')
            .text(completed + ' / ' + devices.length + ' (' + success + ' success, ' + failed + ' failed)');
    }

    // Update device status
    function updateDeviceStatus(index, status, details) {
        devices[index].status = status;
        devices[index].details = details;
        
        var statusCell = $('#device-status-' + index);
        var detailsCell = $('#device-details-' + index);
        
        statusCell.removeClass('status-pending status-installing status-success status-failed');
        
        switch(status) {
            case 'installing':
                statusCell.addClass('status-installing').html('🔄 Installing...');
                break;
            case 'success':
                statusCell.addClass('status-success').html('✅ Success');
                break;
            case 'failed':
                statusCell.addClass('status-failed').html('❌ Failed');
                break;
        }
        
        detailsCell.text(details);
        updateProgress();
    }

    // Install single device
    function installDevice(index) {
        if (stopRequested || index >= devices.length) {
            isRunning = false;
            $('#btnStart').prop('disabled', false);
            $('#btnStop').prop('disabled', true);
            if (stopRequested) {
                alert('Batch installation stopped.');
            } else {
                alert('Batch installation completed!');
            }
            return;
        }

        updateDeviceStatus(index, 'installing', 'Connecting...');
        
        var device = devices[index];
        var simulate = $('#simulate_mode').is(':checked');
        
        $.ajax({
            type: 'POST',
            url: baseurl + '/ajax_install_single',
            data: {
                ip_address: device.ip,
                device_name: device.name,
                latest_apk: $('#latest_apk').val(),
                simulate: simulate ? '1' : '0'
            },
            success: function(response) {
                var data = typeof response === 'string' ? JSON.parse(response) : response;
                
                if (data.success) {
                    updateDeviceStatus(index, 'success', 'Installed successfully');
                } else {
                    updateDeviceStatus(index, 'failed', data.error || 'Unknown error');
                }
                
                // Install next device
                currentIndex = index + 1;
                installDevice(currentIndex);
            },
            error: function(xhr, status, error) {
                updateDeviceStatus(index, 'failed', 'Network error: ' + error);
                
                // Continue with next device
                currentIndex = index + 1;
                installDevice(currentIndex);
            }
        });
    }

    // Form submit
    $('#batchForm').submit(function(e) {
        e.preventDefault();
        
        if (!parseDevices()) {
            alert('Please enter at least one device (IP, Name)');
            return;
        }
        
        // Show progress box and build table
        $('#progressBox').show();
        buildTable();
        
        // Start installation
        isRunning = true;
        stopRequested = false;
        currentIndex = 0;
        
        $('#btnStart').prop('disabled', true);
        $('#btnStop').prop('disabled', false);
        
        installDevice(0);
    });

    // Stop button
    $('#btnStop').click(function() {
        if (confirm('Are you sure you want to stop the batch installation?')) {
            stopRequested = true;
            $(this).prop('disabled', true);
        }
    });
});
</script>
