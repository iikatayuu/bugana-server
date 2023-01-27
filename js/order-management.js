
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
  const tempShip = $('#temp-ship').prop('content')
  let category = 'all'
  let page = 1
  let limit = parseInt($('#limit-page').val())
  let transactionId = $('#transaction-search').val()
  let transactions = []
  let currentTransaction = null

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
      const dateStr = dateFormat(transaction.date)
      let totalAmount = parseFloat(transaction.total_amount)
      if (transaction.paymentoption === 'delivery') {
        const shippingRes = await $.getJSON('/api/shipping.php?brgy=' + transaction.user.addressbrgy)
        const shipping = shippingRes.fee
        totalAmount += shipping
      }

      let id = transaction.id
      while (id.length < 6) id = `0${id}`

      $(elem).find('.transaction-id').text(id)
      $(elem).find('.transaction-date').text(dateStr)
      $(elem).find('.customer-name').text(user.name)
      $(elem).find('.total-amount').text(totalAmount.toFixed(2))
      $(elem).find('.order-type').text(transaction.paymentoption === 'delivery' ? 'COD' : 'COP')
      $(elem).find('.order-status-text').text(transaction.status === 'success' ? (transaction.paymentoption === 'delivery' ? 'Delivered' : 'Picked Up') : '')
      $(elem).find('.order-status').attr({
        src: '/imgs/status-' + (transaction.status === 'success' || transaction.status === 'approved' ? 'check.png' : 'pending.png'),
        alt: transaction.status === 'success' || transaction.status === 'approved' ? 'Successful' : 'Pending'
      })

      if (transaction.status !== 'success' && transaction.status !== 'rejected') {
        if (transaction.status !== 'approved') {
          $(elem).find('.order-status-violation').attr('disabled', null).attr('data-tx', i).click(async function (event) {
            event.preventDefault()
            currentTransaction = transaction
            addViolation()
          })
        }

        $(elem).find('.order-status').click(async function (event) {
          event.preventDefault()

          currentTransaction = transaction
          const modalSelector = transaction.status === 'approved' ? '#modal-unconfirm-order' : '#modal-confirm-order'
          $(modalSelector).find('[data-order]').click(confirmOrder.bind(this))
          modal('open', modalSelector)
        })
      }

      $(elem).find('.transaction-action').attr('data-code', transaction.code).click(showOrder)

      $('#transactions').append(elem)
    }
  }

  async function addViolation () {
    const response = await $.ajax('/api/admin/violations/add.php', {
      method: 'post',
      dataType: 'json',
      data: {
        transactionid: currentTransaction.code,
        token: token
      }
    })

    if (response.success) window.location.href = '/customer-violation-reports.php'
  }

  async function confirmOrder (event) {
    event.preventDefault()

    const response = await $.ajax('/api/admin/transaction/approve.php', {
      method: 'post',
      dataType: 'json',
      data: {
        code: currentTransaction.code,
        token: token
      }
    })

    if (response.success) displayTransactions()
    modal('close')
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
    const dateStr = dateFormat(tx.date)
    let grandTotal = 0

    $('#order-customer-name').text(user.name)
    $('#transaction-id').text(code)
    $('#transaction-date').text(dateStr)
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
      const feeRes = await $.getJSON('/api/shipping.php?brgy=' + user.addressbrgy)
      const fee = feeRes.fee

      $(deliveryElem).find('.total-name').text('Delivery Fee:')
      $(deliveryElem).find('.total-value').text(fee.toFixed(2))
      $('#orders').append(deliveryElem)

      $('.delivery').removeClass('d-none')
      grandTotal += fee
    } else $('.delivery').addClass('d-none')

    $(grandTotalElem).find('.total-name').text('Total Order Amount:')
    $(grandTotalElem).find('.total-value').text(grandTotal.toFixed(2))
    $('#orders').append(grandTotalElem)

    modal('open', '#modal-order')
  }

  async function displayFees () {
    $('#delivery-fees').empty()

    const fees = await $.getJSON('/api/allshipping.php')
    for (let i = 0; i < fees.length; i++) {
      const elem = $(tempShip).clone(true, true)
      const fee = fees[i]
      const brgy = fee.name
      const shipping = fee.fee

      $(elem).find('.brgy-name').text(brgy)
      $(elem).find('.brgy-fees').text(shipping)
      $(elem).find('.brgy-edit').attr('data-brgy', brgy).click(editBrgy)
      $('#delivery-fees').append(elem)
    }
  }

  async function editBrgy (event) {
    event.preventDefault()

    const brgy = $(this).attr('data-brgy')
    $('#edit-brgy-name-input').val(brgy)
    $('.edit-brgy-name').text(brgy)
    modal('close')
    modal('open', '#modal-shipping-edit')
  }

  $('#form-update').submit(async function (event) {
    event.preventDefault()

    const form = $(this).get(0)
    const action = $(form).attr('action')
    const method = $(form).attr('method')
    const token = sessionStorage.getItem('token')
    const formData = new FormData(form)
    formData.append('token', token)

    $(form).find('[type="submit"]').attr('disabled', true).text('Changing...')
    const response = await $.ajax(action, {
      method: method,
      dataType: 'json',
      data: formData,
      processData: false,
      contentType: false
    })

    $(form).find('[type="submit"]').attr('disabled', null).text('Confirm')
    if (response.success) {
      modal('close')
      modal('open', '#modal-update-successful')
      await displayFees()
    }
  })

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
  displayFees()
})
