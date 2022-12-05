
function modal (action, target = '') {
  if (action === 'open') {
    $('.modal-container').removeClass('d-none')
    $(target).toggleClass('modal-open')
  } else if (action === 'close') {
    $('.modal-container').addClass('d-none')
    $('.modal-open').toggleClass('modal-open')
  }
}

$(document).ready(function () {
  $('.logout').click(function () {
    sessionStorage.removeItem('token')
    sessionStorage.removeItem('payload')
    window.location.href = '/'
  })

  $('[data-modal]').click(function (event) {
    event.preventDefault()

    const target = $(this).attr('data-modal')
    const isOpen = $(target).hasClass('modal-open')

    if (isOpen) {
      $('.modal-container').addClass('d-none')
      $(target).removeClass('modal-open')
    } else {
      modal('close')
      $('.modal-container').removeClass('d-none')
      $(target).addClass('modal-open')
    }
  })

  $('.modal-container').click(function (event) {
    event.preventDefault()

    $('.modal-container').toggleClass('d-none')
    $('.modal-open').removeClass('modal-open')
  })

  $('[data-dropdown="toggle"]').click(function (event) {
    event.preventDefault()

    $(this).parents('.dropdown').find('.dropdown-content').toggleClass('active')
  })

  $(document).click(function (event) {
    const targets = event.originalEvent.composedPath()
    const isDropdown = $(targets).hasClass('dropdown')
    if (!isDropdown) $('.dropdown-content.active').removeClass('active')
  })
})
