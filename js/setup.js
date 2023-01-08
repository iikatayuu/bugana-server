
$(document).ready(function () {
  $('#form-setup').submit(async function (event) {
    event.preventDefault()

    $('#form-setup-error').text('')
    $(this).find('[type="submit"]').attr('disabled', true).text('SUBMITTING')

    const headAdminUser = $('#headadmin-user').val()
    const headAdminPass = $('#headadmin-pass').val()
    const adminUser = $('#admin-user').val()
    const adminPass = $('#admin-pass').val()

    if (headAdminUser !== '' && headAdminPass === '') {
      $('#form-setup-error').text('Invalid passwrod for head admin')
      return
    }

    if (adminUser !== '' && adminPass === '') {
      $('#form-setup-error').text('Invalid passwrod for admin')
      return
    }

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

    $(this).find('[type="submit"]').attr('disabled', null).text('SUBMIT')
    if (response.success) {
      window.location.href = '/setup.php'
    } else {
      $('#form-setup-error').text(response.message)
    }
  })
})
