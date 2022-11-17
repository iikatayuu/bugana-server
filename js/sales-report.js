
$(document).ready(function () {
  const token = sessionStorage.getItem('token')
  if (token === null) {
    window.location.href = '/'
    return
  }

  const tempSale = $('#temp-sale').prop('content')
  const tempTotal = $('#temp-total').prop('content')
  async function displayTransactions () {
    $('#sales').empty()

    const params = new URLSearchParams()
    params.set('token', token)
    params.set('date', 'weekly')
    const response = await $.ajax('/api/transaction/sales.php?' + params.toString(), {
      method: 'get',
      dataType: 'json'
    })

    if (!response.success) return
    let total = 0
    for (let i = 0; i < response.transactions.length; i++) {
      const transaction = response.transactions[i]
      const product = transaction.product
      total += parseFloat(transaction.amount)

      const elem = $(tempSale).clone(true, true)
      $(elem).find('.product-name').text(product.name)
      $(elem).find('.product-price').text(product.price)
      $(elem).find('.quantity-sold').text(transaction.quantity)
      $(elem).find('.product-revenue').text(transaction.amount)
      $('#sales').append(elem)
    }

    const totalElem = $(tempTotal).clone(true, true)
    $(totalElem).find('.sales-report-total-amount').text(total.toFixed(2))
    $('#sales').append(totalElem)
  }

  displayTransactions()
})
