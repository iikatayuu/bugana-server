
$(document).ready(function () {
  const token = sessionStorage.getItem('token')
  if (token === null) {
    window.location.href = '/'
    return
  }

  const searchParams = new URLSearchParams(window.location.search)
  const id = searchParams.get('id')
  if (id === null) {
    window.location.href = '/product-management.php'
    return
  }

  let files = []
  $('#file-product-photos').on('change', function (event) {
    files = $(this).prop('files')

    $('#product-photos').empty()
    if (files.length > 4) {
      $('#form-product-edit-error').text('Maximum files reached')
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

  $('#form-product-edit').submit(async function (event) {
    event.preventDefault()

    $('#form-product-edit-error').empty()
    $(this).find('[type="submit"]').attr('disabled', true).text('SAVING CHANGES')

    const form = $(this).get(0)
    const action = $(form).attr('action')
    const method = $(form).attr('method')
    const token = sessionStorage.getItem('token')
    const formData = new FormData(form)
    formData.append('token', token)
    formData.append('id', id)

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

    $(this).find('[type="submit"]').attr('disabled', null).text('SAVE CHANGES')
    if (response.success) {
      window.location.href = '/product-management.php'
    } else {
      $('#form-product-edit-error').text(response.message)
    }
  })

  $.ajax('/api/product/get.php?id=' + id, {
    method: 'get',
    dataType: 'json',
    success: function (data) {
      if (data.success) {
        const product = data.product
        $('#product-name').val(product.name)
        $('#product-category').val(product.category)
        $('#product-description').val(product.description)
        $('#product-price').val(product.price)

        for (let i = 0; i < product.photos.length; i++) {
          const photo = product.photos[i]
          const elem = $('<img src="" alt="Product Photo ' + i + '" width="150" class="mx-2" />')
          $(elem).attr('src', photo)
          $('#product-photos').append(elem)
        }

        $('#form-product-edit').find('[type="submit"]').attr('disabled', null)
      }
    }
  })
})
