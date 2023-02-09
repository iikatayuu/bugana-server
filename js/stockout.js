
$(document).ready(function () {
  const token = sessionStorage.getItem('token')
  if (token === null) {
    window.location.href = '/'
    return
  }

  const tempItem = $('#temp-item').prop('content')
  const tempPageBtn = $('#temp-page-btn').prop('content')
  let farmerSort = ''
  let productSort = ''
  let stockOutSort = ''
  let category = 'all'
  let page = 1
  let limit = parseInt($('#limit-page').val())
  let searchQ = $('#search-q').val()

  async function displayStocks () {
    $('#inventory').empty()

    const params = new URLSearchParams()
    params.set('token', token)
    params.set('page', page.toString())
    params.set('limit', limit.toString())
    params.set('category', category)
    if (searchQ) params.set('search', searchQ)
    if (farmerSort) params.set('farmer.sort', farmerSort)
    if (productSort) params.set('product.sort', productSort)
    if (stockOutSort) params.set('stockout.sort', stockOutSort)

    const response = await $.ajax(`/api/admin/stock/stockout.php?${params.toString()}`, {
      method: 'get',
      dataType: 'json'
    })
    if (!response.success) return

    $('#pages').empty()
    if (response.prev) $('[data-prev]').removeClass('d-none').attr('data-page', page - 1)
    else $('[data-prev]').addClass('d-none')

    if (response.next) $('[data-next]').removeClass('d-none').attr('data-page', page + 1)
    else $('[data-next]').addClass('d-none')

    let i = page > 2 ? page - 2 : 1
    if (i > 2) $('#pages').append('<span class="mr-2">...</span>')
    $('[data-page="1"]').toggleClass('active', page === i && page === 1)

    for (i; i < page + 3; i++) {
      if (i <= 1 || i > response.pages) continue

      const elem = $(tempPageBtn).clone(true, true)
      $(elem).find('[data-page]').attr('data-page', i).text(i)
      if (page === i) $(elem).find('[data-page]').addClass('active')
      $('#pages').append(elem)
    }

    if (i <= response.pages) {
      const elem = $(tempPageBtn).clone(true, true)
      $(elem).find('[data-page]').attr('data-page', response.pages).text(response.pages)
      if (i !== response.pages) $('#pages').append('<span class="mr-2">...</span>')
      $('#pages').append(elem)
    }

    for (let i = 0; i < response.stocks.length; i++) {
      const stock = response.stocks[i]
      const elem = $(tempItem).clone(true, true)

      let categoryStr = ''
      let status = ''

      switch (stock.product.category) {
        case 'vegetable':
          categoryStr = 'Vegetable'
          break

        case 'root-crops':
          categoryStr = 'Root Crops'
          break

        case 'fruits':
          categoryStr = 'Fruits'
          break
      }

      switch (stock.status) {
        case 'pending':
          status = 'Pending'
          break

        case 'approved':
          status = 'Approved'
          break

        case 'sold':
        case 'success':
          status = 'Sold'
          break
        
        case 'perished':
          status = 'Perished'
          break
      }

      const dateStr = dateFormat(stock.date)
      $(elem).find('.item-farmer-name').text(stock.username)
      $(elem).find('.item-category').text(categoryStr)
      $(elem).find('.item-product-name').text(stock.product.name)
      $(elem).find('.item-stock-out-date').text(dateStr)
      $(elem).find('.item-quantity').text(parseFloat(stock.quantity) * -1)
      $(elem).find('.item-price').text(commaNumber(stock.product.price.toFixed(2)))
      $(elem).find('.item-product-revenue').text(commaNumber(stock.revenue))
      $(elem).find('.item-status').text(status)
      $('#inventory').append(elem)
    }
  }

  let searchTimer = null
  let limitTimer = null

  $('#search-q').on('keyup', function () {
    const value = $(this).val()
    clearTimeout(searchTimer)
    searchTimer = setTimeout(function () {
      searchQ = value
      page = 1
      displayStocks()
    }, 1250)
  })

  $('#search-q').on('keydown', function () {
    if (searchTimer) clearTimeout(searchTimer)
  })

  $('#limit-page').on('keyup', function () {
    const value = $(this).val()
    clearTimeout(limitTimer)
    limitTimer = setTimeout(function () {
      limit = parseInt(value)
      page = 1
      displayStocks()
    }, 1250)
  })

  $('#limit-page').on('keydown', function () {
    if (limitTimer) clearTimeout(limitTimer)
  })

  $('#sort-farmer-ascending').click(function () {
    farmerSort = 'asc'
    $('.dropdown-content.active').removeClass('active')
    displayStocks()
  })

  $('#sort-farmer-descending').click(function () {
    farmerSort = 'desc'
    $('.dropdown-content.active').removeClass('active')
    displayStocks()
  })

  $('#sort-product-ascending').click(function () {
    productSort = 'asc'
    $('.dropdown-content.active').removeClass('active')
    displayStocks()
  })

  $('#sort-product-descending').click(function () {
    productSort = 'desc'
    $('.dropdown-content.active').removeClass('active')
    displayStocks()
  })

  $('#sort-stockout-ascending').click(function () {
    stockOutSort = 'asc'
    $('.dropdown-content.active').removeClass('active')
    displayStocks()
  })

  $('#sort-stockout-descending').click(function () {
    stockOutSort = 'desc'
    $('.dropdown-content.active').removeClass('active')
    displayStocks()
  })

  $('#products-category-select').on('change', function () {
    const value = $(this).val()
    page = 1
    category = value
    displayStocks()
  })

  $(document).on('click', '[data-page]', function (event) {
    const target = event.currentTarget
    const value = $(target).attr('data-page')
    page = parseInt(value)
    displayStocks()
  })

  displayStocks()
})
