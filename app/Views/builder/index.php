<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 2/23/2023
 * Time: 9:23 AM
 */

use App\Libraries\HtmlBuilder;

$htmlListTable = HtmlBuilder::renderOption($tables)

?>

<form method="post" action="<?=$baseUrl?>/build">

    <table>
        <thead>
            <tr><td></td><td></td></tr>
        </thead>
        <tbody>
            <tr><td>CRUD Table</td><td><select id='table' onchange="onChangeTable(this)"><?=$htmlListTable?></select></td></tr>
            <tr><td>CRUD allowedFields</td>
                <td>
                    <table id="tableCrudFields" border="1">
                        <thead><tr><td>&nbsp;</td><td>Field</td><td>pk</td><td>type</td></tr></thead>
                        <tbody></tbody>
                    </table>
                </td>
            </tr>
            <tr><td>View</td><td><select id='view' onchange="onChangeView(this)"><?=$htmlListTable?></select></td></tr>
        </tbody>
    </table>


    <br/><br/>

    <textarea id="json" name="json" rows="25" cols="50"><?=$sample?></textarea>
    <br/>

    <button type="submit">Build</button>

</form>

<script>
    const tbody = document.getElementById('tableCrudFields').getElementsByTagName('tbody')[0];
    const json = document.getElementById("json");

    var modelData = null;
    var fieldsSelected = []; //daftar semua field pada table

//    var fieldsPick = []; //field yg di pilih
//    var fieldsType = []; //field type
//    var fieldsAutoInc = []; //field auto inc

    var modelTable = ''; //json table
    var modelPk = []; //json pk
//    var modelUpdateFields = []; //upadateFields berisikan nama2 field yg boleh di update
    var modelView = '';
    var modelFieldList = '';

    /**
     * event saat table crud di pilih
     * @param that
     */
    function onChangeTable(that) {
        const options = that.selectedOptions;
        onFetchFields(options[0].value);
    }

    /**
     * di call saat user click checkbox pada field table
     */
    function onChangeField(that, idx) {
        fieldsSelected[idx].selected = that.checked;
        updateJson();
    }

    function onChangeType(that, idx) {
        fieldsSelected[idx].type = that.value;
        updateJson();
    }

    function onChangeView(that) {
        const options = that.selectedOptions;
        onFetchViewFields(options[0].value);
    }


    /**
     * ambil fields dari tablename
     * @param name string
     */
    function onFetchFields(name) {
        const url = "<?=$baseUrl?>/ajaxGetFields/" + name;

        modelTable = name;

        $.ajax({
            url: url,
        }).done(function(data) {
            modelData = data;
            removeAllRows();
            renderFields(data);
        });
    }

    function onFetchViewFields(name) {
        const url = "<?=$baseUrl?>/ajaxGetFields/" + name;

        modelView = name;

        $.ajax({
            url: url,
        }).done(function(data) {
            buildFieldList(data);
        });
    }

    /**
     * hapus semua rows
     */
    function removeAllRows() {
        while (tbody.firstChild) {
            tbody.removeChild(tbody.firstChild);
        }
    }

    /**
     * render fields utk table crud
     */
    function renderFields(data) {

        //reset
        modelPk = [];
        fieldsSelected = [];
        modelUpdateFields = [];

        //buat row utk setiap field
        data.forEach(function (value, idx) {
            const field = value.Field;
            const key = value.Key;
            const type = value.Type;
            const extra = value.Extra;

            const isAutoInc = extra.localeCompare('auto_increment')==0 ? true : false;
            const isPk = key.localeCompare('PRI')==0 ? true : false; //primary key

            //converikan type mysql ke type internal
            newType = convertType(type);

            //insert field ke array
//            fieldsPick.push(field);
//            fieldsAutoInc.push(isAutoInc);

            const o = {}; //empty object
            fieldsSelected.push(o);
            o.field = field;
            o.type = newType;
            o.typeOri = type; //original type
            o.autoInc = isAutoInc;
            o.extra = extra;
            o.selected = true;
            o.isNull = value.Null;

            const row = tbody.insertRow();

            //col1 = checkbox
            //setiap kali pilih table, otomatis di select semua field
            const col1 = document.createElement("td");

            //apabila pk maka tdk bisa di uncheck
            if (isPk) disabled = 'disabled'; else disabled='';
            col1.innerHTML = "<input id='cb"+idx+"' type='checkbox' value='"+idx+"' onchange='onChangeField(this, "+idx+")' checked "+disabled+"/>";
            row.appendChild(col1);

            //col2 = field name
            const col2 = document.createElement("td");
            col2.textContent = field;
            row.appendChild(col2);

            //colr3 = primary key
            const col3 = document.createElement("td");
            if (isPk){
                //set pk, pk hanya bisa di set 1 saja
                modelPk.push(field);
                col3.innerHTML = '<i class="fa fa-key" aria-hidden="true"></i>';
            } else {
                col3.innerHTML = '';
            }
            row.appendChild(col3);

            //type
            const col4 = document.createElement("td");
            col4.innerHTML = buildSelectType(newType, idx);
            row.appendChild(col4);

//            modelUpdateFields.push(field);
        });

        updateJson();
    }

    /**
     * field list di pakai utk table view
     *
     * @param data
     */
    function buildFieldList(data) {

        modelFieldList = '';

        //buat row utk setiap field
        data.forEach(function (value, idx) {
            const field = value.Field;

            if (modelFieldList.length==0){
                modelFieldList = "'"+field+"'";
            } else {
                modelFieldList += ",'"+field+"'";
            }
        });

        updateJson();
    }

    /**
     * buat select utk data type
     *
     *
     * @param idx
     */
    const arType = ['varchar', 'numeric', 'filemanager', 'hidden'];
    function buildSelectType(type, idx) {

        var option = '';
        arType.forEach(function(value, idx){
//            if (value==type) selected = 'selected'; else selected = '';
            selected = (value == type) ? 'selected' : '';
            option += "<option value='"+value+"' "+selected+">"+value+"</option>";
        });

        return "<select id='sel"+idx+"' index='"+idx+"' onchange='onChangeType(this, "+idx+")'>" + option + "</select>"
    }

    /**
     * conversikan type dari db ke type internal formBuilder
     *
     * @param actualType
     * @returns {*}
     */
    function convertType(actualType) {
        if (actualType.includes('varchar')) return 'varchar';
        if (actualType.includes('int')) return 'numeric';
        if (actualType.includes('smallint')) return 'numeric';
        return 'varchar';
    }

    function updateJson() {

        const obj = JSON.parse(json.value);

        //update form
        const updateFields = []; //reset
        const fields = [];

        for(idx=0; idx<fieldsSelected.length; idx++){
            const field = fieldsSelected[idx];

            //skip apabila field kosong
            if (field.selected==false) continue;

            const o = {};
            o.field = field.field;
            o.type = field.type;
            o.autoInc = field.autoInc;

            //apabila field tdk boleh null dan autoInc==false, maka required harus di set
            if (field.isNull.includes('NO') && o.autoInc==false) o.required = 'required';

            //insert ke dalam object
            fields.push(o);

            //apabila autoinc == true, maka tdk boleh di masukkan pada updateFields
            if (o.autoInc==false){
                updateFields.push(o.field);
            }
        }

        //pindahkan semua value ke obj
        obj.model.table = modelTable;
        obj.model.pk = modelPk;
        obj.model.updateFields = updateFields;
        obj.model.view = modelView;
        obj.model.fieldList = modelFieldList;
        obj.form.fields = fields;

        //replace json dari obj
        json.value = JSON.stringify(obj, null, 4);
    }
</script>