
Date.prototype.getWeek = function () {
  const date = new Date(this.getTime())
  date.setHours(0, 0, 0, 0)
  date.setDate(date.getDate() + 3 - (date.getDay() + 6) % 7)
  const week1 = new Date(date.getFullYear(), 0, 4)
  return 1 + Math.round(((date.getTime() - week1.getTime()) / 86400000 - 3 + (week1.getDay() + 6) % 7) / 7)
}

const MONTHS = [
  'January',
  'February',
  'March',
  'April',
  'May',
  'June',
  'July',
  'August',
  'September',
  'October',
  'November',
  'December'
]

const MONTHS_SHORT = [
  'Jan',
  'Feb',
  'Mar',
  'Apr',
  'May',
  'Jun',
  'Jul',
  'Aug',
  'Sep',
  'Oct',
  'Nov',
  'Dec'
]

function dateFormat (d) {
  if (!d) return ''

  let dateStr = ''
  const date = new Date(d)
  const hrs = date.getHours()
  const meridian = hrs > 12 ? 'PM' : 'AM'
  let hours = (hrs > 12 ? hrs - 12 : hrs).toString()
  while (hours.length < 2) hours = `0${hours}`
  let minutes = date.getMinutes().toString()
  while (minutes.length < 2) minutes = `0${minutes}`

  dateStr += MONTHS_SHORT[date.getMonth()] + ' '
  dateStr += date.getDate() + ', '
  dateStr += date.getFullYear() + ' '
  dateStr += hours + ':'
  dateStr += minutes + ' '
  dateStr += meridian

  return dateStr
}

function dateFormat2 (d) {
  if (!d) return ''

  let dateStr = ''
  const date = new Date(d)
  const hrs = date.getHours()
  const meridian = hrs > 12 ? 'PM' : 'AM'
  let hours = (hrs > 12 ? hrs - 12 : hrs).toString()
  while (hours.length < 2) hours = `0${hours}`
  let minutes = date.getMinutes().toString()
  while (minutes.length < 2) minutes = `0${minutes}`

  dateStr += hours + ':'
  dateStr += minutes + ' '
  dateStr += meridian + ' '
  dateStr += MONTHS_SHORT[date.getMonth()] + '. '
  dateStr += date.getDate() + ', '
  dateStr += date.getFullYear()

  return dateStr
}

function commaNumber (number) {
  const str = number.toString().split('.')
  if (str[0] > 3) {
    str[0] = str[0].replace(/(\d)(?=(\d{3})+$)/g, '$1,')
  }

  return str.join('.')
}

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

  $(document).on('click', '[data-dropdown="toggle"]', function (event) {
    event.preventDefault()

    $(event.target).parents('.dropdown').find('.dropdown-content').toggleClass('active')
  })

  $(document).click(function (event) {
    const targets = event.originalEvent.composedPath()
    const isDropdown = $(targets).hasClass('dropdown')
    if (!isDropdown) $('.dropdown-content.active').removeClass('active')
  })

  $('[data-print]').click(function (event) {
    event.preventDefault()

    const selector = $(this).attr('data-print')
    const contents = $(selector).clone(true, true)

    $(contents).find('[data-print-style]').each(function () {
      const styles = $(this).attr('data-print-style')
      $(this).attr('style', styles)
    })

    const html = $(contents).html()

    const child = window.open('', '', 'width=900, height=600')
    child.document.write(html)
    child.print()
    // child.close()
  })
})
