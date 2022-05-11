<div class="wait-loader display-no" id="quotes-main-loader"><img  src="<?php echo base_url() ?>assets/admin/img/ajax-loader.gif" align="absmiddle"  ></div>
<div class="page-footer">
    <div class="page-footer-inner">
          <?php echo $this->lang->line('copyright');?>&copy; <?php echo date('Y');?>  <?php echo $this->lang->line('site_footer');?>
    </div>
    <div class="page-footer-tools">
        <span class="go-top">
        <i class="fa fa-angle-up"></i>
        </span>
    </div>
</div>
<!-- END footer -->
</body>
<!-- END BODY -->
<script type="text/javascript">

function notifyMe() {
  // Let's check if the browser supports notifications
  if (!("Notification" in window)) {
    alert("This browser does not support desktop notification");
  }
  // Otherwise, we need to ask the user for permission
  else if (Notification.permission !== "granted") {
    Notification.requestPermission().then(function (permission) {
      // If the user accepts, let's create a notification
      if (permission === "granted") {
        var notification = new Notification("NOUVEAU COMMANDE!!", {
          body: "Veuillez verifier la liste des commandes."
        });
        notification.onclick = function() {
           window.open('https://www.e-sakafo.mg/backoffice/order/view');
        };
      }
    });
  }
  // Let's check whether notification permissions have already been granted
  else if (Notification.permission === "granted") {
    // If it's okay let's create a notification
    var notification = new Notification("NOUVEAU COMMANDE!!", {
          body: "Veuillez verifier la liste des commandes."
        });
        notification.onclick = function() {
           window.open('https://www.e-sakafo.mg/backoffice/order/view');
    };
  }

  
}

$(document).ready(function(){
    /*var obj = document.createElement("audio");
                    obj.src = "<?php //echo base_url() ?>assets/admin/img/notification_sound.wav"; 
                    obj.play(); */

   var i = setInterval(function(){
      jQuery.ajax({
        type : "POST",
        dataType : "json",
        async: false,
        url : '<?php echo base_url().ADMIN_URL?>/dashboard/ajaxNotification',
        success: function(response) {
            var past_count = $('.notification span.count').html();
            if(response != null){
              if(response.order_count != '' && response.order_count != null){
                if(past_count < response.order_count){
                    notifyMe();
                    var obj = document.createElement("audio");
                    obj.src = "<?php echo base_url() ?>assets/admin/img/notification_sound.wav"; 
                    obj.play(); 
                }
                var count = (response.order_count >= 100)?'99+':response.order_count;
                $('.notification span.count').html(count);
              }
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {           
        }
      });
    },10000);
    
   
});
function changeViewStatus(){
    jQuery.ajax({
        type : "POST",
        dataType : "html",
        url : '<?php echo base_url().ADMIN_URL?>/dashboard/changeViewStatus',
        success: function(response) {
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {           
        }
    });
}
</script>
<?php if($this->session->userdata("language_slug")=='ar'){  ?>
<script type="text/javascript" src="<?php echo base_url()?>assets/admin/pages/scripts/localization/messages_ar.js"> </script>
<?php } ?>
<?php if($this->session->userdata("language_slug")=='fr'){  ?>
<script type="text/javascript" src="<?php echo base_url()?>assets/admin/pages/scripts/localization/messages_fr.js"> </script>
<?php } ?>
<?php if($this->session->userdata("language_slug")=='bn'){  ?>
<script type="text/javascript" src="<?php echo base_url()?>assets/admin/pages/scripts/localization/messages_bn.js"> </script>
<?php } ?>
</html>