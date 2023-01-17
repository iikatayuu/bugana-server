
$(document).ready(function () {
  const token = sessionStorage.getItem('token')
  if (token === null) {
    window.location.href = '/'
    return
  }

  const bgPlugin = {
    id: 'customCanvasBackgroundColor',
    beforeDraw: (chart, args, options) => {
      const { ctx } = chart
      ctx.save()
      ctx.globalCompositeOperation = 'destination-over'
      ctx.fillStyle = options.color || '#99ffff'
      ctx.fillRect(0, 0, chart.width, chart.height)
      ctx.restore()
    }
  }

  const backgroundColors = [
    '#b6f790',
    '#ceb035',
    '#d82a94',
    '#8f4806',
    '#7320f7',
    '#76425b',
    '#10220c',
    '#75165e',
    '#4105d9',
    '#3982a7',
    '#2eaafe',
    '#15cd04',
    '#00f246',
    '#e95bae',
    '#b4cafa',
    '#457969',
    '#f0c64a',
    '#5b6734',
    '#1dcb9c',
    '#a1c208'
  ]

  const userTemp = $('#temp-user-new').prop('content')
  const legendTemp = $('#temp-legend').prop('content')
  let selected = 'weekly'
  let chart = null
  let weeklyStats = null
  let monthlyStats = null
  let yearlyStats = null

  function displayGraph () {
    if (chart !== null) chart.destroy()

    let label = ''
    let data = null

    switch (selected) {
      case 'weekly':
        label = 'Weekly Earnings'
        data = weeklyStats
        break

      case 'monthly':
        label = 'Monthly Earnings'
        data = monthlyStats
        break

      case 'yearly':
        label = 'Yearly Earnings'
        data = yearlyStats
        break
    }

    chart = new Chart('graph', {
      type: 'line',
      data: {
        labels: data.map(item => item.name),
        datasets: [{
          label: label,
          data: data.map(item => item.quantities),
          point: { backgroundColor: '#000' }
        }]
      },
      options: {
        response: false,
        maintainAspectRatio: false,
        scales: {
          x: { display: false },
          y: {
            beginAtZero: true,
            ticks: {
              callback: (value) => value + ' kg'
            }
          }
        },
        elements: {
          point: {
            radius: 6,
            backgroundColor: backgroundColors
          }
        },
        plugins: {
          legend: { display: false },
          customCanvasBackgroundColor: {
            color: '#e6f6e7'
          }
        }
      },
      plugins: [bgPlugin]
    })

    $('#legends').empty()
    for (let i = 0; i < data.length; i++) {
      const elem = $(legendTemp).clone(true, true)
      $(elem).find('.legend-color').css('background-color', backgroundColors[i])
      $(elem).find('.legend-name').text(data[i].name)
      $('#legends').append(elem)
    }
  }

  $('[data-graph]').click(function (event) {
    const graph = $(this).attr('data-graph')
    let data = null

    switch (graph) {
      case 'weekly':
        data = weeklyStats
        break

      case 'monthly':
        data = monthlyStats
        break

      case 'yearly':
        data = yearlyStats
        break
    }

    if (data !== null) {
      selected = graph
      $('[data-graph].active').removeClass('active')
      $(this).addClass('active')
      displayGraph()
    }
  })

  $.ajax('/api/admin/stats.php?token=' + token, {
    method: 'get',
    dataType: 'json',
    success: function (data) {
      if (data.success) {
        const stats = data.stats
        $('.total-products-sold').text(stats.totalProductsSold)
        $('.total-products-unsold').text(stats.totalProductsUnsold)
        $('.total-orders').text(stats.totalOrders)
        $('.total-users').text(stats.totalUsers)

        for (let i = 0; i < stats.users.length && i < 3; i++) {
          const user = stats.users[i]
          const elem = $(userTemp).clone(true, true)

          $(elem).find('.user-img').attr({
            src: '/api/profileimg.php?id=' + user.id,
            alt: user.name + ' Profile Picture'
          })

          $(elem).find('.user-name').text(user.name)
          $('#users-new').append(elem)
        }

        weeklyStats = stats.weekly
        monthlyStats = stats.monthly
        yearlyStats = stats.yearly

        $('[data-graph="weekly"]').text('Week ' + stats.weeklyDate)
        $('[data-graph="monthly"]').text(stats.monthlyDate + ' Monthly Sales')
        $('[data-graph="yearly"').text(stats.yearlyDate + ' Yearly Sales')

        displayGraph()
      }
    }
  })
})
