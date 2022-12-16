var globalTableList;
jQuery('document').ready(function()
{
    var arraycolumndisplay = globalTableList.columns().visible();
    var arraycolumnname    = globalTableList.columns().header().toArray().map(x => x.innerText);
    for(var i=0; i < (arraycolumndisplay.length - 1);i++)
    {
        var checked = '';
        if(arraycolumndisplay[i]) checked ='checked';
        jQuery('.checkboxdisplay').append('<div class="col-xl-3 col-lg-3 col-md-3 col-sm-3"><label class="container">'+arraycolumnname[i]+'<input type="checkbox" '+checked+' data-column="'+i+'" class="toggle-vis"><span class="checkmark"></span></label></div>')
    }
    
    jQuery('.toggle-vis').change(function() 
    {
        var column = globalTableList.column( $(this).attr('data-column') );
        column.visible( ! column.visible() );
    });

    jQuery('.showOptionsModal').click(function()
    {  
        jQuery('#modal-checkbox').modal(); 
    });

    jQuery(document.body).on( 'click', '#newForm .input-group-addon', function (event)
    {
        jQuery('#modal-galery').modal();
        var uriidinput        = jQuery(this).attr('data-id');
        localStorage.setItem('input-name',uriidinput);
        localStorage.setItem('form-active',"#newForm");
        var filemanagerurl    = jQuery('#modal-galery').find('iframe').attr('src');
        var filemanagerurlnew = uriquerystring.updateQueryStringParameter(filemanagerurl,'field_id',uriidinput);
        jQuery('#modal-galery').find('iframe').attr('src',filemanagerurlnew)
    });

    jQuery(document.body).on( 'click', '#editForm .input-group-addon', function (event)
    {
        jQuery('#modal-galery').modal();
        var uriidinput        = jQuery(this).attr('data-id');
        localStorage.setItem('input-name',uriidinput);
        localStorage.setItem('form-active',"#editForm");
        var filemanagerurl    = jQuery('#modal-galery').find('iframe').attr('src');
        var filemanagerurlnew = uriquerystring.updateQueryStringParameter(filemanagerurl,'field_id',uriidinput);
        jQuery('#modal-galery').find('iframe').attr('src',filemanagerurlnew)
    });
    
    jQuery('#modal-galery').on('hidden.bs.modal', function (event) 
    {
        var uriidinput  = localStorage.getItem('input-name');
        var formactive  = localStorage.getItem('form-active');
        var newurlvalue = jQuery("input[name="+uriidinput+"]").val();
        if(newurlvalue.length > 0)
        {
            var imagetoupload  = newurlvalue.replace(/[\[\]["]+/g,'').split(",");
            $.each(imagetoupload,function(index,value)
            {
                $('.images-preview').prepend('<div class="img" style="background-image: url(\'' + value + '\');"><span>remove</span></div>');
            });

            jQuery("input[name="+uriidinput+"]").val("");
            var elementimages = jQuery(formactive + " input[name="+uriidinput+"]").parent().next().children();
            var newarrayimage = [];
            $.each(elementimages,function(idx, val)
            {
                var imagestoupload = $(this).css("background-image");
                var stringimageulr = imagestoupload.replace('url(','').replace(')','').replace(/\"/gi, "");
                newarrayimage.push(stringimageulr);
             });
             jQuery(formactive + " input[name="+uriidinput+"]").val(newarrayimage.toString());
        }
    });

    jQuery(document.body).on( 'click', '.images-preview .img', function (event)
    {
        var formid     = $(this).closest("form").attr('id');
        var uriidinput = $(this).parent().prev("div").children("input").attr("name");
        var background = jQuery(this).css('background-image');
        var urlimage   = background.replace('url(','').replace(')','').replace(/\"/gi, "");
        var urlvalue   = jQuery("#"+formid+ " input[name="+uriidinput+"]").val().split(",");
        var newurl     = [];
        $.each(urlvalue, function( index, value ) 
        {
            if(value != urlimage)
            {
                newurl.push(value);
            }
        });
        jQuery("#"+formid+" input[name="+uriidinput+"]").val(newurl.toString());
        $(this).remove();
    });

    jQuery('#datalist tbody').on("click", ".popupimage", function(event) 
    {
        event.stopPropagation();
        $('.imagepreview').attr('src', $(this).find('img').attr('src'));
        $('#imagemodal').modal('show');
    });

    jQuery(document.body).on("click", ".toggle-password", function(event)
    {
        jQuery(this).toggleClass("fa-eye fa-eye-slash");
        var input = jQuery(jQuery(this).attr("toggle"));
        if (input.attr("type") == "password") 
        {
            input.attr("type", "text");
        } 
        else 
        {
            input.attr("type", "password");
        }
    });
    
});