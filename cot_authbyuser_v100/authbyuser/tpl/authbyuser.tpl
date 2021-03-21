<!-- BEGIN: MAIN -->
<div class="block">
  <h2 class="users">Авторизация через пользователя</h2>
  <form action="{PHP|cot_url('admin', 'm=other&p=authbyuser&a=auth')}" method="POST" enctype="multipart/form-data">
    <table class="cells">
      <tr>
        <td>Выберите пользователей</td>
        <td>
          <div id="ueditfindedurrs"></div>
          <input type="text" id="newperfstoadd" />
          <small>Логин или почта пользователя</small>
          <br>
          <div id="ueditrow">

          </div>
        </td>
      </tr>
      <tr>
        <td></td>
        <td><button type="submit">Авторизоваться</button></td>
      </tr>
    </table>
  </form>
</div>
<script>
  var urrs_json = JSON.parse('{PHP.urrs_json}');
  $('#newperfstoadd').change(function() {
   var thisval = $('#newperfstoadd').val();
   var fnd = false;
   var notfoundarr = [ ];
   for(i=0; i < urrs_json.length; i++) {

      if(urrs_json[i]['name'].trim() == thisval.trim() || urrs_json[i]['email'].trim() == thisval.trim()) {
        $('#ueditfindedurrs').html('');
        if($('[data-perf="'+urrs_json[i]['id']+'"]').length == 0) {
            $('#ueditfindedurrs').append('<div style="padding: 5px;margin-bottom: 10px;background-color: #f6f7e4;" data-perf="'+urrs_json[i]['id']+'">'+
                  '<input type="radio" name="id" value="'+urrs_json[i]['id']+'" style="margin-top: 0px;margin-right: 5px;">'+
                  '<a href="users/'+urrs_json[i]['id']+'?m=details" target="_blank">'+urrs_json[i]['name']+'</a> - '+urrs_json[i]['email']+ '</div>');
        }
        $('#newperfstoadd').val(notfoundarr.join("\n"));
      }
   }
  });
</script>
<!-- END: MAIN -->