
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

      $(elem).find('.user-code').text(user.code)
      $(elem).find('.user-name').text(user.name)
      $(elem).find('.user-address').text(user.addressstreet + ', ' + user.addresspurok + ', ' + user.addressbrgy)
      $(elem).find('.user-contact').text(user.mobile)
      $(elem).find('.user-email').text(user.email)
      $(elem).find('.user-transaction').text(transaction ? transaction.transaction_code : '')
      $(elem).find('.counts').text(user.counts)
      $(elem).find('.user-actions').attr('data-id', user.id).click(banUser)
      $('#users').append(elem)
    }
  }

  async function banUser (event) {
    const userid = $(this).attr('data-id')
    if (confirm('Are you sure?')) {
      const formData = new FormData()
      formData.set('token', token)
      formData.set('id', userid)
      await $.ajax('/api/admin/violations/ban.php', {
        method: 'post',
        data: formData,
        contentType: false,
        processData: false
      })
      await displayUsers()
    }
  }

  $('#form-add').submit(async function (event) {
    event.preventDefault()

    $('#form-add-error').empty()

    const form = $(this).get(0)
    const action = $(form).attr('action')
    const method = $(form).attr('method')
    const formData = new FormData(form)
    formData.set('token', token)

    const res = await $.ajax(action, {
      method: method,
      data: formData,
      processData: false,
      contentType: false
    })

    if (res.success) {
      await displayUsers()
      modal('#modal-add', 'close')
    } else {
      $('#form-add-error').text(res.message)
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
