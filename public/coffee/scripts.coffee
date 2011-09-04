$ ->
  jQuery.writeCookie = (name, value, days)->
    expires = ''
    if days
      date = new Date()
      date.setTime(date.getTime()+(days*86400000))
      expires = "; expires="+date.toGMTString()
    document.cookie = name+"="+value+expires+"; path=/"
    true
  
  jQuery.readCookie = (name)->
    nameEQ = name + "="
    ca = document.cookie.split(';')
    for i in [0...ca.length]
      c = ca[i]
      while c.charAt(0) is ' '
        c = c.substring(1, c.length)
      if c.indexOf(nameEQ) is 0
        return c.substring(nameEQ.length, c.length)
    null

  $('#intro .hide-info').click ->
    $('#intro').slideUp 'fast', ->
      $.writeCookie('hide-intro', true)
      true
    true

  $('#bookbark-widget .close, #bookbark-widget .cancel').click ->
    $('#bookbark-widget').hide('fast')
    true

  $('#bookbark-widget .add-category').keyup (e)->
    if e.keyCode is 13
      e.preventDefault()
      el = $('<span class="btn small" />').text($(this).val())
      $('#bookbark-widget .categories').append(el).show()
    true
  
  $('#add-button').click ->
    $('#bookbark-widget').show ->
      selected = $('.categories .selected')
      categories = $('#bookbark-widget .categories')
      category = $('<span class="btn small" />;')
      if selected.length > 0
        categories.html('')
        selected.each ->
          cat = category.clone().text($(this).text())
          categories.append(cat)
          true
        categories.show()
      else
        categories.hide()
      true
    true

  $('.categories .columns').click (e)->
    if e.ctrlKey is true
      e.preventDefault()
      $(this).toggleClass('selected')
    true
  .find('span').click ->
    element = $(this)
    manager = $('#category-manager')
    category = element.parents('.columns')
    if manager.data('category-id') is category.data('id') and manager.is(':visible')
      manager.hide()
      category.removeClass('active')
    else
      manager.css({
        top: element.offset().top + 16
        left: element.offset().left + element.outerWidth() / 2 - manager.outerWidth() / 2
        })
      .data('category-id', category.data('id')).data('category', category)
      star = manager.find('.star').removeClass('on')
      if category.data('marked')
        star.addClass('on')
      $('.categories .columns').removeClass('active')
      category.addClass('active')
      manager.show()
    true
  
  $('#category-manager .star').click ->
    star = $(this)
    star.toggleClass('on')
    marked = star.hasClass('on')
    $.get('/kategorie/'+$('#category-manager').data('category-id')+'/ustaw-zaznaczenie/'+( if marked then 'tak' else 'nie'), ->
      if marked
        targetContainer   = $('#marked-categories')
        currentContainer  = $('#normal-categories')
      else 
        targetContainer   = $('#normal-categories')
        currentContainer  = $('#marked-categories')
      $('#category-manager').data('category').appendTo(targetContainer.find('.categories')).data('marked', marked)
      if currentContainer.find('.columns').length is 0
        currentContainer.hide()
      if not targetContainer.is(':visible')
        targetContainer.show()
      true
    )
    true

  $('#load')
  .ajaxStart(-> $(this).show())
  .ajaxStop(-> $(this).hide())

  $(document).click (e)->
    if not $(e.target).hasClass('category-actions')
      $('#category-manager').hide()
      $('.categories .columns').removeClass('active')
      true

  $(document).keyup (e)->
    if e.keyCode is 83 and not $(e.target).is('input')
      $('#category-manager').hide()
      $('#search').show()
      $('#search-input').val('').focus().trigger('keyup')
    if e.keyCode is 27
      $('#search').hide()
      $('#search-input').val('').blur().trigger('keyup')

  $('#search-input').keyup ->
    val = $(this).val()
    if val.length > 0
      $('.categories .columns:not(:contains("'+val+'"))').hide()
      $('.categories .columns:contains("'+val+'")').show()
    else
      $('.categories .columns').show()
    $('#category-manager').hide()
    true
 
  true
