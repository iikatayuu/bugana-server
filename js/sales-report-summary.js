
$(document).ready(function () {
  const token = sessionStorage.getItem('token')
  if (token === null) {
    window.location.href = '/'
    return
  }

  const searchParams = new URLSearchParams(window.location.search);
  const date = searchParams.get('date')
  const prev = searchParams.get('prev')

  async function displayReport () {
    const res = await $.ajax('/api/admin/transaction/summary.php', {
      method: 'post',
      data: {
        date: date,
        token: token,
        prev: prev !== null
      },
      dataType: 'json'
    })

    if (res.success) {
      $('#total-sales').text(commaNumber(res.sales.total.toFixed(2)))
      $('#total-delivery-sales').text(commaNumber(res.sales.delivery.toFixed(2)))
      $('#unsold-products').text(commaNumber(res.sales.unsold.toFixed(2)))
      $('#report-total').text(commaNumber(res.sales.grandtotal.toFixed(2)))
    }
  }

  async function displayAnnualReport () {
    const tempAnnual = $('#temp-annual').prop('content')
    const res = await $.ajax('/api/admin/transaction/annual.php', {
      method: 'post',
      data: { token: token },
      dataType: 'json'
    })

    if (res.success) {
      let totalSales = 0

      for (let i = 0; i < res.months.length; i++) {
        const monthName = MONTHS[i]
        const monthSales = parseFloat(res.months[i])
        const elem = $(tempAnnual).clone(true, true)
        totalSales += monthSales

        $(elem).find('.annual-month-name').text(monthName)
        $(elem).find('.annual-month-sales').text('Php ' + commaNumber(monthSales.toFixed(2)))
        $(elem).find('.annual-month-details').attr('href', 'sales-report.php?date=monthly&detailed&month=' + i)
        $('#annual-sales').append(elem)
      }

      const totalElem = $(tempAnnual).clone(true, true)
      $(totalElem).find('.annual-month-name').addClass('text-bold').text('TOTAL')
      $(totalElem).find('.annual-month-sales').text('Php ' + commaNumber(totalSales.toFixed(2)))
      $(totalElem).find('.annual-month-details').attr('href', 'sales-report.php?date=annual&detailed')
      $('#annual-sales').append(totalElem)
    }
  }

  $('[data-prev]').click(async function () {
    const params = new URLSearchParams(window.location.search)
    params.set('prev', '1')
    
    window.location.href = window.location.pathname + '?' + params.toString()
  })

  $('[data-present]').click(async function () {
    const params = new URLSearchParams(window.location.search)
    params.delete('prev')
    
    window.location.href = window.location.pathname + '?' + params.toString()
  })

  if (date !== 'annual') {
    displayReport()
  } else {
    displayAnnualReport()
  }
})
