$(document).ready(function() {
  $('.ignore').bind('click', function() {
    var $this = $(this);
    var $photo = $this.parent().parent();
    $photo.find('img').animate({ opacity: 0.25 }, 1000);
    $photo.append('<div class="status"><p>Ignoring this photo...</p><img src="img/indicator.gif" width="16" height="16" /></div>');
    var $href = $this.attr('href') + '&mode=ajax';
    $.get($href, function(data) {
      if (data == '1') {
        $photo.hide('slow', function() { $(this).remove(); });
      } else {
        alert('Couldn\'t ignore this photo!' + "\n" + 'Message: ' + data);
      }
    });
    return false;
  });
$('.favor').bind('click', function() {
  var $this = $(this);
  var $photo = $this.parent().parent();
  $photo.find('img').animate({ opacity: 0.25 }, 1000);
  $photo.append('<div class="status"><p>Favoring this photo...</p><img src="img/indicator.gif" width="16" height="16" /></div>');
  var $href = $this.attr('href') + '&mode=ajax';
  $.get($href, function(data) {
    if (data == '1') {
      $photo.hide('slow', function() { $(this).remove(); });
    } else {
      alert('Couldn\'t favor this photo!' + "\n" + 'Message: ' + data);
    }
  });
  return false;
});
});