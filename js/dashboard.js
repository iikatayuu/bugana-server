
$(document).ready(function () {
  const token = sessionStorage.getItem('token')
  const payloadItem = sessionStorage.getItem('payload')
  if (token === null) {
    window.location.href = '/'
    return
  }

  const payload = JSON.parse(payloadItem)
  if (payload.type === 'headadmin') {
    $('.headadmin-btns').removeClass('d-none')
    $('.admin-pp').attr('src', '/imgs/headadmin.png')
    $('.admin-name').text('Head Admin')
  } else {
    $('.admin-btns').removeClass('d-none')
    $('.admin-pp').attr('src', '/imgs/admin.png')
    $('.admin-name').text('Admin')
  }
})
