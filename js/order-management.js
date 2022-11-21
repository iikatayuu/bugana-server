
$(document).ready(function () {
  const token = sessionStorage.getItem('token')
  if (token === null) {
    window.location.href = '/'
    return
  }

  const tempTransaction = $('#temp-transaction').prop('content')
  const tempDetails = $('#temp-transaction-details').prop('content')
  const tempDetailsTotal = $('#temp-details-total').prop('content')
  const tempPageBtn = $('#temp-page-btn').prop('content')
  let category = 'all'
  let page = 1
  let limit = parseInt($('#limit-page').val())
  let transactionId = $('#transaction-search').val()
  let transactions = []

  async function displayTransactions () {
    $('#transactions').empty()

    const params = new URLSearchParams()
    params.set('page', page.toString())
    params.set('limit', limit.toString())
    params.set('category', category)
    params.set('token', token)
    if (transactionId) params.set('search', transactionId)

    const response = await $.ajax(`/api/admin/transaction/list.php?${params.toString()}`, {
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

    transactions = response.transactions
    for (let i = 0; i < transactions.length; i++) {
      const transaction = transactions[i]
      const user = transaction.user
      const elem = $(tempTransaction).clone(true, true)
      const totalAmount = parseFloat(transaction.total_amount) + (transaction.type === 'delivery' ? 50 : 0)

      $(elem).find('.transaction-id').text(transaction.code)
      $(elem).find('.transaction-date').text(transaction.date)
      $(elem).find('.customer-code').text(user.code)
      $(elem).find('.customer-address').text(user.addressstreet + ', ' + user.addresspurok + ', ' + user.addressbrgy)
      $(elem).find('.total-amount').text(totalAmount.toFixed(2))
      $(elem).find('.order-type').text(transaction.type === 'delivery' ? 'COD' : 'COP')
      $(elem).find('.order-status').attr({
        src: '/imgs/status-' + (transaction.status === 'success' ? 'check.png' : 'pending.png'),
        alt: transaction.status === 'success' ? 'Successful' : 'Pending'
      })

      $(elem).find('.transaction-action').attr('data-code', transaction.code).click(showOrder)

      $('#transactions').append(elem)
    }
  }

  async function showOrder (event) {
    event.preventDefault()

    const code = $(this).attr('data-code')
    const response = await $.ajax('/api/admin/transaction/get.php', {
      method: 'post',
      dataType: 'json',
      data: { code, token }
    })
    if (!response.success) return

    const transactions = response.transactions
    const tx = transactions[0]
    const user = tx.user
    let grandTotal = 0

    $('#order-customer-name').text(user.name)
    $('#transaction-id').text(code)
    $('#transaction-date').text(tx.date)
    $('#order-customer-code').text(user.code)
    $('#order-customer-address').text(user.addressstreet + ', ' + user.addresspurok + ', ' + user.addressbrgy)
    $('#order-type').text(tx.paymentoption === 'delivery' ? 'Cash On Delivery' : 'Cash On Pickup')
    $('#orders').empty()

    const displayed = []
    for (let i = 0; i < transactions.length; i++) {
      const elem = $(tempDetails).clone(true, true)
      const transaction = transactions[i]
      const product = transaction.product
      const amountEach = parseFloat(transaction.amount) / parseInt(transaction.quantity)
      grandTotal += parseFloat(transaction.amount)

      if (!displayed.includes(product.code)) {
        displayed.push(product.code)
        $(elem).find('.farmer-code').text(product.code)
      }

      $(elem).find('.product-name').text(product.name)
      $(elem).find('.order-quantity').text(transaction.quantity)
      $(elem).find('.order-price').text(amountEach.toFixed(2))
      $(elem).find('.order-amount').text(transaction.amount)
      $('#orders').append(elem)
    }

    const totalAmountElem = $(tempDetailsTotal).clone(true, true)
    const grandTotalElem = $(tempDetailsTotal).clone(true, true)

    $(totalAmountElem).find('.total-name').text('Total Name:')
    $(totalAmountElem).find('.total-value').text(grandTotal.toFixed(2))
    $('#orders').append(totalAmountElem)

    if (tx.paymentoption === 'delivery') {
      const deliveryElem = $(tempDetailsTotal).clone(true, true)
      $(deliveryElem).find('.total-name').text('Delivery Fee:')
      $(deliveryElem).find('.total-value').text('50.00')
      $('#orders').append(deliveryElem)

      $('.delivery').removeClass('d-none')
      grandTotal += 50
    } else $('.delivery').addClass('d-none')

    $(grandTotalElem).find('.total-name').text('Total Order Amount:')
    $(grandTotalElem).find('.total-value').text(grandTotal.toFixed(2))
    $('#orders').append(grandTotalElem)

    modal('open', '#modal-order')
  }

  $('#transactions-category-select').on('change', function () {
    const value = $(this).val()
    page = 1
    category = value
    displayTransactions()
  })

  let codeTimer = null
  let limitTimer = null

  $('#transaction-search').on('keyup', function () {
    const value = $(this).val()
    clearTimeout(codeTimer)
    codeTimer = setTimeout(function () {
      transactionId = value
      page = 1
      displayTransactions()
    }, 1250)
  })

  $('#transaction-search').on('keydown', function () {
    if (codeTimer) clearTimeout(codeTimer)
  })

  $('#limit-page').on('keyup', function () {
    const value = $(this).val()
    clearTimeout(limitTimer)
    limitTimer = setTimeout(function () {
      limit = parseInt(value)
      page = 1
      displayTransactions()
    }, 1250)
  })

  $('#limit-page').on('keydown', function () {
    if (limitTimer) clearTimeout(limitTimer)
  })

  $(document).on('click', '[data-page]', function (event) {
    const target = event.currentTarget
    const value = $(target).attr('data-page')
    page = parseInt(value)
    displayTransactions()
  })

  displayTransactions()
})
