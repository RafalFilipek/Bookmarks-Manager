(function() {
  $(function() {
    $('#intro .hide-info').click(function() {
      $('#intro').slideUp('fast', function() {
        $.cookie('hide-intro', true);
        return true;
      });
      return true;
    });
    $('#bookbark-widget .close, #bookbark-widget .cancel').click(function() {
      $('#bookbark-widget').hide('fast');
      return true;
    });
    $('#bookbark-widget .add-category').keyup(function(e) {
      var el;
      if (e.keyCode === 13) {
        e.preventDefault();
        el = $('<span class="btn small" />').text($(this).val());
        $('#bookbark-widget .categories').append(el).show();
      }
      return true;
    });
    $('#add-button').click(function() {
      $('#bookbark-widget').show(function() {
        var categories, category, selected;
        selected = $('#categories .selected');
        categories = $('#bookbark-widget .categories');
        category = $('<span class="btn small" />;');
        return true;
      });
      if (selected.length > 0) {
        categories.html('');
        selected.each(function() {
          var cat;
          cat = category.clone().text($(this).text());
          categories.append(cat);
          return true;
        });
        categories.show();
      } else {
        categories.hide();
      }
      return true;
    });
    $('#categories .columns').click(function(e) {
      if (e.ctrlKey === true) {
        e.preventDefault();
        $(this).toggleClass('selected');
      }
      return true;
    }).find('span').click(function() {
      var category, element, manager;
      element = $(this);
      manager = $('#category-manager');
      category = element.parents('.columns');
      if (manager.data('category-id') === category.data('id') && manager.is(':visible')) {
        manager.hide();
        category.removeClass('active');
      } else {
        manager.css({
          top: element.offset().top + 16,
          left: element.offset().left + element.outerWidth() / 2 - manager.outerWidth() / 2
        }).data('category-id', category.data('id')).show();
        $('#categories .columns').removeClass('active');
        category.addClass('active');
      }
      return true;
    });
    $('#category-manager .star').click(function() {
      $(this).toggleClass('on');
      return true;
    });
    return true;
  });
}).call(this);
