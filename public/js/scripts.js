(function() {
  $(function() {
    jQuery.writeCookie = function(name, value, days) {
      var date, expires;
      expires = '';
      if (days) {
        date = new Date();
        date.setTime(date.getTime() + (days * 86400000));
        expires = "; expires=" + date.toGMTString();
      }
      document.cookie = name + "=" + value + expires + "; path=/";
      return true;
    };
    jQuery.readCookie = function(name) {
      var c, ca, i, nameEQ, _ref;
      nameEQ = name + "=";
      ca = document.cookie.split(';');
      for (i = 0, _ref = ca.length; 0 <= _ref ? i < _ref : i > _ref; 0 <= _ref ? i++ : i--) {
        c = ca[i];
        while (c.charAt(0) === ' ') {
          c = c.substring(1, c.length);
        }
        if (c.indexOf(nameEQ) === 0) {
          return c.substring(nameEQ.length, c.length);
        }
      }
      return null;
    };
    $('#intro .hide-info').click(function() {
      $('#intro').slideUp('fast', function() {
        $.writeCookie('hide-intro', true);
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
        selected = $('.categories .selected');
        categories = $('#bookbark-widget .categories');
        category = $('<span class="btn small" />;');
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
      return true;
    });
    $('.categories .columns').click(function(e) {
      if (e.ctrlKey === true) {
        e.preventDefault();
        $(this).toggleClass('selected');
      }
      return true;
    }).find('span').click(function() {
      var category, element, manager, star;
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
        }).data('category-id', category.data('id')).data('category', category);
        star = manager.find('.star').removeClass('on');
        if (category.data('marked')) {
          star.addClass('on');
        }
        $('.categories .columns').removeClass('active');
        category.addClass('active');
        manager.show();
      }
      return true;
    });
    $('#category-manager .star').click(function() {
      var marked, star;
      star = $(this);
      star.toggleClass('on');
      marked = star.hasClass('on');
      $.get('/kategorie/' + $('#category-manager').data('category-id') + '/ustaw-zaznaczenie/' + (marked ? 'tak' : 'nie'), function() {
        var currentContainer, targetContainer;
        if (marked) {
          targetContainer = $('#marked-categories');
          currentContainer = $('#normal-categories');
        } else {
          targetContainer = $('#normal-categories');
          currentContainer = $('#marked-categories');
        }
        $('#category-manager').data('category').appendTo(targetContainer.find('.categories')).data('marked', marked);
        if (currentContainer.find('.columns').length === 0) {
          currentContainer.hide();
        }
        if (!targetContainer.is(':visible')) {
          targetContainer.show();
        }
        return true;
      });
      return true;
    });
    $('#load').ajaxStart(function() {
      return $(this).show();
    }).ajaxStop(function() {
      return $(this).hide();
    });
    $(document).click(function(e) {
      if (!$(e.target).hasClass('category-actions')) {
        $('#category-manager').hide();
        $('.categories .columns').removeClass('active');
        return true;
      }
    });
    $(document).keyup(function(e) {
      if (e.keyCode === 83 && !$(e.target).is('input')) {
        $('#category-manager').hide();
        $('#search').show();
        $('#search-input').val('').focus().trigger('keyup');
      }
      if (e.keyCode === 27) {
        $('#search').hide();
        return $('#search-input').val('').blur().trigger('keyup');
      }
    });
    $('#search-input').keyup(function() {
      var val;
      val = $(this).val();
      if (val.length > 0) {
        $('.categories .columns:not(:contains("' + val + '"))').hide();
        $('.categories .columns:contains("' + val + '")').show();
      } else {
        $('.categories .columns').show();
      }
      $('#category-manager').hide();
      return true;
    });
    return true;
  });
}).call(this);
