
$(document).ready(function () {
  const token = sessionStorage.getItem('token')
  const payloadItem = sessionStorage.getItem('payload')
  if (token === null) {
    window.location.href = '/'
    return
  }

  const payload = JSON.parse(payloadItem)
  const tempUser = $('#temp-user').prop('content')
  const tempAction = $('#temp-user-actions').prop('content')
  const search = new URLSearchParams(window.location.search)
  const view = payload.type === 'admin' ? 'farmers' : search.get('view') || 'all'
  let page = 1
  let users = []

  async function displayUsers () {
    $('#users').empty()

    const params = new URLSearchParams()
    params.set('token', token)
    params.set('view', view)
    params.set('page', page)
    const response = await $.ajax('/api/admin/users.php?' + params.toString(), {
      method: 'get',
      dataType: 'json'
    })

    if (!response.success) return

    $('[data-next]').attr('disabled', response.next ? null : true)
    $('[data-prev]').attr('disabled', response.prev ? null : true)

    users = response.users
    for (let i = 0; i < users.length; i++) {
      const user = users[i]
      const elem = $(tempUser).clone(true, true)
      $(elem).find('.user-code').text(user.code)
      $(elem).find('.user-name').text(user.name)
      $(elem).find('.user-email').text(user.email)
      $(elem).find('.user-created').text(user.created)
      $(elem).find('.user-lastlogin').text(user.lastlogin)
      if ((payload.type === 'headadmin' && user.type === 'customer') || (payload.type === 'admin' && user.type === 'farmer')) {
        const actionsElem = $(tempAction).clone(true, true)
        $(actionsElem).find('.user-action-edit').attr('data-index', i).click(editUser)
        $(actionsElem).find('.user-action-archive').attr('data-index', i).click(archiveUser)
        $(elem).find('.user-actions').empty().append(actionsElem)
      }

      $('#users').append(elem)
    }
  }

  async function editUser (event) {
    event.preventDefault()

    const index = $(this).attr('data-index')
    const user = users[index]
    $('#edit-type-text').text(user.type === 'customer' ? 'Customer' : 'Farmer')
    $('#edit-id').val(user.id)
    $('#edit-name').val(user.name)
    $('#edit-gender').val(user.gender)
    $('#edit-birthday').val(user.birthday)
    $('#edit-email').val(user.email)
    $('#edit-mobile').val(user.mobile)
    $('#edit-username').val(user.username)
    $('#edit-address-street').val(user.addressstreet)
    $('#edit-address-brgy').val(user.addressbrgy)
    $('#edit-address-city').val(user.addresscity)
    modal('open', '#modal-edit')
  }

  async function archiveUser (event) {
    event.preventDefault()
  }

  $('[data-prev]').click(function (event) {
    event.preventDefault()
    page--
    displayUsers()
  })

  $('[data-next]').click(function (event) {
    event.preventDefault()
    page++
    displayUsers()
  })

  $('#form-register').submit(async function (event) {
    event.preventDefault()

    const form = $(this).get(0)
    const action = $(form).attr('action')
    const method = $(form).attr('method')
    const formData = new FormData(form)
    if (payload.type === 'admin') formData.append('type', 'farmer')

    $('#form-register-error').empty()
    $(form).find('[type="submit"]').attr('disabled', true).text('Registering...')

    const response = await $.ajax(action, {
      method: method,
      dataType: 'json',
      data: formData,
      processData: false,
      contentType: false
    })

    $(form).find('[type="submit"]').attr('disabled', null).text('Register account')
    if (response.success) {
      $(form).trigger('reset')
      await displayUsers()
      modal('close')
    } else {
      $('#form-register-error').text(response.message)
    }
  })

  $('#form-edit').submit(async function (event) {
    event.preventDefault()

    const form = $(this).get(0)
    const action = $(form).attr('action')
    const method = $(form).attr('method')
    const token = sessionStorage.getItem('token')
    const formData = new FormData(form)
    formData.append('token', token)

    $('#form-edit-error').empty()
    $(form).find('[type="submit"]').attr('disabled', true).text('Saving...')

    const response = await $.ajax(action, {
      method: method,
      dataType: 'json',
      data: formData,
      processData: false,
      contentType: false
    })

    $(form).find('[type="submit"]').attr('disabled', null).text('Save Changes')
    if (response.success) {
      $(form).trigger('reset')
      await displayUsers()
      modal('close')
    } else {
      $('#form-edit-error').text(response.message)
    }
  })

  displayUsers()
})
