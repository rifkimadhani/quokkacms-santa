<?php 
?>
<style>
body::-webkit-scrollbar { width: 0.2em; }
.select2-container--default .select2-results__option[aria-disabled=true] { display: none; }
.step-result {
    padding: 12px 15px;
    margin-bottom: 10px;
    border-radius: 4px;
    background: #fcfcfc;
    border: 1px solid #e9e9e9;
    border-left: 3px solid #5c90d2;
}
.step-result.success { border-left-color: #46c37b; }
.step-result.error { border-left-color: #d26a5c; }
.step-result h6 { margin: 0 0 5px 0; font-weight: 600; text-transform: uppercase; font-size: 11px; color: #575757; letter-spacing: 0.5px; }
.step-result .cmd { font-family: 'SFMono-Regular', Consolas, monospace; font-size: 11px; color: #888; word-break: break-all; background: #f5f5f5; padding: 4px 8px; border-radius: 3px; }
.step-result .output { font-weight: 500; margin-top: 8px; font-size: 13px; }
</style>

<div class="block block-rounded">
    <div class="block-header block-header-default">
        <h3 class="block-title"><i class="si si-cloud-download text-primary mr-5"></i> Single Device Installer</h3>
        <div class="block-options">
            <a class="btn btn-sm btn-secondary" href="<?= $baseUrl ?>/batch">
                <i class="fa fa-list-ul mr-5"></i> Batch Install
            </a>
        </div>
    </div>
    <div class="block-content">
        <form id="newForm" action="<?= $baseUrl?>/installapp" method="post">
            <div class="row push">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="device_type">Device Type</label>
                        <select class="form-control" id="device_type" name="device_type" required>
                            <option value="android_stb">Android STB (Rooted)</option>
                            <option value="philips_tv" selected>Philips TV (Non-Rooted)</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="ip_address">Device IP Address</label>
                        <input type="text" class="form-control" id="ip_address" name="ip_address" 
                               value="<?php if(isset($ip_address)) echo $ip_address; ?>" 
                               placeholder="e.g. 192.168.1.100" autocomplete="off" required>
                    </div>
                </div>
            </div>
            <div class="row push">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="latest_apk">APK Version</label>
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
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="stb_name">Device Name</label>
                        <select class="form-control" id="stb_name" name="stb_id" required>
                            <option value="" disabled selected>Select device or type new name...</option>
                            <?php
                            if(count($stbdevices) > 0) {
                                foreach ($stbdevices as $valueoption) {
                                    echo "<option value='{$valueoption['id']}' data-src='{$valueoption['data']}'>{$valueoption['value']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-rocket mr-5"></i> Install App
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Installation Results -->
<div class="block block-rounded" id="resultsBlock" style="display:none;">
    <div class="block-header block-header-default">
        <h3 class="block-title"><i class="si si-speedometer text-muted mr-5"></i> Installation Progress</h3>
    </div>
    <div class="block-content">
        <div id="div_ping"></div>
        <div id="div_connect"></div>
        <div id="div_mkdir"></div>
        <div id="div_chmod"></div>
        <div id="div_pushapk"></div>
        <div id="div_pushconfig"></div>
        <div id="div_install"></div>
        <div id="div_runapp"></div>
        <div id="div_disconnect"></div>
    </div>
</div>

<script>
$('document').ready(function() {
    var baseurl = "<?=$baseUrl?>";
    
    $('#newForm input[name=ip_address]').inputmask({ alias: "ip" });
    jQuery('#stb_name').select2({
        placeholder: "Select device or type new name...",
        allowClear: true,
        tags: true,
        language: { noResults: function() { return "Type to add new device"; } }
    });

    $("#newForm").submit(function(e) {
        e.preventDefault();
        jQuery('#overlay-loader-indicator').show();
        $('#resultsBlock').show();
        clearDiv();

        $.ajax({
            type: 'POST',
            url: baseurl + '/ajax_install',
            data: $(this).serialize(),
            success: function(response) {
                updateStatus(JSON.parse(response));
            },
            error: function(error) {
                console.log('Error:', error);
                jQuery('#overlay-loader-indicator').hide();
            }
        });
    });
});

function updateStatus(data) {
    function getStep(key, altKey) {
        if (data[key]) return data[key];
        if (altKey && data[altKey]) return data[altKey];
        return null;
    }
    clearDiv();
    
    if (data.error && data.ping) {
        updateDiv('Ping Check', $('#div_ping'), data.ping, true);
        jQuery('#overlay-loader-indicator').hide();
        return;
    }

    if (getStep('ping')) updateDiv('Ping', $('#div_ping'), getStep('ping'));
    if (getStep('connect')) updateDiv('Connect', $('#div_connect'), getStep('connect'));
    if (getStep('root')) updateDiv('Root', $('#div_mkdir'), getStep('root'));
    if (getStep('mkdir')) updateDiv('Create Dir', $('#div_mkdir'), getStep('mkdir'));
    if (getStep('chmod')) updateDiv('Set Permission', $('#div_chmod'), getStep('chmod'));
    if (getStep('push_apk')) updateDiv('Push APK', $('#div_pushapk'), getStep('push_apk'));
    if (getStep('install')) updateDiv('Install', $('#div_install'), getStep('install'));
    var configStep = getStep('push_config', 'copy_config');
    if (configStep) updateDiv('Config', $('#div_pushconfig'), configStep);
    if (getStep('runapp_first')) updateDiv('Init App', $('#div_runapp'), getStep('runapp_first'));
    if (getStep('disable_bloatware')) updateDiv('Disable Bloatware', $('#div_mkdir'), getStep('disable_bloatware'));
    if (getStep('accessibility')) updateDiv('Accessibility', $('#div_chmod'), getStep('accessibility'));
    if (getStep('set_home')) updateDiv('Set Home', $('#div_pushapk'), getStep('set_home'));
    if (getStep('runapp')) updateDiv('Run App', $('#div_runapp'), getStep('runapp'));
    if (getStep('disconnect')) updateDiv('Disconnect', $('#div_disconnect'), getStep('disconnect'));

    jQuery('#overlay-loader-indicator').hide();
}

function updateDiv(title, div, step, isError) {
    if (!step) return;
    const text = step.retString || '';
    const hasError = isError || /fail|error|not found/i.test(text);
    
    let html = '<div class="step-result ' + (hasError ? 'error' : 'success') + '">';
    html += '<h6>' + (hasError ? '✗ ' : '✓ ') + title + '</h6>';
    html += '<div class="cmd">$ ' + step.cmd + '</div>';
    html += '<div class="output" style="color:' + (hasError ? '#d26a5c' : '#46c37b') + '">' + text + '</div>';
    html += '</div>';
    div.html(html);
}

function clearDiv() {
    $('#div_ping, #div_connect, #div_mkdir, #div_chmod, #div_pushapk, #div_pushconfig, #div_install, #div_runapp, #div_disconnect').html('');
}
</script>
