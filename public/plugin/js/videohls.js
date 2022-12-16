jQuery(function()
{
  jQuery("#datalist").on('click', '.play-video-datatable', function()
  {
    var urlstream    = $(this).children("img").attr("data-url");
    var urldatatitle = $(this).children("img").attr("data-title");
    if (Hls.isSupported()) 
    {
      video = document.getElementById('video');
      globalHls.attachMedia(video);
      globalHls.on(Hls.Events.MEDIA_ATTACHED, function () 
      {
        globalHls.loadSource(urlstream);
        globalHls.on(Hls.Events.MANIFEST_PARSED, function (event, datavideo) 
        {
            $("#videomodal .modal-header h5").empty();
            jQuery('#videomodal').modal({backdrop: false});
            $("#videomodal .modal-header").prepend("<div class=col-8><h5>"+ urldatatitle + "</h5></div>");
            video.play();
        });
      });
    }
  });
  jQuery(document.body).on('hidden.bs.modal', '#videomodal', function () 
  {
    globalHls.detachMedia()
  });
});