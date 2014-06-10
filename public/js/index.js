function setBarWidth(dataElement, barElement, cssProperty, barPercent) {
  var listData = []
  $(dataElement).each(function() {
    listData.push($(this).html());
  });
  var listMax = Math.max.apply(Math, listData);
  $(barElement).each(function(index) {
    var width = (listData[index]/listMax) * barPercent + '%';
    $(this).css(cssProperty, width);
  });
}
setBarWidth('.style-1 span.monitor-count', '.style-1 em', 'padding-right', 50);
setBarWidth('.style-2 span', '.style-2 span', 'padding-right', 50);
