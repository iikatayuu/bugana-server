
$(document).ready(function () {
  const token = sessionStorage.getItem('token')
  if (token === null) {
    window.location.href = '/'
    return
  }

  const searchParams = new URLSearchParams(window.location.search);
  const date = searchParams.get('date')
  const unsold = searchParams.get('unsold')
  const monthIndex = searchParams.get('month')

  const tempSale = $('#temp-sale').prop('content')
  const tempTotal = $('#temp-total').prop('content')
  const tempBreakdown = $('#temp-breakdown').prop('content')
  let transactions = []

  async function displayTransactions () {
    $('#sales').empty()

    const params = new URLSearchParams()
    params.set('token', token)
    params.set('date', date)
    if (unsold !== null) params.set('unsold', '1')
    if (monthIndex !== null) params.set('month', monthIndex)

    const response = await $.ajax('/api/admin/transaction/sales.php?' + params.toString(), {
      method: 'get',
      dataType: 'json'
    })

    if (!response.success) return
    let total = 0
    transactions = response.transactions
    for (let i = 0; i < response.transactions.length; i++) {
      const transaction = response.transactions[i]
      const product = transaction.product
      total += parseFloat(transaction.amount)

      const elem = $(tempSale).clone(true, true)
      $(elem).find('.product-name').text(product.name)
      $(elem).find('.quantity-sold').text(transaction.quantity)
      $(elem).find('.product-revenue').text(transaction.amount)
      $(elem).find('.product-details').attr({
        'data-product': product.name,
        'data-index': i
      }).click(showProduct)

      $('#sales').append(elem)
    }

    const totalElem = $(tempTotal).clone(true, true)
    const totalStr = total.toFixed(2)
    $(totalElem).find('.sales-report-total-amount').text(totalStr)
    $('#report-total-sales').text(totalStr)
    $('#sales').append(totalElem)
  }

  async function showProduct (event) {
    event.preventDefault()

    const product = $(this).attr('data-product')
    const index = $(this).attr('data-index')
    const currentTx = transactions[index]
    const response = await $.ajax('/api/admin/product/breakdown.php', {
      method: 'post',
      dataType: 'json',
      data: { product, token }
    })
    if (!response.success) return

    const breakdown = response.breakdown
    $('#modal-product-breakdown-farmers').empty()
    for (let i = 0; i < breakdown.length; i++) {
      const elem = $(tempBreakdown).clone(true, true)
      const farmerProduct = breakdown[i]
      const totalAmount = currentTx.quantity * parseFloat(farmerProduct.price)

      $(elem).find('.bd-farmer-name').text(farmerProduct.name)
      $(elem).find('.bd-product-name').text(product)
      $(elem).find('.bd-product-price').text(farmerProduct.price)
      $(elem).find('.bd-product-quantity').text(currentTx.quantity)
      $(elem).find('.bd-total-amount').text(totalAmount.toFixed(2))

      $('#modal-product-breakdown-farmers').append(elem)
    }

    modal('open', '#modal-product-breakdown')
  }

  const currentDate = new Date()
  $('#sales-report-table-date').text(`${MONTHS[currentDate.getMonth()]} ${currentDate.getDate()}, ${currentDate.getFullYear()}`)

  if (date === 'weekly') {
    const firstDate = new Date(currentDate)
    const day = firstDate.getDay()
    const diff = firstDate.getDate() - day + (day === 0 ? -6 : 0)
    firstDate.setDate(diff)

    const month = MONTHS[firstDate.getMonth()]
    const weekNum = firstDate.getWeek()
    $('#report-date').text(`${month} ${currentDate.getDate()}, ${currentDate.getFullYear()}`)
    $('#report-month').text(`Week ${weekNum}`)
  }

  if (date === 'monthly') {
    const month = MONTHS[currentDate.getMonth()]
    $('#report-date').text(`${month} ${currentDate.getDate()}, ${currentDate.getFullYear()}`)
    $('#report-month').text(`Month of ${month}`)
  }

  if (date === 'annual') {
    const month = MONTHS[currentDate.getMonth()]
    $('#report-date').text(`${month} ${currentDate.getDate()}, ${currentDate.getFullYear()}`)
    $('#report-month').text(`Year ${currentDate.getFullYear()}`)
  }

  displayTransactions()
})
