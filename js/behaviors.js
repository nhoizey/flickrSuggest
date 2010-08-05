$(document).ready(function() {
  $('.ignore').bind('click', function() {
    var $this = $(this);
    var $photo = $this.parent().parent();
    $photo.append('<div class="status"><p>Ignoring this photo...</p><img src="img/indicator.gif" width="16" height="16" /></div>');
    $photo.find('img').animate({ opacity: 0.25 }, 2000);
    var $href = $this.attr('href') + '&mode=ajax';
    $.get($href, function(data) {
      if (data == '1') {
        $photo.animate({ opacity: 0 }, 1000, function() {
          $(this).remove();
        });
      } else {
        $photo.find('.status').html('<p><strong>Couldn\'t ignore this photo!</strong></p><p>Message:<br />' + data + '</p>');
        $photo.find('img').animate({ opacity: 0.75 }, 2000, function () {
          $photo.find('.status').remove();
        });
      }
    });
    return false;
  });
  $('<p style="float: right; clear: left;"><a href="">ignore all</a></p>').insertBefore('.pager').find('a').bind('click', function () {
    $('.ignore').click();
    return false;
  });
});