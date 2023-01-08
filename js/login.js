
$(document).ready(function () {
  if (sessionStorage.getItem('token') !== null) {
    window.location.href = '/dashboard.php'
    return
  }

  $('#form-login').submit(async function (event) {
    event.preventDefault()

    $('#form-login-error').text('')
    $(this).find('[type="submit"]').attr('disabled', true).text('LOGGING IN')

    const form = $(this).get(0)
    const action = form.action
    const method = form.method
    const formData = new FormData(form)
    const response = await $.ajax(action, {
      method: method,
      data: formData,
      processData: false,
      contentType: false
    })

    $(this).find('[type="submit"]').attr('disabled', null).text('LOG IN')
    if (response.success) {
      const token = response.token
      const parts = token.split('.')
      const payload = atob(parts[1])
      const payloadObj = JSON.parse(payload)

      if (payloadObj.type === 'admin' || payloadObj.type === 'headadmin') {
        sessionStorage.setItem('token', token)
        sessionStorage.setItem('payload', payload)
        window.location.href = '/dashboard.php'
      }
    } else {
      $('#form-login-error').text(response.message)
    }
  })
})
