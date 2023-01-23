
$(document).ready(function () {
  const token = sessionStorage.getItem('token')
  if (token === null) {
    window.location.href = '/'
    return
  }

  const tempProduct = $('#temp-product').prop('content')
  const tempPageBtn = $('#temp-page-btn').prop('content')
  let productSort = ''
  let priceSort = ''
  let category = 'all'
  let page = 1
  let limit = parseInt($('#limit-page').val())
  let userCode = $('#user-search').val()
  let products = []

  async function displayProducts () {
    $('#products').empty()

    const params = new URLSearchParams()
    params.set('page', page.toString())
    params.set('limit', limit.toString())
    params.set('category', category)
    if (userCode) params.set('farmer', userCode)
    if (productSort) params.set('product.sort', productSort)
    if (priceSort) params.set('price.sort', priceSort)

    const response = await $.ajax('/api/product/list.php?' + params.toString(), {
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

    products = response.products
    for (let i = 0; i < products.length; i++) {
      const product = products[i]
      const elem = $(tempProduct).clone(true, true)
      let category = ''
      switch (product.category) {
        case 'vegetable':
          category = 'Vegetable'
          break

        case 'root-crops':
          category = 'Root Crops'
          break

        case 'fruits':
          category = 'Fruits'
          break
      }

      const createdDate = dateFormat(product.created)
      $(elem).find('.product-farmer-name').text(product.farmername)
      $(elem).find('.product-name').text(product.name)
      $(elem).find('.product-category').text(category)
      $(elem).find('.product-description').text(product.description)
      $(elem).find('.product-price').text(product.price)
      $(elem).find('.product-added').text(createdDate)
      $(elem).find('.product-edited').text(product.edited)
      $(elem).find('.product-action-edit').attr('href', (index, attr) => attr + product.id)
      $('#products').append(elem)
    }
  }

  $('#products-category-select').on('change', function () {
    const value = $(this).val()
    page = 1
    category = value
    displayProducts()
  })

  let codeTimer = null
  let limitTimer = null

  $('#user-search').on('keyup', function () {
    const value = $(this).val()
    clearTimeout(codeTimer)
    codeTimer = setTimeout(function () {
      userCode = value
      page = 1
      displayProducts()
    }, 1250)
  })

  $('#user-search').on('keydown', function () {
    if (codeTimer) clearTimeout(codeTimer)
  })

  $('#limit-page').on('keyup', function () {
    const value = $(this).val()
    clearTimeout(limitTimer)
    limitTimer = setTimeout(function () {
      limit = parseInt(value)
      page = 1
      displayProducts()
    }, 1250)
  })

  $('#limit-page').on('keydown', function () {
    if (limitTimer) clearTimeout(limitTimer)
  })

  $(document).on('click', '[data-page]', function (event) {
    const target = event.currentTarget
    const value = $(target).attr('data-page')
    page = parseInt(value)
    displayProducts()
  })

  $('#sort-product-ascending').click(function () {
    productSort = 'asc'
    $('.dropdown-content.active').removeClass('active')
    displayProducts()
  })

  $('#sort-product-descending').click(function () {
    productSort = 'desc'
    $('.dropdown-content.active').removeClass('active')
    displayProducts()
  })

  $('#sort-price-ascending').click(function () {
    priceSort = 'asc'
    $('.dropdown-content.active').removeClass('active')
    displayProducts()
  })

  $('#sort-price-descending').click(function () {
    priceSort = 'desc'
    $('.dropdown-content.active').removeClass('active')
    displayProducts()
  })

  displayProducts()
})
