
$(document).ready(function () {
  const token = sessionStorage.getItem('token')
  if (token === null) {
    window.location.href = '/'
    return
  }

  const bgs = [
    '#b8255f',
    '#d62035',
    '#ff9933',
    '#fad000',
    '#afb82b',
    '#7ecc49',
    '#299438',
    '#6accbc',
    '#158fad',
    '#14aaf5',
    '#96c3eb',
    '#4073ff',
    '#884dff',
    '#af38eb',
    '#eb96eb',
    '#e05194',
    '#ff8d85',
    '#808080',
    '#b8b8b8',
    '#ccac93'
  ];

  $.ajax('/api/admin/stats.php?token=' + token, {
    method: 'get',
    dataType: 'json',
    success: function (data) {
      if (data.success) {
        const stats = data.stats
        $('.total-products-sold').text(stats.totalProductsSold)
        $('.total-orders').text(stats.totalOrders)
        $('.total-users').text(stats.totalUsers)

        const chart = new Chart('graph-weekly', {
          type: 'bar',
          data: {
            labels: stats.weekly.map(item => item.name),
            datasets: [{
              label: 'Weekly Earnings',
              data: stats.weekly.map(item => item.earned),
              backgroundColor: bgs.slice(0, stats.weekly.length)
            }]
          },
          options: {
            responsive: false,
            maintainAspectRatio: false,
            scales: {
              y: { beginAtZero: true }
            }
          }
        })
      }
    }
  })
})
