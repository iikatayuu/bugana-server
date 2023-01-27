
$(document).ready(function () {
  const token = sessionStorage.getItem('token')
  if (token === null) {
    window.location.href = '/'
    return
  }

  const tempUser = $('#temp-user').prop('content')
  const tempPageBtn = $('#temp-page-btn').prop('content')
  let page = 1
  let limit = parseInt($('#limit-page').val())
  let users = []

  async function displayUsers() {
    $('#users').empty()

    const params = new URLSearchParams()
    params.set('page', page.toString())
    params.set('limit', limit.toString())
    params.set('token', token)
    const response = await $.getJSON(`/api/admin/users/violations.php?${params.toString()}`)
    if (!response.success) return

    $('#pages').empty()
    if (response.prev) $('[data-prev]').removeClass('d-none').attr('data-page', page - 1)
    else $('[data-prev]').addClass('d-none')

    if (response.next) $('[data-next]').removeClass('d-none').attr('data-page', page + 1)
    else $('[data-next]').addClass('d-none')

    let i = page > 2 ? page - 2 : 1
    if (i > 2) $('#pages').append('<span class="mr-2">...</span>')
    $('[data-page="1"]').toggleClass('active', page === i && page === 1)

    for (i; i < page + 3; i++) {
      if (i <= 1 || i > response.pages) continue

      const elem = $(tempPageBtn).clone(true, true)
      $(elem).find('[data-page]').attr('data-page', i).text(i)
      if (page === i) $(elem).find('[data-page]').addClass('active')
      $('#pages').append(elem)
    }

    if (i <= response.pages) {
      const elem = $(tempPageBtn).clone(true, true)
      $(elem).find('[data-page]').attr('data-page', response.pages).text(response.pages)
      if (i !== response.pages) $('#pages').append('<span class="mr-2">...</span>')
      $('#pages').append(elem)
    }

    users = response.users
    for (let i = 0; i < users.length; i++) {
      const elem = $(tempUser).clone(true, true)
      const user = users[i]
      const transaction = user.transaction

      let id = transaction ? transaction.transaction_id : null
      while (id !== null && id.length < 6) id = `0${id}`

      $(elem).find('.user-name').text(user.name)
      $(elem).find('.user-address').text(user.addressstreet + ', ' + user.addresspurok + ', ' + user.addressbrgy)
      $(elem).find('.user-contact').text(user.mobile)
      $(elem).find('.user-email').text(user.email)
      $(elem).find('.user-transaction').text(id || '')
      $(elem).find('.counts').text(user.counts)
      $(elem).find('.user-actions').attr({
        'data-id': user.id,
        disabled: user.active === '0' ? null : true
      }).click(unbanUserModal)

      $('#users').append(elem)
    }
  }

  async function unbanUserModal (event) {
    event.preventDefault()
    const userid = $(this).attr('data-id')
    $('#modal-confirm-unban').find('[data-user]').attr('data-user', userid)
    modal('open', '#modal-confirm-unban')
  }

  $('[data-user]').click(async function (event) {
    event.preventDefault()

    const userid = $(this).attr('data-user')
    $('#modal-confirm-unban').find('button').attr('disabled', true)
    const response = await $.ajax('/api/admin/violations/unban.php', {
      method: 'post',
      dataType: 'json',
      data: { token: token, id: userid }
    })

    $('#modal-confirm-unban').find('button').attr('disabled', null)
    if (response.success) {
      await displayUsers()
      modal('close')
      modal('open', '#modal-unban-successful')
    }
  })

  let limitTimer = null

  $('#limit-page').on('keyup', function () {
    const value = $(this).val()
    clearTimeout(limitTimer)
    limitTimer = setTimeout(function () {
      limit = parseInt(value)
      page = 1
      displayUsers()
    }, 1250)
  })

  $('#limit-page').on('keydown', function () {
    if (limitTimer) clearTimeout(limitTimer)
  })

  $(document).on('click', '[data-page]', function (event) {
    const target = event.currentTarget
    const value = $(target).attr('data-page')
    page = parseInt(value)
    displayUsers()
  })

  displayUsers()
})
