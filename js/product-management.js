
$(document).ready(function () {
  const token = sessionStorage.getItem('token')
  if (token === null) {
    window.location.href = '/'
    return
  }

  const tempProduct = $('#temp-product').prop('content')
  let category = 'all'
  let page = 1
  let products = []

  async function displayProducts () {
    $('#products').empty()

    const params = new URLSearchParams()
    params.set('page', page)
    params.set('category', category)
    const response = await $.ajax('/api/product/list.php?' + params.toString(), {
      method: 'get',
      dataType: 'json'
    })

    if (!response.success) return

    $('[data-next]').attr('disabled', response.next ? null : true)
    $('[data-prev]').attr('disabled', response.prev ? null : true)

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

      $(elem).find('.product-code').text(product.code)
      $(elem).find('.product-name').text(product.name)
      $(elem).find('.product-category').text(category)
      $(elem).find('.product-description').text(product.description)
      $(elem).find('.product-added').text(product.created)
      $(elem).find('.product-edited').text(product.edited)
      $(elem).find('.product-action-edit').attr('href', (index, attr) => attr + product.id)
      $(elem).find('.product-action-archive').attr('data-index', 1).click(archiveProduct)
      $('#products').append(elem)
    }
  }

  async function archiveProduct (event) {
    event.preventDefault()
  }

  $('[data-prev]').click(function (event) {
    event.preventDefault()
    page--
    displayProducts()
  })

  $('[data-next]').click(function (event) {
    event.preventDefault()
    page++
    displayProducts()
  })

  $('#products-category-select').on('change', function () {
    const value = $(this).val()
    page = 1
    category = value
    displayProducts()
  })

  displayProducts()
})
