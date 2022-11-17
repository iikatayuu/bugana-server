
$(document).ready(function () {
  const token = sessionStorage.getItem('token')
  if (token === null) {
    window.location.href = '/'
    return
  }

  let files = []
  $('#file-product-photos').on('change', function (event) {
    files = $(this).prop('files')

    $('#product-photos').empty()
    if (files.length > 4) {
      $('#form-product-add-error').text('Maximum files reached')
      return
    }

    for (let i = 0; i < files.length; i++) {
      const file = files[i]
      const elem = $('<img src="" alt="Product Photo ' + i + '" width="150" class="mx-2" />')
      const src = URL.createObjectURL(file)
      $(elem).attr('src', src)
      $('#product-photos').append(elem)
    }
  })

  $('#form-product-add').submit(async function (event) {
    event.preventDefault()

    $('#form-product-add-error').empty()
    $(this).find('[type="submit"]').attr('disabled', true).text('ADDING PRODUCT')

    const form = $(this).get(0)
    const action = $(form).attr('action')
    const method = $(form).attr('method')
    const token = sessionStorage.getItem('token')
    const formData = new FormData(form)
    formData.append('token', token)

    if (files.length === 0) {
      $('#form-product-add-error').text('Add a photo')
      return
    }

    for (let i = 0; i < files.length; i++) {
      const file = files[i]
      formData.append('photo-' + i, file, file.name)
    }

    const response = await $.ajax(action, {
      method: method,
      dataType: 'json',
      data: formData,
      processData: false,
      contentType: false
    })

    $(this).find('[type="submit"]').attr('disabled', null).text('ADD PRODUCT')
    if (response.success) {
      window.location.href = '/product-management.php'
    } else {
      $('#form-product-add-error').text(response.message)
    }
  })
})
