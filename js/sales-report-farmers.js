
$(document).ready(function () {
  const token = sessionStorage.getItem('token')
  if (token === null) {
    window.location.href = '/'
    return
  }

  const tempItem = $('#temp-farmer').prop('content')
  const tempPageBtn = $('#temp-page-btn').prop('content')
  const tempTransaction = $('#temp-transaction').prop('content')
  let userSort = ''
  let salesSort = ''
  let page = 1
  let userCode = $('#user-search').val()
  let farmers = []

  async function displayFarmers () {
    $('#farmers').empty()

    const params = new URLSearchParams()
    params.set('page', page.toString())
    params.set('token', token)
    params.set('sales', '1')
    params.set('view', 'farmers')
    if (userCode) params.set('user', userCode)
    if (userSort) params.set('user.sort', userSort)
    if (salesSort) params.set('sales.sort', salesSort)

    const response = await $.ajax(`/api/admin/users.php?${params.toString()}`, {
      method: 'get',
      dataType: 'json'
    })
    if (!response.success) return

    $('.pages').empty()
    if (response.prev) $('[data-prev]').removeClass('d-none').attr('data-page', page - 1)
    else $('[data-prev]').addClass('d-none')

    if (response.next) $('[data-next]').removeClass('d-none').attr('data-page', page + 1)
    else $('[data-next]').addClass('d-none')

    let i = page > 2 ? page - 2 : 1
    if (i > 2) $('.pages').append('<span class="mr-2">...</span>')
    $('[data-page="1"]').toggleClass('active', page === i && page === 1)

    for (i; i < page + 3; i++) {
      if (i <= 1 || i > response.pages) continue

      const elem = $(tempPageBtn).clone(true, true)
      $(elem).find('[data-page]').attr('data-page', i).text(i)
      if (page === i) $(elem).find('[data-page]').addClass('active')
      $('.pages').append(elem)
    }

    if (i <= response.pages) {
      const elem = $(tempPageBtn).clone(true, true)
      $(elem).find('[data-page]').attr('data-page', response.pages).text(response.pages)
      if (i !== response.pages) $('.pages').append('<span class="mr-2">...</span>')
      $('.pages').append(elem)
    }

    farmers = response.users
    for (let i = 0; i < farmers.length; i++) {
      const farmer = farmers[i]
      const elem = $(tempItem).clone(true, true)
      const sales = parseFloat(farmer.sales).toFixed(2)

      $(elem).find('.farmer-code').text(farmer.code)
      $(elem).find('.farmer-name').text(farmer.name)
      $(elem).find('.total-sales').text(sales)
      $(elem).find('.farmer-actions').attr({
        'data-id': farmer.id,
        'data-index': i
      }).click(showDetails)
      $('#farmers').append(elem)
    }
  }

  async function showDetails (event) {
    event.preventDefault()

    const farmerId = $(this).attr('data-id')
    const index = $(this).attr('data-index')
    const farmer = farmers[index]
    const params = new URLSearchParams()
    params.set('token', token)
    params.set('id', farmerId)
    params.set('farmer', '1')
    const response = await $.getJSON(`/api/transaction/list.php?${params.toString()}`)
    if (!response.success) return

    $('.farmer-details-name').text(farmer.name)
    $('#transactions').empty()

    const transactions = response.transactions
    for (let i = 0; i < transactions.length; i++) {
      const elem = $(tempTransaction).clone(true, true)
      const transaction = transactions[i]
      const user = transaction.user
      const product = transaction.product
      const priceEach = parseFloat(transaction.amount) / parseInt(transaction.quantity)

      $(elem).find('.buyer-name').text(user.name)
      $(elem).find('.transaction-date').text(transaction.date)
      $(elem).find('.product-name').text(product.name)
      $(elem).find('.product-price').text(priceEach.toFixed(2))
      $(elem).find('.product-quantity').text(transaction.quantity)
      $(elem).find('.total-amount').text(transaction.amount)
      $('#transactions').append(elem)
    }

    modal('open', '#modal-farmer')
  }

  let codeTimer = null
  $('#farmer-search').on('keyup', function () {
    const value = $(this).val()
    clearTimeout(codeTimer)
    codeTimer = setTimeout(function () {
      userCode = value
      page = 1
      displayFarmers()
    }, 1250)
  })

  $('#farmer-search').on('keydown', function () {
    if (codeTimer) clearTimeout(codeTimer)
  })

  $('#sort-farmer-ascending').click(function () {
    userSort = 'asc'
    $('.dropdown-content.active').removeClass('active')
    displayFarmers()
  })

  $('#sort-farmer-descending').click(function () {
    userSort = 'desc'
    $('.dropdown-content.active').removeClass('active')
    displayFarmers()
  })

  $('#sort-sales-ascending').click(function () {
    salesSort = 'asc'
    $('.dropdown-content.active').removeClass('active')
    displayFarmers()
  })

  $('#sort-sales-descending').click(function () {
    salesSort = 'desc'
    $('.dropdown-content.active').removeClass('active')
    displayFarmers()
  })

  $(document).on('click', '[data-page]', function (event) {
    const target = event.currentTarget
    const value = $(target).attr('data-page')
    page = parseInt(value)
    displayFarmers()
  })

  displayFarmers()
})
