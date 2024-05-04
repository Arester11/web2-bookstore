$(document).ready(function () {
  $(".btnLogoutAdmin").click(function () {
    $.ajax({
      type: "post",
      url: "../controller/admin/index.controller.php",
      dataType: "html",
      data: {
        isLogout: true,
      },
    }).done(function (result) {
      if (result) {
        alert("Đăng xuất thành công!");
        location.reload();
      } else {
        alert("Hệ thống gặp sự cố không thể đăng xuất!");
      }
    });
  });

  $.ajax({
    type: "post",
    url: "../controller/admin/index.controller.php",
    dataType: "html",
    data: {
      isRender: true,
    },
  }).done(function (result) {
    const data = JSON.parse(result);
    renderSiderBars(data);
  });
});

function renderSiderBars(data) {
  const siderBars = document.querySelector(".sidebar__items");
  siderBars.innerHTML = "";

  var params = new URLSearchParams(window.location.search);
  var page = params.get("page");

  var sidebarItems = [
    {
      page: "home",
      name: "Trang chủ",
      icon: "fa-house",
      href: "?page=home",
      fncid: 1,
    },
    {
      page: "product",
      name: "Sản phẩm",
      icon: "fa-book",
      href: "?page=product",
      fncid: 2,
    },
    {
      page: "order",
      name: "Đơn hàng",
      icon: "fa-cart-shopping",
      href: "?page=order",
      fncid: 3,
    },
    {
      page: "account",
      name: "Thành viên",
      icon: "fa-user",
      href: "?page=account",
      fncid: 4,
    },
    {
      page: "publisher",
      name: "Nhà xuất bản",
      icon: "fa-upload",
      href: "?page=publisher",
      fncid: 5,
    },
    {
      page: "author",
      name: "Tác giả",
      icon: "fa-book-open-reader",
      href: "?page=author",
      fncid: 6,
    },
    {
      page: "category",
      name: "Thể loại sách",
      icon: "fa-list",
      href: "?page=category",
      fncid: 7,
    },
    {
      page: "supplier",
      name: "Nhà cung cấp",
      icon: "fa-industry",
      href: "?page=supplier",
      fncid: 8,
    },
    {
      page: "receipt",
      name: "Nhập hàng",
      icon: "fa-file-invoice",
      href: "?page=receipt",
      fncid: 9,
    },
    
    {
      page: "role",
      name: "Phân quyền",
      icon: "fa-gavel",
      href: "?page=role",
      fncid: 10,
    },
    {
      page: "discount",
      name: "Khuyễn mãi",
      icon: "fa-file-invoice",
      href: "?page=discount",
      fncid: 11,
    },
    
  ];

  let html = ""; // Khởi tạo biến html ở đây

  sidebarItems.forEach((siderbarItem, index) => {
    let active = "";
    let href = "#";
    let nonActive = "";

    data.forEach((role) => {
      if (siderbarItem.fncid == role.function_id || siderbarItem.fncid == 1) {
        href = siderbarItem.href;
      }
    });

    active = page == siderbarItem.page ? "active" : "";
    if (page == null && siderbarItem.page == "home") {
      active = "active";
      // return;
    }

    if (href == "#" && siderbarItem.fncid != 1) {
      nonActive = "nonActive";
    }

    html += `<li class="sidebar__item ${active} ${nonActive}"  fncid="${siderbarItem.fncid}">
              <a href="${href}"><i class="fa-solid ${siderbarItem.icon}"></i>${siderbarItem.name}</a>
            </li>`;
  });

  siderBars.innerHTML = html;
}
