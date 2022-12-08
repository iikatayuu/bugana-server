
$(document).ready(function () {
  const token = sessionStorage.getItem('token')
  if (token === null) {
    window.location.href = '/'
    return
  }

  const searchParams = new URLSearchParams(window.location.search);
  const date = searchParams.get('date')

  async function displayReport () {
    const res = await $.ajax('/api/admin/transaction/summary.php', {
      method: 'post',
      data: { date: date, token: token },
      dataType: 'json'
    })

    if (res.success) {
      $('#total-sales').text(res.sales.total.toFixed(2))
      $('#total-delivery-sales').text(res.sales.delivery.toFixed(2))
      $('#unsold-products').text(res.sales.unsold.toFixed(2))
      $('#report-total').text(res.sales.grandtotal.toFixed(2))
    }
  }

  displayReport()
})
