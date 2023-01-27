<?php

require_once 'includes/html.php';

$styles = ['/css/dashboard.css', '/css/order-management.css'];
$scripts = ['/js/dashboard.js', '/js/order-management.js'];
out_header('BUGANA Order Management', $styles, $scripts);

$sort = !empty($_GET['sort']) ? $_GET['sort'] : 'all';

?>
<main>
  <div class="sidebar">
    <div class="sidebar-logo mb-5">
      <img src="/imgs/logo-inverse.png" alt="BUGANA Logo" width="64" />
      <span class="sidebar-title">BUGANA</span>
    </div>

    <a href="/dashboard.php" class="sidebar-link">
      <svg width="16" height="16" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M29.0333 12.2663L16.1834 0.443791C16.0285 0.303169 15.8441 0.191554 15.6411 0.115385C15.438 0.0392156 15.2201 0 15.0001 0C14.7801 0 14.5622 0.0392156 14.3591 0.115385C14.156 0.191554 13.9717 0.303169 13.8168 0.443791L0.966852 12.2813C0.655916 12.5634 0.410347 12.8984 0.244404 13.2667C0.0784609 13.635 -0.0045496 14.0293 0.000192273 14.4267V26.9994C-0.00109909 27.7674 0.324812 28.5066 0.91081 29.0649C1.49681 29.6231 2.29828 29.9578 3.15017 30H8.33347V16.4972C8.33347 16.0993 8.50906 15.7176 8.82162 15.4363C9.13418 15.1549 9.5581 14.9968 10.0001 14.9968H20.0001C20.4421 14.9968 20.866 15.1549 21.1786 15.4363C21.4911 15.7176 21.6667 16.0993 21.6667 16.4972V30H26.85C27.7019 29.9578 28.5034 29.6231 29.0894 29.0649C29.6754 28.5066 30.0013 27.7674 30 26.9994V14.4267C30.0013 13.621 29.6547 12.8463 29.0333 12.2663Z" fill="currentColor" />
      </svg>
      <span>Dashboard</span>
    </a>

    <a href="/inventory.php" class="sidebar-link">
      <svg width="16" height="16" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M23.625 20.6245H6.39375C6.1875 20.6245 6.01875 20.8355 6.01875 21.0933L6.01406 23.906C6.01406 24.1638 6.18281 24.3747 6.38906 24.3747H23.625C23.8312 24.3747 24 24.1638 24 23.906V21.0933C24 20.8355 23.8312 20.6245 23.625 20.6245ZM23.625 26.2498H6.37969C6.17344 26.2498 6.00469 26.4608 6.00469 26.7186L6 29.5312C6 29.7891 6.16875 30 6.375 30H23.625C23.8312 30 24 29.7891 24 29.5312V26.7186C24 26.4608 23.8312 26.2498 23.625 26.2498ZM23.625 14.9993H6.40313C6.19688 14.9993 6.02813 15.2102 6.02813 15.468L6.02344 18.2807C6.02344 18.5385 6.19219 18.7495 6.39844 18.7495H23.625C23.8312 18.7495 24 18.5385 24 18.2807V15.468C24 15.2102 23.8312 14.9993 23.625 14.9993ZM28.6172 6.85434L15.8625 0.215346C15.5883 0.0731786 15.2945 0 14.9977 0C14.7009 0 14.407 0.0731786 14.1328 0.215346L1.38281 6.85434C0.548438 7.29382 0 8.3134 0 9.45017V29.5312C0 29.7891 0.16875 30 0.375 30H4.125C4.33125 30 4.5 29.7891 4.5 29.5312V14.9993C4.5 13.968 5.18438 13.1242 6.02813 13.1242H23.9719C24.8156 13.1242 25.5 13.968 25.5 14.9993V29.5312C25.5 29.7891 25.6688 30 25.875 30H29.625C29.8312 30 30 29.7891 30 29.5312V9.45017C30 8.3134 29.4516 7.29382 28.6172 6.85434Z" fill="currentColor" />
      </svg>
      <span>Inventory</span>
    </a>

    <a href="/sales-report.php" class="sidebar-link">
      <svg width="16" height="16" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M30 6.5625V27.1875C30 28.7408 28.3211 30 26.25 30H3.75C1.67891 30 0 28.7408 0 27.1875V6.5625C0 5.00918 1.67891 3.75 3.75 3.75H10C10 1.68223 12.243 0 15 0C17.757 0 20 1.68223 20 3.75H26.25C28.3211 3.75 30 5.00918 30 6.5625ZM15 2.34375C13.9645 2.34375 13.125 2.97334 13.125 3.75C13.125 4.52666 13.9645 5.15625 15 5.15625C16.0355 5.15625 16.875 4.52666 16.875 3.75C16.875 2.97334 16.0355 2.34375 15 2.34375ZM22.5 9.02344V7.85156C22.5 7.75832 22.4506 7.6689 22.3627 7.60297C22.2748 7.53704 22.1556 7.5 22.0312 7.5H7.96875C7.84443 7.5 7.7252 7.53704 7.63729 7.60297C7.54939 7.6689 7.5 7.75832 7.5 7.85156V9.02344C7.5 9.11668 7.54939 9.2061 7.63729 9.27203C7.7252 9.33796 7.84443 9.375 7.96875 9.375H22.0312C22.1556 9.375 22.2748 9.33796 22.3627 9.27203C22.4506 9.2061 22.5 9.11668 22.5 9.02344Z" fill="currentColor" />
      </svg>
      <span>Sales Report</span>
    </a>

    <a href="/user-management.php" class="sidebar-link">
      <svg width="16" height="16" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M28.6202 21.873C28.7421 21.0469 28.7421 20.2031 28.6202 19.377L29.8297 18.5039C29.9703 18.4043 30.0313 18.1992 29.9844 18.0059C29.6703 16.7402 29.1312 15.5918 28.428 14.6426C28.3202 14.4961 28.1467 14.4609 28.0061 14.5605L26.7966 15.4336C26.2856 14.8887 25.6996 14.4668 25.0667 14.1855V12.4395C25.0667 12.2402 24.9542 12.0645 24.7995 12.0234C23.7541 11.7305 22.6899 11.7422 21.696 12.0234C21.5413 12.0645 21.4288 12.2402 21.4288 12.4395V14.1855C20.7959 14.4668 20.2099 14.8887 19.699 15.4336L18.4894 14.5605C18.3535 14.4609 18.1754 14.4961 18.0675 14.6426C17.3643 15.5918 16.8252 16.7402 16.5111 18.0059C16.4642 18.1992 16.5299 18.4043 16.6658 18.5039L17.8753 19.377C17.7534 20.2031 17.7534 21.0469 17.8753 21.873L16.6658 22.7461C16.5252 22.8457 16.4642 23.0508 16.5111 23.2441C16.8252 24.5098 17.3643 25.6523 18.0675 26.6074C18.1754 26.7539 18.3488 26.7891 18.4894 26.6895L19.699 25.8164C20.2099 26.3613 20.7959 26.7832 21.4288 27.0645V28.8105C21.4288 29.0098 21.5413 29.1855 21.696 29.2266C22.7415 29.5195 23.8056 29.5078 24.7995 29.2266C24.9542 29.1855 25.0667 29.0098 25.0667 28.8105V27.0645C25.6996 26.7832 26.2856 26.3613 26.7966 25.8164L28.0061 26.6895C28.142 26.7891 28.3202 26.7539 28.428 26.6074C29.1312 25.6582 29.6703 24.5098 29.9844 23.2441C30.0313 23.0508 29.9657 22.8457 29.8297 22.7461L28.6202 21.873ZM23.2525 23.4668C21.9961 23.4668 20.9788 22.1895 20.9788 20.625C20.9788 19.0605 22.0008 17.7832 23.2525 17.7832C24.5041 17.7832 25.5261 19.0605 25.5261 20.625C25.5261 22.1895 24.5088 23.4668 23.2525 23.4668ZM10.5011 15C13.8155 15 16.5017 11.6426 16.5017 7.5C16.5017 3.35742 13.8155 0 10.5011 0C7.1867 0 4.50047 3.35742 4.50047 7.5C4.50047 11.6426 7.1867 15 10.5011 15ZM19.9334 28.2715C19.8255 28.2012 19.7177 28.1191 19.6146 28.043L19.2442 28.3125C18.9629 28.5117 18.6442 28.623 18.3254 28.623C17.8144 28.623 17.3221 28.3535 16.9705 27.8848C16.1126 26.7246 15.4563 25.3125 15.086 23.8066C14.8281 22.7695 15.175 21.6738 15.9251 21.1289L16.2955 20.8594C16.2908 20.707 16.2908 20.5547 16.2955 20.4023L15.9251 20.1328C15.175 19.5938 14.8281 18.4922 15.086 17.4551C15.1282 17.2852 15.1891 17.1152 15.236 16.9453C15.0578 16.9277 14.8844 16.875 14.7016 16.875H13.9187C12.8779 17.4727 11.72 17.8125 10.5011 17.8125C9.28223 17.8125 8.12898 17.4727 7.08356 16.875H6.30066C2.82217 16.875 0 20.4023 0 24.75V27.1875C0 28.7402 1.00792 30 2.25024 30H18.752C19.2255 30 19.6661 29.8125 20.0271 29.502C19.9709 29.2793 19.9334 29.0508 19.9334 28.8105V28.2715Z" fill="currentColor" />
      </svg>
      <span>User Management</span>
    </a>

    <a href="/product-management.php" class="sidebar-link">
      <svg width="16" height="16" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M26.2873 18.75H11.0194L11.3603 20.625H25.3404C26.1425 20.625 26.737 21.4629 26.5593 22.3429L26.2719 23.7653C27.2454 24.2969 27.9167 25.4196 27.9167 26.7187C27.9167 28.547 26.5876 30.026 24.9571 29.9996C23.4038 29.9745 22.1264 28.5564 22.0844 26.8094C22.0615 25.8551 22.4014 24.9901 22.9596 24.3749H12.0404C12.5809 24.9706 12.9167 25.8003 12.9167 26.7187C12.9167 28.5828 11.535 30.0838 9.86094 29.9964C8.37448 29.9187 7.16557 28.5676 7.08745 26.8958C7.02714 25.6047 7.63099 24.4668 8.54844 23.8731L4.88974 3.75H1.25C0.559636 3.75 0 3.12041 0 2.34375V1.40625C0 0.62959 0.559636 0 1.25 0H6.59005C7.18386 0 7.69568 0.46998 7.81469 1.12441L8.29208 3.75H28.7495C29.5516 3.75 30.1462 4.58795 29.9684 5.46791L27.5063 17.6554C27.3769 18.2957 26.871 18.75 26.2873 18.75ZM21.25 9.84375H18.75V7.5C18.75 6.98221 18.3769 6.5625 17.9167 6.5625H17.0833C16.6231 6.5625 16.25 6.98221 16.25 7.5V9.84375H13.75C13.2897 9.84375 12.9167 10.2635 12.9167 10.7812V11.7187C12.9167 12.2365 13.2897 12.6562 13.75 12.6562H16.25V15C16.25 15.5178 16.6231 15.9375 17.0833 15.9375H17.9167C18.3769 15.9375 18.75 15.5178 18.75 15V12.6562H21.25C21.7103 12.6562 22.0833 12.2365 22.0833 11.7187V10.7812C22.0833 10.2635 21.7103 9.84375 21.25 9.84375Z" fill="currentColor" />
      </svg>
      <span>Product Management</span>
    </a>

    <a href="/order-management.php" class="sidebar-link active headadmin-btns d-none">
      <svg width="16" height="16" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M29.25 20.625H28.5V14.291C28.5 13.5469 28.2609 12.832 27.8391 12.3047L23.1562 6.45117C22.7344 5.92383 22.1625 5.625 21.5672 5.625H19.5V2.8125C19.5 1.25977 18.4922 0 17.25 0H5.25C4.00781 0 3 1.25977 3 2.8125V5.625H0.375C0.16875 5.625 0 5.83594 0 6.09375V7.03125C0 7.28906 0.16875 7.5 0.375 7.5H13.125C13.3313 7.5 13.5 7.71094 13.5 7.96875V8.90625C13.5 9.16406 13.3313 9.375 13.125 9.375H1.875C1.66875 9.375 1.5 9.58594 1.5 9.84375V10.7812C1.5 11.0391 1.66875 11.25 1.875 11.25H11.625C11.8313 11.25 12 11.4609 12 11.7188V12.6562C12 12.9141 11.8313 13.125 11.625 13.125H0.375C0.16875 13.125 0 13.3359 0 13.5938V14.5312C0 14.7891 0.16875 15 0.375 15H10.125C10.3313 15 10.5 15.2109 10.5 15.4688V16.4062C10.5 16.6641 10.3313 16.875 10.125 16.875H3V24.375C3 27.4805 5.01562 30 7.5 30C9.98438 30 12 27.4805 12 24.375H18C18 27.4805 20.0156 30 22.5 30C24.9844 30 27 27.4805 27 24.375H29.25C29.6625 24.375 30 23.9531 30 23.4375V21.5625C30 21.0469 29.6625 20.625 29.25 20.625ZM7.5 27.1875C6.25781 27.1875 5.25 25.9277 5.25 24.375C5.25 22.8223 6.25781 21.5625 7.5 21.5625C8.74219 21.5625 9.75 22.8223 9.75 24.375C9.75 25.9277 8.74219 27.1875 7.5 27.1875ZM22.5 27.1875C21.2578 27.1875 20.25 25.9277 20.25 24.375C20.25 22.8223 21.2578 21.5625 22.5 21.5625C23.7422 21.5625 24.75 22.8223 24.75 24.375C24.75 25.9277 23.7422 27.1875 22.5 27.1875ZM26.25 15H19.5V8.4375H21.5672L26.25 14.291V15Z" fill="currentColor" />
      </svg>
      <span>Order Management</span>
    </a>

    <a href="/customer-violation-reports.php" class="sidebar-link headadmin-btns d-none">
      <svg width="16" height="16" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M30 15C30 23.2861 23.2837 30 15 30C6.71631 30 0 23.2861 0 15C0 6.71873 6.71631 0 15 0C23.2837 0 30 6.71873 30 15ZM15 18.0242C13.4634 18.0242 12.2177 19.2699 12.2177 20.8065C12.2177 22.343 13.4634 23.5887 15 23.5887C16.5366 23.5887 17.7823 22.343 17.7823 20.8065C17.7823 19.2699 16.5366 18.0242 15 18.0242ZM12.3585 8.02343L12.8072 16.2492C12.8281 16.6342 13.1464 16.9355 13.5319 16.9355H16.4681C16.8536 16.9355 17.1719 16.6342 17.1928 16.2492L17.6415 8.02343C17.6642 7.60766 17.3332 7.25806 16.9168 7.25806H13.0831C12.6668 7.25806 12.3358 7.60766 12.3585 8.02343Z" fill="currentColor" />
      </svg>
      <span>Customer Violation Reports</span>
    </a>

    <button type="button" class="sidebar-link sidebar-logout logout text-left">
      <svg width="16" height="16" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M5.625 0.615433H10.5469C10.9336 0.615433 11.25 1.02865 11.25 1.5337V4.59459C11.25 5.09964 10.9336 5.51286 10.5469 5.51286H5.625C4.58789 5.51286 3.75 6.60713 3.75 7.96157L3.75 22.6539C3.75 24.0083 4.58789 25.1026 5.625 25.1026H10.5469C10.9336 25.1026 11.25 25.5158 11.25 26.0208V29.0817C11.25 29.5868 10.9336 30 10.5469 30H5.625C2.51953 30 0 26.7095 0 22.6539L0 7.96157C0 3.90589 2.51953 0.615433 5.625 0.615433ZM8.37891 15.9964L18.2227 28.8522C19.1016 30 20.625 29.1965 20.625 27.5513V20.2051H28.5938C29.373 20.2051 30 19.3864 30 18.3686L30 11.0225C30 10.0047 29.373 9.18593 28.5938 9.18593H20.625L20.625 1.83979C20.625 0.194559 19.1016 -0.608925 18.2227 0.53891L8.37891 13.3947C7.83398 14.114 7.83398 15.2771 8.37891 15.9964Z" fill="currentColor" />
      </svg>
      <span>Sign Out</span>
    </button>
  </div>

  <div class="dashboard-main">
    <header class="dashboard-header">
      <h5>Order Management</h5>
      <img src="" alt="Admin image" class="admin-pp mr-2" width="60" />
      <span class="admin-name"></span>
    </header>

    <div class="d-flex flex-align-center mt-4 mx-3">
      <div class="flex-1">
        <input type="text" id="transaction-search" name="q" placeholder="Search Transaction ID" class="form-control form-control-white box-shadow table-search" />
      </div>

      <div>
        <button type="button" class="btn btn-tertiary btn-sm text-md p-1 mr-2" data-modal="#modal-delivery-fees">View Delivery Fees</button>
      </div>

      <div>
        <select id="transactions-category-select" class="btn btn-sm btn-tertiary text-md py-1">
          <option value="all" selected>Sort By: All</option>
          <option value="success">Sort By: Done</option>
          <option value="pending">Sort By: Undone</option>
        </select>
      </div>
    </div>

    <table class="dashboard-table">
      <thead>
        <tr>
          <th>Transaction ID</th>
          <th>
            <div class="d-flex flex-align-center flex-center">
              <span class="mr-1">Transaction Date</span>
              <div class="dropdown">
                <button type="button" class="btn btn-text" data-dropdown="toggle">
                  <img src="/imgs/down.svg" alt="Sort by transaction date date" width="12" />
                </button>
                <div class="dropdown-content">
                  <button type="button" class="btn btn-text btn-block text-md py-1" id="sort-date-ascending">Oldest</button>
                  <button type="button" class="btn btn-text btn-block text-md py-1" id="sort-date-descending">Latest</button>
                </div>
              </div>
            </div>
          </th>
          <th>Customer Code</th>
          <th>Total Amount</th>
          <th>Order Type</th>
          <th>Order Status</th>
          <th>Actions</th>
        </tr>
      </thead>

      <tbody id="transactions"></tbody>
    </table>

    <div class="d-flex flex-space-between flex-align-center mx-5 mb-2">
      <div>
        <span>Page Limit: </span>
        <input type="number" id="limit-page" value="10" />
      </div>

      <div class="pagination d-flex flex-align-center flex-center">
        <button type="button" class="btn btn-text mr-2" data-page="" data-prev>
          <img src="/imgs/prev.svg" alt="Previous" width="18" />
        </button>

        <button type="button" class="btn btn-background-secondary btn-round-sm btn-sm mr-2" data-page="1">1</button>
        <div id="pages" class="d-content"></div>

        <button type="button" class="btn btn-text mr-2" data-page="" data-next>
          <img src="/imgs/next.svg" alt="Next" width="18" />
        </button>
      </div>
    </div>
  </div>

  <div class="modal-container d-none"></div>
  <div id="modal-order" class="modal">
    <div class="card p-0">
      <div class="d-flex flex-align-center mt-1 mb-2 pt-1 px-2">
        <div class="card-title text-center flex-1">ORDER DETAILS</div>
        <div class="modal-x">
          <button type="button" class="btn btn-text" data-modal="#modal-order">
            <svg fill="#000" width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M376.6 427.5c11.31 13.58 9.484 33.75-4.094 45.06c-5.984 4.984-13.25 7.422-20.47 7.422c-9.172 0-18.27-3.922-24.59-11.52L192 305.1l-135.4 162.5c-6.328 7.594-15.42 11.52-24.59 11.52c-7.219 0-14.48-2.438-20.47-7.422c-13.58-11.31-15.41-31.48-4.094-45.06l142.9-171.5L7.422 84.5C-3.891 70.92-2.063 50.75 11.52 39.44c13.56-11.34 33.73-9.516 45.06 4.094L192 206l135.4-162.5c11.3-13.58 31.48-15.42 45.06-4.094c13.58 11.31 15.41 31.48 4.094 45.06l-142.9 171.5L376.6 427.5z"/></svg>
          </button>
        </div>
      </div>

      <div class="px-4 pb-3">
        <table class="table-details">
          <tr>
            <td>Customer Name:</td>
            <td id="order-customer-name"></td>
          </tr>

          <tr>
            <td>Transaction ID:</td>
            <td id="transaction-id"></td>
          </tr>

          <tr>
            <td>Transaction Date:</td>
            <td id="transaction-date"></td>
          </tr>

          <tr>
            <td>Customer Code:</td>
            <td id="order-customer-code"></td>
          </tr>

          <tr>
            <td>Address:</td>
            <td id="order-customer-address"></td>
          </tr>

          <tr>
            <td>Order Type:</td>
            <td id="order-type"></td>
          </tr>
        </table>

        <table class="table-products">
          <thead>
            <th class="px-2 text-center">Farmer Code</th>
            <th class="px-2 text-center">Product Name</th>
            <th class="px-2 text-center">Quantity</th>
            <th class="px-2 text-center">Price</th>
            <th class="px-2 text-center">Total Amount</th>
          </thead>

          <tbody id="orders"></tbody>
        </table>

        <div class="text-center">
          <button type="button" class="btn btn-primary px-5" data-modal="#modal-order">OK</button>
        </div>
      </div>
    </div>
  </div>

  <div id="modal-delivery-fees" class="modal">
    <div class="card card-round-sm text-center p-0">
      <table class="table-details">
        <thead>
          <tr>
            <th>Barangay</th>
            <th>Fees</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody id="delivery-fees"></tbody>
      </table>
    </div>
  </div>

  <div id="modal-update-successful" class="modal">
    <div class="card card-round-sm card-tertiary text-center p-0">
      <div class="d-flex flex-align-center mt-1 mx-2">
        <div class="flex-1"></div>
        <div class="modal-x">
          <button type="button" class="btn btn-text" data-modal="#modal-update-successful">
            <img src="/imgs/circle-x.svg" alt="Exit modal" width="24" height="24" />
          </button>
        </div>
      </div>

      <div class="p-2">
        <img src="/imgs/modal-check.svg" alt="Success" width="48" height="48" class="mb-2" />
        <div>Delivery fee successfully updated!</div>
      </div>
    </div>
  </div>

  <div id="modal-confirm-order" class="modal">
    <div class="card card-round-sm card-tertiary text-center p-0">
      <div class="d-flex flex-align-center mt-1 mx-2">
        <div class="flex-1"></div>
        <div class="modal-x">
          <button type="button" class="btn btn-text" data-modal="#modal-confirm-order">
            <img src="/imgs/circle-x.svg" alt="Exit modal" width="24" height="24" />
          </button>
        </div>
      </div>

      <div class="p-2 text-center">
        <div class="mb-3">Confirm Order?</div>
        <div class="d-flex flex-space-around mx-4">
          <button type="button" class="btn btn-secondary mr-2" data-order="">Yes</button>
          <button type="button" class="btn btn-secondary" data-modal="#modal-confirm-order">No</button>
        </div>
      </div>
    </div>
  </div>

  <div id="modal-unconfirm-order" class="modal">
    <div class="card card-round-sm card-tertiary text-center p-0">
      <div class="d-flex flex-align-center mt-1 mx-2">
        <div class="flex-1"></div>
        <div class="modal-x">
          <button type="button" class="btn btn-text" data-modal="#modal-unconfirm-order">
            <img src="/imgs/circle-x.svg" alt="Exit modal" width="24" height="24" />
          </button>
        </div>
      </div>

      <div class="p-2 text-center">
        <div class="mb-3">Unconfirm Order?</div>
        <div class="d-flex flex-space-around mx-4">
          <button type="button" class="btn btn-secondary mr-2" data-order="">Yes</button>
          <button type="button" class="btn btn-secondary" data-modal="#modal-unconfirm-order">No</button>
        </div>
      </div>
    </div>
  </div>

  <div id="modal-shipping-edit" class="modal">
    <form action="/api/admin/update-ship.php" method="post" id="form-update" class="card card-round-sm card-tertiary text-center p-0">
      <div class="d-flex flex-align-center mt-1 mx-2">
        <div class="flex-1"></div>
        <div class="modal-x">
          <button type="button" class="btn btn-text" data-modal="#modal-shipping-edit">
            <img src="/imgs/circle-x.svg" alt="Exit modal" width="24" height="24" />
          </button>
        </div>
      </div>

      <div class="p-2 text-center">
        <div class="mb-2"><span class="edit-brgy-name"></span></div>
        <input type="hidden" id="edit-brgy-name-input" name="brgy-name" />

        <div class="form-group mb-2">
          <input type="text" name="amount" placeholder="Shipping fee" class="form-control p-1" />
        </div>

        <div class="d-flex flex-space-around mx-4">
          <button type="button" class="btn btn-secondary btn-sm mr-2" data-modal="#modal-shipping-edit">Cancel</button>
          <button type="submit" class="btn btn-secondary btn-sm">Confirm</button>
        </div>
      </div>
    </form>
  </div>

  <template id="temp-page-btn">
    <button type="button" class="btn btn-background-secondary btn-round-sm btn-sm mr-2" data-page=""></button>
  </template>

  <template id="temp-ship">
    <tr>
      <td class="brgy-name"></td>
      <td><span class="brgy-fees"></span> PHP</td>
      <td>
        <button type="button" class="brgy-edit btn btn-text">
          <img src="/imgs/edit.svg" alt="Edit barangay" width="16" height="16" />
        </button>
      </td>
    </tr>
  </template>

  <template id="temp-transaction">
    <tr>
      <td class="transaction-id"></td>
      <td class="transaction-date"></td>
      <td class="customer-name"></td>
      <td><span class="total-amount"></span> PHP</td>
      <td class="order-type"></td>
      <td class="order-status-text"></td>
      <td class="d-flex flex-align-center">
        <img src="" alt="" width="18" class="order-status mr-1" />
        <button type="button" class="order-status-violation btn btn-secondary btn-sm text-sm mr-1" disabled>Add Violation</button>
        <a href="#" class="transaction-action">View Details</a>
      </td>
    </tr>
  </template>

  <template id="temp-transaction-details">
    <tr>
      <td class="farmer-code text-center"></td>
      <td class="product-name text-center"></td>
      <td class="text-center"><span class="order-quantity"></span> KG</td>
      <td class="text-center"><span class="order-price"></span> PHP</td>
      <td class="text-center"><span class="order-amount"></span> PHP</td>
    </tr>
  </template>

  <template id="temp-details-total">
    <tr>
      <td colspan="2" class="text-center"></td>
      <td colspan="2" class="total-name text-right"></td>
      <td class="total-value text-center text-bold"></td>
    </tr>
  </template>
</main>
<?php

out_footer();

?>
