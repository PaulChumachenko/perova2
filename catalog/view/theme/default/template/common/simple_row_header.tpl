<tr>
  <td class="simplecheckout-customer-left" colspan="2">
    <h4><?php echo $label ?></h4>
	<style type="text/css">
.mymagicoverbox {
  display:inline-block;
  color:#ffffff;
  padding:10px;
  margin:10px;
  cursor:pointer;
  font-weight:300;
  font-family:'Roboto';
}
.mymagicoverbox_fenetre {
  z-index:9999;
  position:fixed;
  margin-left:50%;
  top:100px;
  text-align:center;
  display:none;
  padding:5px;
  background-color:#ffffff;
  color:#fea501;
  font-style:normal;
  font-size:20px;
  font-weight:300;
  font-family:'Roboto';
}
.mymagicoverbox_fenetreinterieur {
  text-align:center;
  overflow:auto;
  padding:10px;
  background-color:#ffffff;
  color:#666666;
  font-weight:400;
  font-family:'Roboto';
  font-size:17px;
  border-top:1px solid #e7e7e7;
  margin-top:10px
}
.mymagicoverbox_fermer {
  color:#CB2025;
  cursor:pointer;
  font-weight:400;
  font-size:14px;
  font-style:normal
  font-family:'Roboto';
}
#myfond_gris {
  display: none;
  background-color:#000000;
  opacity:0.7;
  width:100%;
  height:100%;
  z-index:9998;
  position:fixed;
  top:0;
  bottom:0;
  right:0;
  left:0;
}
</style>


<div id="myfond_gris" opendiv=""></div>

<div align="center">

<div style="    background-color: #fea501;
    border-radius: 4px;" iddiv="box_1" class="mymagicoverbox">
Открыть карту
</div>





<div id="box_1" class="mymagicoverbox_fenetre" style="left:-225px; width:450px;">
Режим работы:
Пн-Вс с 9:00 до 17:30
<div class="mymagicoverbox_fenetreinterieur">

<div align="center">
<br>		<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1269.360370659435!2d30.590206368871165!3d50.48354235115606!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNTDCsDI5JzAwLjgiTiAzMMKwMzUnMjguNiJF!5e0!3m2!1sru!2sua!4v1528558964598" width="100%" height="300px" frameborder="1" style="border:0" allowfullscreen=""></iframe>
<br><br>


<div style="width:100px" align="center" class="mymagicoverbox_fermer">ЗАКРЫТЬ</div>


</div>
</div>
</div>


<link href='https://fonts.googleapis.com/css?family=Roboto:100,400,300,500,700' rel='stylesheet' type='text/css'>



</div>
</div>

<script type="text/javascript">
$(document).ready(function(){

$(".mymagicoverbox").click(function()
{
        $('#myfond_gris').fadeIn(300);
        var iddiv = $(this).attr("iddiv");
        $('#'+iddiv).fadeIn(300);
        $('#myfond_gris').attr('opendiv',iddiv);
        return false;
});

$('#myfond_gris, .mymagicoverbox_fermer').click(function()
{
        var iddiv = $("#myfond_gris").attr('opendiv');
        $('#myfond_gris').fadeOut(300);
        $('#'+iddiv).fadeOut(300);
});

});
</script>
  </td>
</tr>