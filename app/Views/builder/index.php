<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 2/23/2023
 * Time: 9:23 AM
 */
?>

<!-- <form method="post" action="<?//=$baseUrl?>/build">
    <textarea name="json" rows="25" cols="50"><?//=$sample?></textarea>
    <br/>
    <button type="submit">Build</button>
</form> -->

<!-- JSON Editor -->
<form method="post" action="<?=$baseUrl?>/build">
    <div class="row" style="margin-bottom:1em;">
        <div class="col-md-8">
            <div id="jsoneditor" style="height: fit-content;"></div>
            <button class="btn btn-dark mb-2 mt-2 pull-right" type="button" onclick="convertToJson()"><i class="fa fa-code fa-fw"></i> Convert</button>
        </div>
        <div class="col-md-4">
            <textarea class="pull-right form-control" rows="11" name="json" id="jsoninput" style="width: 100%; margin-bottom:0.35rem;"></textarea>
            <button class="btn btn-dark mb-2 mt-2 pull-right" type="submit"><i class="fa fa-check fa-fw"></i> Build</button>
        </div>    
        <br>
    </div>
</form>

<!-- JSON Editor -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jsoneditor/9.10.0/jsoneditor.min.js"></script>
<script>
    var container = document.getElementById("jsoneditor");
    var options = {};
    var editor = new JSONEditor(container, options);

    // Set the initial value
    var initialJson = <?=$sample?>;
    editor.set(initialJson);

    // Convert JSON to textarea value
    function convertToJson() {
        var jsonValue = editor.get();
        var jsonTextarea = document.getElementById("jsoninput");
        jsonTextarea.value = JSON.stringify(jsonValue);
    }
</script>