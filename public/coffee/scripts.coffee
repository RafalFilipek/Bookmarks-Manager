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

  $('#bookmark-widget .close, #bookmark-widget .cancel').click ->
    $('#bookmark-widget').hide('fast')
    true

  $('#bookmark-widget .add-category').keyup (e)->
    if e.keyCode is 13
      e.preventDefault()
      el = $('<span class="btn small" />').text($(this).val())
      $('#bookmark-widget .categories').append(el).show()
    true
  
  $('#add-button').click ->
    $('#bookmark-widget').show ->
      selected = $('.categories .selected')
      categories = $('#bookmark-widget .categories')
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
    else
      if $(e.srcElement).hasClass('category-actions')
        return
      $(this).toggleClass('expanded').parent().isotope('reLayout');
    true
  .find('span').live 'click', ->
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
      .data('category-id', category.data('id'))
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
    $.get(Router.get('mark_category', { id : $('#category-manager').data('category-id'), bool : (if marked then 1 else 0)}), ->
      if marked
        targetContainer   = $('#marked-categories')
        currentContainer  = $('#normal-categories')
      else 
        targetContainer   = $('#normal-categories')
        currentContainer  = $('#marked-categories')
      id = $('#category-manager').data('category-id')
      category = $('#category-' + id)
      category.data('marked', marked)
      targetContainer.find('.categories').isotope('insert', category.clone(true, true))
      currentContainer.find('.categories').isotope('remove', category)
      $('.categories').isotope('reLayout')
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
      $('#search-input').val('').trigger('keyup').focus()
    if e.keyCode is 27
      $('#search').hide()
      $('#search-input').val('').trigger('keyup').blur()

  $('#search-input').keyup ->
    val = $(this).val()
    type = if (val.length > 0 and val[0] is '#') then 'categories' else 'bookmarks'
    val = val.replace('#', '')
    l = val.length
    if l is 0
      $('.categories').isotope({filter: '*'})
      true
    if type is 'categories'
      $('.categories').isotope({filter: ':contains("'+val+'")'})
    $('#category-manager').hide()
    true
  
  $('.categories').isotope({
    itemSelector: '.columns'
    masonry: {
      columnWidth: 240
    }
    getSortData: {
      name: (el)-> el.data('name')
    }
    sortBy: 'name'
  })

  $('.categories-block h6 ul a').click (e)->
    e.preventDefault()
    link = $(this)
    layout = link.data('layout')
    categories = link.parents('.categories-block').find('.categories')
    if categories.data('layout') == layout
      return
    link.parents('ul').find('a').toggleClass('active')
    categories.find('.columns').removeClass('expanded')
    categories.toggleClass('grid list').data('layout', layout).isotope({layoutMode: layout})


  true