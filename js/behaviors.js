$(document).ready(function() {
  $('.ignore').bind('click', function(){
    var $this = $(this);
    $this.after('<img src="img/indicator.gif" width="16" height="16" />');
    href = $this.attr('href') + '&mode=ajax';
    $.get(href, function(data){
      if (data == '1') {
        $this.parent().parent().remove();
      } else {
        alert('Couldn\'t put it in the ignore list!' + "\n" + 'Message: ' + data);
      }
    });
    return false;
  });
});