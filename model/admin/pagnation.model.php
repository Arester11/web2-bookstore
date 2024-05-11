<?php
class pagnation
{
    private $number_of_item;
    private $current_page;
    private $table;
    private $filter = "";
    private $orderby = "";

    public function __construct($number_of_item, $current_page, $table)
    {
        $this->number_of_item = $number_of_item;
        $this->current_page = $current_page;
        $this->table = $table;
    }
    public function setNumberOfItem($number_of_item)
    {
        $this->number_of_item = $number_of_item;
    }
    public function getNumberOfItem()
    {
        return $this->number_of_item;
    }
    public function setCurrentPage($current_page)
    {
        $this->current_page = $current_page;
    }
    public function getCurrentPage()
    {
        return $this->current_page;
    }
    public function setTable($table)
    {
        $this->table = $table;
    }
    public function getTable()
    {
        return $this->table;
    }
    public function setFilter($filter)
    {
        $this->filter = $filter;
    }
    public function getFilter()
    {
        return $this->filter;
    }
    public function setOrderby($orderby)
    {
        $this->orderby = $orderby;
    }
    public function getOrderby()
    {
        return $this->orderby;
    }
    public function getData()
    {
        
        $database = new connectDB();
        $offset = ($this->current_page - 1) * $this->number_of_item;
        $sql = "SELECT DISTINCT $this->table.* FROM $this->table $this->filter $this->orderby LIMIT $this->number_of_item OFFSET $offset ";
        $result = $database->query($sql);
        $database->close();
        if ($result->num_rows > 0) {
            return (object) array(
                'success' => true,
                'data' => $result
            );
        } else {
            return (object) array(
                'success' => false
            );
        }
    }
    function getTotalRecords()
    {

        $database = new connectDB();
        $total_records = $database->query("SELECT DISTINCT $this->table.* FROM $this->table $this->filter");
        $database->close();
        return $total_records->num_rows;
    }
    public function render()
    {
        $database = new connectDB();
        $load_result = $this->getData();
        if ($load_result->success) {
            $result = $load_result->data;
            switch ($this->table) {

                case "products": {
                        echo '<div class="table__wrapper">
                    <table id="content-product">
                        <thead class="menu">
                            <tr>
                                <th data-order="id">Mã SP <i class="fas fa-sort-up ASC hidden"></i> <i class="fas fa-sort-down DESC hidden"></i></th>
                                <th>Ảnh</th>
                                <th data-order="name">Tên Sản Phẩm <i class="fas fa-sort-up ASC hidden"></i> <i class="fas fa-sort-down DESC hidden"></i></th>
                                <th>Thể loại</i></th>
                                <th>Ngày cập nhật/Ngày tạo</th>
                                <th>Tác giả</th>
                                <th data-order="price">Giá <i class="fas fa-sort-up ASC hidden"></i> <i class="fas fa-sort-down DESC hidden"></i></th>
                                <th data-order="quantity">Số lượng <i class="fas fa-sort-up ASC hidden"></i> <i class="fas fa-sort-down DESC hidden"></i></th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="table-content" id="content">';

                        while ($row = mysqli_fetch_array($result)) {
                            // masp
                            echo '<tr>
                        <td class="id" publisher_id="' . $row['publisher_id'] .'" supplier_id="' . $row['supplier_id'] . '"status="' . $row['status'] . '">' . $row['id'] . '</td>';
                            // img

                            echo '<td class="image">
                        <img src="../' . $row['image_path'] . '" alt="image not found" />';
                            //name
                            echo '<td class="name">' . $row['name'] . '</td>';
                            //catagory
                            $cat_sql = "SELECT *  FROM `categories` WHERE id IN (SELECT category_id  FROM `category_details` WHERE product_id = '" . $row['id'] . "')";
                            $cat_result = $database->query($cat_sql);

                            $cat_str = '<td class="type" value="[';
                            $category = mysqli_fetch_array($cat_result);
                            if ($category) {
                                $cat_str = $cat_str . $category['id'] . ']">';
                                $cat_str = $cat_str . $category['name'];
                            } else $cat_str = $cat_str . ']">';

                            while ($category = mysqli_fetch_array($cat_result)) {
                                $search = '/' . preg_quote(']', '/') . '/';
                                $cat_str = preg_replace($search, ',' . $category['id'] . ']', $cat_str, 1);
                                $cat_str = $cat_str . ", " . $category['name'];
                            }
                            echo $cat_str . '</td>';
                            // date
                            echo '<td class="date">' . date("d/m/Y", strtotime($row['update_date'])) . ' ' . date("d/m/Y", strtotime($row['create_date'])) . '</td>';
                            //author
                            $author_sql = "SELECT *  FROM `authors` WHERE id IN (SELECT author_id  FROM `author_details` WHERE product_id = '" . $row['id'] . "')";
                            $author_result = $database->query($author_sql);
                            $author_str = '<td class="author" value="[';
                            $author = mysqli_fetch_array($author_result);
                            if ($author) {
                                $author_str = $author_str . $author['id'] . ']">';
                                $author_str = $author_str . $author['name'];
                            } else $author_str = $author_str . ']">';

                            while ($author = mysqli_fetch_array($author_result)) {
                                $search = '/' . preg_quote(']', '/') . '/';
                                $author_str = preg_replace($search, ',' . $author['id'] . ']', $author_str, 1);
                                $author_str = $author_str . ", " . $author['name'];
                            }
                            echo $author_str . '</td>';
                            //price and amount
                            echo '<td class="price">';
                            $price_number =  $row['price'];
                            $price = "";
                            while ($price_number > 0) {
                                $price = substr("$price_number", -3, 3) . '.' . $price;
                                $price_number = substr("$price_number", 0, -3);
                            }

                            echo  trim($price, '. ') . '&#8363;</td> <td class="amount">' . $row['quantity'] . '</td>';
                            // button
                            echo '<td class="actions ">
                        <button class="actions--edit" >Sửa</button>
                        </td>';
                            echo '</tr>';
                        }
                        echo ' 
                        </tbody>
                        </table>
                    
                        </div>';
                    }
                    break;
                    case "suppliers": {
                        echo '
                    <div class="table__wrapper">
                    <table id="content-product">
                        <thead class="menu">
                            <tr>
                            <th>Mã nhà cung cấp</th>
                            <th>Tên nhà cung cấp</th>
                            <th>Email </th>
                            <th>Số điện thoại </th>
                            <th>Trạng thái </th>
                            <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="table-content" id="content">

                    ';

                        while ($row = mysqli_fetch_array($result)) {
                            echo '<tr>';
                            echo '<td class="id">'  . $row['id'] . '</td>';
                            echo '<td class="name">' . $row['name'] . '</td>';
                            echo '<td class="email">' . $row['email'] . '</td>';
                            echo '<td class="number_phone">' . $row['number_phone'] . '</td>';
                            if($row['status'] == 1) {
                                echo '<td class="status" >Đang hoạt động</td>';
                            } else {
                                echo '<td class="status" >Không hoạt động</td>';
                            }
                            // echo '<td class="status">' . $row['status'] . '</td>';                          
                            echo '<td class="actions">
                            <button class="actions--edit">Sửa</button>
                            <button class="actions--delete">Xoá</button>
                        </td>
                        </tr>';
                        }
                        echo ' 
                    </tbody>
                    </table>
                    </div>';
                    }
                    break;
                    case "goodsreceipts": {
                        echo '
                            <div class="table__wrapper">
                                <table id="content-product">
                                    <thead class="menu">
                                        <tr>
                                            <th>Mã đơn nhập</th>
                                            <th>Tên nhà cung cấp</th>
                                            <th>Người nhập đơn</th>
                                            <th>Tổng tiền</th>
                                            <th>Ngày tạo</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-content" id="content">
                        ';
                    
                        while ($row = mysqli_fetch_array($result)) {
                            echo '<tr>';
                            echo '<td class="id">'  . $row['id'] . '</td>';                    
                            // Truy vấn tên nhà cung cấp từ bảng 'suppliers'
                            $sup_sql = "SELECT * FROM `suppliers` WHERE id =" . $row['supplier_id'];
                            $sup_result = $database->query($sup_sql);
                            $supplier = mysqli_fetch_array($sup_result);
                            $supplier_name = ($supplier) ? $supplier['name'] : '';
                    
                            echo '<td class="supplierName">' . $supplier_name . '</td>';
                            
                            echo '<td class="staff_id">' . $row['staff_id'] . '</td>';
                            echo '<td class="total_price">';
                            $price_number =  $row['total_price'];
                            $price = "";
                            while ($price_number > 0) {
                                $price = substr("$price_number", -3, 3) . '.' . $price;
                                $price_number = substr("$price_number", 0, -3);
                            }

                            echo  trim($price, '. ') . '&#8363;</td>';
                            echo '<td class="date_create">' . $row['date_create'] . '</td>';
                    
                            echo '<td class="actions">
                                    <button class="actions--edit">Xem chi tiết</button>
                                </td>
                            </tr>';
                        }
                    
                        echo ' 
                            </tbody>
                        </table>
                    </div>';
                    }
                    break;
                    
                
                case "orders": {
                        echo '
                    <div class="table__wrapper">
                        <table id="content-product">
                            <thead class="menu">
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Mã KH</th>
                                    <th>Mã NV</th>
                                    <th>Ngày tạo</th>
                                    <th>Tổng giá</th>
                                    <th>Địa chỉ giao</th>
                                    <th>Trạng thái</th>
                                    <th>Mã giảm giá</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody class="table-content" id="content">
                    ';
                        while ($row = mysqli_fetch_array($result)) {
                            echo '<tr>';
                            echo '<td class="order_id">' . $row[0] . '</td>';
                            echo '<td class="customer_id">' . $row[1] . '</td>';
                            echo '<td class="staff_id">' . $row[2] . '</td>';
                            echo '<td class="date-update">' . $row[4] . '</td>';
                            $price_number = $row['5'];
                            $price = "";
                            while ($price_number > 0) {
                                $price = substr("$price_number", -3, 3) . '.' . $price;
                                $price_number = substr("$price_number", 0, -3);
                            }
                            echo '<td class="total_price">' . trim($price, '.') . '&#8363;</td>';
                            $sql_address = 'SELECT * from delivery_infoes WHERE user_info_id="' . $row[3] . '"';
                            $result_address = $database->query($sql_address);
                            $row_address = mysqli_fetch_array($result_address);

                            echo '<td class="address">' . $row_address['address'].', '.$row_address['ward'] .', '.$row_address['district'] .', '.$row_address['city'] . '</td>';

                            $sql_status = 'SELECT * from order_statuses WHERE id="' . $row[6] . '"';
                            $result_status  = $database->query($sql_status);
                            $row_status = mysqli_fetch_array($result_status);

                            echo '<td class="status">' . $row_status['name'] . '</td>';
                            echo '<td class="discount_code">' . $row['discount_code'] . '</td>';
                            echo '<td class="actions">
                        <button class="actions--view">Chi tiết</button>
                        <!-- <button class="actions--delete">Xoá</button> -->
                        </td>
                        </tr>';
                        }
                        echo ' 
                    </tbody>
                    </table>
                    </div>';
                    }
                    break;
                case "accounts": {
                        echo '
                    <div class="table__wrapper">
                        <table id="content-product">
                            <thead class="menu">
                            <tr>
                                <th>Mã người dùng</th>
                                <th>Loại tài khoản</th>
                                <th>Trạng thái tài khoản</th>
                                <th>Hành động</th>
                            </tr>
                            </thead>
                            <tbody class="table-content" id="content">
                    ';

                        while ($row = mysqli_fetch_array($result)) {
                            echo '<tr>';
                            echo '<td class="id">'  . $row['username'] . '</td>';

                            $sql_role = 'SELECT * from roles WHERE id="' . $row['role_id'] . '"';
                            $row_role = mysqli_fetch_array($database->query($sql_role));

                            echo '<td class="type" value="staff">' . $row_role['name'] . '</td>';
                            $sql_status = 'SELECT * from accounts WHERE username="' . $row['username'] . '"';
                            $row_status = mysqli_fetch_array($database->query($sql_status));

                           $status = "";
                           if($row['status'] == 1) $status = "Hoạt động";
                           if($row['status'] == 0) $status = "Không hoạt động";
                           
                            echo '<td class="status" value="active">'.$status.'</td>';
                            if($row['username'] == "adminfahasa") {
                                echo '<td class="actions">
                                <button class="actions--pass">Đổi mật khẩu</button>
                                </td>
                            </tr>';
                            } else {
                                echo '<td class="actions">
                            <button class="actions--edit">Sửa</button>
                            <button class="actions--pass">Đổi mật khẩu</button>
                            </td>
                        </tr>';
                            }
                        }
                        echo ' 
                    </tbody>
                    </table>
                    </div>';
                    }
                    break;
                case "authors": {
                        echo '
                    <div class="table__wrapper">
                    <table id="content-product">
                        <thead class="menu">
                            <tr>
                            <th>Mã tác giả</th>
                            <th>Tên tác giả</th>
                            <th>Email tác giả</th>
                            <th>Trạng thái</th>                                    
                            <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="table-content" id="content">

                    ';

                        while ($row = mysqli_fetch_array($result)) {
                            echo '<tr>';
                            echo '<td class="id">'  . $row['id'] . '</td>';
                            echo '<td class="name">' . $row['name'] . '</td>';
                            echo '<td class="email">' . $row['email'] . '</td>';
                            if($row['status'] == 1) {
                                echo '<td class="status" >Đang hoạt động</td>';
                            } else {
                                echo '<td class="status" >Không hoạt động</td>';
                            }
                            echo '<td class="actions">
                            <button class="actions--edit">Sửa</button>' ;
                           if($row['status'] == 1) 
                           echo '<button class="actions--delete">Xoá</button>
                            
                        </td>
                        </tr>';
                        }
                        echo ' 
                    </tbody>
                    </table>
                    </div>';
                    }
                    break;
                case "publishers": {
                        echo '
                    <div class="table__wrapper">
                    <table id="content-product">
                        <thead class="menu">
                            <tr>
                            <th>Mã NXB</th>
                            <th>Tên NXB</th>
                            <th>Email NXB</th>
                            <th>Trạng Thái</th>
                            <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="table-content" id="content">

                    ';

                        while ($row = mysqli_fetch_array($result)) {
                            echo '<tr>';
                            echo '<td class="id">'  . $row['id'] . '</td>';
                            echo '<td class="name">' . $row['name'] . '</td>';
                            echo '<td class="email">' . $row['email'] . '</td>';
                            if($row['status'] == 1) {
                                echo '<td class="status" >Đang hoạt động</td>';
                            } else {
                                echo '<td class="status" >Không hoạt động</td>';
                            }
                            echo '<td class="actions">
                            <button class="actions--edit">Sửa</button>
                            <button class="actions--delete">Xoá</button>
                        </td>
                        </tr>';
                        }
                        echo ' 
                    </tbody>
                    </table>
                    </div>';
                    }
                    break;
                   
                case "categories": {
                        echo '
                    <div class="table__wrapper">
                    <table id="content-product">
                        <thead class="menu">
                            <tr>
                            <th>Mã thể loại</th>                 
                            <th>Tên thể loại</th>
                            <th>Số lượng sách</th>
                            <th>Trạng thái</th>
                                      
                            <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="table-content" id="content">

                    ';

                        while ($row = mysqli_fetch_array($result)) {
                            echo '<tr>';
                            echo '<td class="id">'  . $row['id'] . '</td>';
                            echo '<td class="name">' . $row['name'] . '</td>';
                            $sql_amount =
                                "SELECT SUM(quantity) as total 
                                FROM `products`  as p 
                                INNER JOIN category_details as cd ON cd.category_id = " . $row['id'] . "
                                WHERE p.id = cd.product_id";
                            $result_amount = $database->query($sql_amount);
                            $amount = mysqli_fetch_array($result_amount)['total'];
                            if ($amount !== null) {
                                echo '<td class="amount">' . $amount . '</td>';
                            } else {
                                echo '<td class="amount">' . 0 . '</td>';
                            }
                            if($row['status'] == 1) {
                                echo '<td class="status" >Đang hoạt động</td>';
                            } else {
                                echo '<td class="status" >Không hoạt động</td>';
                            }
                            
                            
                            echo '<td class="actions">
                            <button class="actions--edit">Sửa</button>
                            <button class="actions--delete">Xoá</button>
                        </td>
                        </tr>';
                        }
                        echo ' 
                    </tbody>
                    </table>
                    </div>';
                    }
                    break;
                    case "discounts": {
                        echo '
                    <div class="table__wrapper">
                    <table id="content-product">
                        <thead class="menu">
                            <tr>
                            <th>Tên mã giảm giá</th>                 
                            <th>Loại mã giảm giá</th>
                            <th>Giá trị mã giảm giá</th>
                            <th>Ngày bắt đầu</th>
                            <th>Ngày kết thúc</th>
                            <th>Trạng thái</th>                                    
                            <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="table-content" id="content">
                    ';

                        while ($row = mysqli_fetch_array($result)) {
                            echo '<tr>';
                            echo '<td class="discount_code">'  . $row['discount_code'] . '</td>';                                    
                            echo '<td class="type">'.$row['type'].'</td>';
                            echo '<td class="discount_value">' . $row['discount_value'] . '</td>';
                            echo '<td class="start_date">'.$row['start_date'].'</td>';
                            echo '<td class="end_date">'.$row['end_date'].'</td>';
                            if($row['status'] == 1) {
                                echo '<td class="status" >Đang hoạt động</td>';
                            } else {
                                echo '<td class="status" >Không hoạt động</td>';
                            }
                            // echo '<td class="status">'.$row['status'].'</td>';
                            echo '<td class="actions">
                            <button class="actions--edit">Sửa</button>
                            <button class="actions--delete">Xoá</button>
                        </td>
                        </tr>';
                        }
                        echo ' 
                    </tbody>
                    </table>
                    </div>';
                    }
                    break;
                case "functions" : {
                    echo '
                    <div class="table__wrapper">
                    <table id="content-product">
                        <thead class="menu">
                            <tr>
                            <th>Mã quyền</th>                 
                            <th>Tên quyền</th>      
                            <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="table-content" id="content">

                    ';

                        while ($row = mysqli_fetch_array($result)) {
                            echo '<tr>';
                            echo '<td class="id">'  . $row['id'] . '</td>';
                            echo '<td class="name">' . $row['name'] . '</td>';
                            // echo '<td class="status">'.$row['status'].'</td>';
                            // echo '<td class="date-create">'.$row['create_date'].'</td>';
                            // echo '<td class="date-delete">'.$row['delete_date'].'</td>';
                            // echo '<td class="date-update">'.$row['update_date'].'</td>';
                            echo '<td class="actions">
                            <button class="actions--edit">Sửa</button>
                        </td>
                        </tr>';
                        }
                        echo ' 
                    </tbody>
                    </table>
                    </div>';
                }
                break;
            }
            echo '<div class="pagination">';

            $page_number = ceil($this->getTotalRecords() / $this->number_of_item);
            if ($this->current_page > 1) {
                echo "<span class='pag-pre'>&laquo;</span>";
                echo "<span class='pag'>1</span>";
            }
            if ($this->current_page > 2) {
                echo "<span class='pag'>2</span>";
            }
            if ($this->current_page > 4) echo sprintf("...<span class='pag'>%u</span>", $this->current_page - 1);
            elseif ($this->current_page > 3) echo "<span class='pag'>3</span>";
            echo "<span class='active'>$this->current_page</span>";
            if ($this->current_page < $page_number) echo sprintf("<span class='pag'>%u</span>", $this->current_page + 1);
            if ($this->current_page + 3 < $page_number) echo "...";
            if ($this->current_page + 2 < $page_number) echo sprintf("<span class='pag'>%u</span>", $page_number - 1);
            if ($this->current_page + 1 < $page_number) echo "<span class='pag'>$page_number</span>";

            if ($this->current_page < $page_number) echo "<span class='pag-con'>&raquo;</span>";

            echo '</div>';
        } else {
            switch ($this->table) {
                case "products":
                    echo "<div id='zero-item'><h2>Không có sản phẩm nào</h2></div>";
                    break;
                case "orders":
                    echo "<div id='zero-item'><h2>Không có đơn hàng nào</h2></div>";
                    break;
                case "accounts":
                    echo "<div id='zero-item'><h2>Không có tài khoản nào</h2></div>";
                    break;
                case "authors":
                    echo "<div id='zero-item'><h2>Không có tác giả nào</h2></div>";
                    break;
                case "publishers":
                    echo "<div id='zero-item'><h2>Không có NBX nào</h2></div>";
                    break;
                case "suppliers":
                    echo "<div id='zero-item'><h2>Không có nhà cung cấp nào</h2></div>";
                     break;
                     case "discounts":
                        echo "<div id='zero-item'><h2>Không có mã khuyến mãi nào</h2></div>";
                         break;
                case "categories":
                    echo "<div id='zero-item'><h2>Không có thể loại nào</h2></div>";
                    break;
                    case "goodsreceipts":
                        echo "<div id='zero-item'><h2>Không có đơn nhập hàng nào</h2></div>";
                        break;
                case "functions":
                    echo "<div id='zero-item'><h2>Không có quyền nào</h2></div>";
                    break;
            }
        }
        $database->close();
    }
}

function getFilterSQL($table, $data)
{

    switch ($table) {
        case 'products':
            return getProductFilterSQL($data);
            break;
        case 'authors':
            return getAuthorFilterSQL($data);
            break;
            
        case 'categories':
            return getCategoryFilterSQL($data);
            break;
         case 'discounts':
                return getDiscountFilterSQL($data);
             break;
        case 'publishers':
            return getPublisherFilterSQL($data);
            break;
        case 'suppliers':
            return getSupplierFilterSQL($data);
            break;
        case 'functions': 
            return getRoleFilterSQL($data);
            break;
        case 'accounts':
            return getUserFilterSQL($data);
            break;
        case 'goodsreceipts':
            return getReceiptFilterSQL($data);
            break;

    }
}
function getProductFilterSQL($data)
{
    $filter = "";
    $innerjoin = "";
    if (!empty($data)) {
        if (!empty($data['name'])) {
            if ($filter != "") $filter = $filter . " AND ";
            $filter = $filter . "`name` LIKE '%" . $data['name'] . "%'";
        }
        if (!empty($data['id'])) {
            if ($filter != "") $filter = $filter . " AND ";
            $filter = $filter . " id = " . $data['id'];
        }
        if (!empty($data['price_start'])) {
            if ($filter != "") $filter = $filter . " AND ";
            $filter = $filter . " price >= " . $data['price_start'];
        }
        if (!empty($data['price_end'])) {
            if ($filter != "") $filter = $filter . " AND ";
            $filter = $filter . " price <= " . $data['price_end'];
        }
        if ((isset($data['status'])&&$data['status']==0)||!empty($data['status'])&&$data['status']!=-1) {
            if ($filter != "") $filter = $filter . " AND ";
            $filter = $filter . " status=" . $data['status'];
        }
        if (!empty($data['category'])) {
            if ($filter != "") $filter = $filter . " AND ";
            $filter = $filter . " cd.id=products.id ";
            $innerjoin = $innerjoin . "INNER JOIN category_details	as cd ON cd.category_id =' " . $data['category'] . "'";
        }
        if (!empty($data['date_type'])) {
            if (!empty($data['date_start'])) {
                if ($filter != "") $filter = $filter . " AND ";
                $filter = $filter . " `" . $data['date_type'] . "` >= '" . $data['date_start'] . "'";
            }
            if (!empty($data['date_end'])) {
                if ($filter != "") $filter = $filter . " AND ";
                $filter = $filter . " `" . $data['date_type'] . "` <= '" . $data['date_end'] . "'";
            }
        }
        if ($filter != "") $filter = "WHERE " . $filter;
    }
    return $innerjoin . $filter;
}
function getAuthorFilterSQL($data)
{
    $filter = "";
    if (!empty($data)) {
        if (!empty($data['author_name'])) {
            if ($filter != "") $filter = $filter . " AND ";
            $filter = $filter . "`name` LIKE '%" . $data['author_name'] . "%'";
            
        }
        if (!empty($data['author_id'])) {
            if ($filter != "") $filter = $filter . " AND ";
            $filter = $filter . " id = " . $data['author_id'];
        }
        if (!empty($data['author_email'])) {
            if ($filter != "") $filter = $filter . " AND ";
            $filter = $filter . "`email` LIKE '%" . $data['author_email'] . "%'";
        }
        if (!empty($data['author_status'])) {
            if ($filter != "") $filter = $filter . " AND ";
            if ($data['author_status'] == "active") {
               
                $filter = $filter . "status = 1 " ;
            } elseif ($data['author_status'] == "inactive") {
                $filter = $filter . "status = 0 " ;
            }
            
        }


        if ($filter != "") $filter = "WHERE " . $filter;
    }
    return  $filter;
}
function getReceiptFilterSQL($data)
{
    $filter = "";
    $innerjoin = "";
    if (!empty($data)) {
        if (!empty($data['supplierName'])) {
            $supplierName = $data['supplierName'];
            $filter .= " s.name LIKE '%" . $supplierName . "%'";
            $innerjoin .= " INNER JOIN suppliers s ON goodsreceipts.supplier_id = s.id ";
        }
        if (!empty($data['id'])) {
            if (!empty($filter)) $filter .= " AND ";
            $filter .= " goodsreceipts.id = " . $data['id'];
        }
        if (!empty($data['staff_id'])) {
            if (!empty($filter)) $filter .= " AND ";
            $filter .= "`staff_id` LIKE '%" . $data['staff_id'] . "%'";
        }
        if (!empty($data['price_start']) && is_numeric($data['price_start']) && !empty($data['price_end']) && is_numeric($data['price_end']) && $data['price_start'] < $data['price_end']) {
            if (!empty($filter)) $filter .= " AND ";
            $filter .= " total_price BETWEEN " . $data['price_start'] . " AND " . $data['price_end'];
        }
        if (!empty($data['date_type'])) {
            if (!empty($data['date_start']) && !empty($data['date_end']) && $data['date_start'] < $data['date_end']) {
                if (!empty($filter)) $filter .= " AND ";
                $filter .= " `" . $data['date_type'] . "` BETWEEN '" . $data['date_start'] . "' AND '" . $data['date_end'] . "'";
            }
        }
        if (!empty($filter)) $filter = " WHERE " . $filter;
    }
    return $innerjoin . $filter;
}


function getCategoryFilterSQL($data)
{
    $filter = "";
    if (!empty($data)) {
        if (!empty($data['category_name'])) {
            if ($filter != "") $filter = $filter . " AND ";
            $filter = $filter . "`name` LIKE '%" . $data['category_name'] . "%'";
        }
        if (!empty($data['category_id'])) {     
            if ($filter != "") $filter = $filter . " AND ";
            $filter = $filter . " id = " . $data['category_id'];
        }
        if (!empty($data['category_status'])) {
            if ($filter != "") $filter = $filter . " AND ";
            if ($data['category_status'] == "active") {
               
                $filter = $filter . "status = 1 " ;
            } elseif ($data['category_status'] == "inactive") {
                $filter = $filter . "status = 0 " ;
            }
            
        }
        
       

        if ($filter != "") $filter = "WHERE " . $filter;
    }
    return  $filter;
}

function getSupplierFilterSQL($data)
{
    $filter = "";
    if (!empty($data)) {
        if (!empty($data['supplier_name'])) {
            if ($filter != "") $filter = $filter . " AND ";
            $filter = $filter . "`name` LIKE '%" . $data['supplier_name'] . "%'";
        }
        if (!empty($data['supplier_id'])) {     
            if ($filter != "") $filter = $filter . " AND ";
            $filter = $filter . " id = " . $data['supplier_id'];
        }
        if (!empty($data['supplier_status'])) {
            if ($filter != "") $filter = $filter . " AND ";
            if ($data['supplier_status'] == "active") {
               
                $filter = $filter . "status = 1 " ;
            } elseif ($data['supplier_status'] == "inactive") {
                $filter = $filter . "status = 0 " ;
            }
            
        }
        
        if (!empty($data['supplier_date_type'])) {
            if (!empty($data['supplier_date_start'])) {
                if ($filter != "") $filter = $filter . " AND ";
                $filter = $filter . " `" . $data['supplier_date_type'] . "` >= '" . $data['supplier_date_start'] . "'";
            }
            if (!empty($data['supplier_date_end'])) {
                if ($filter != "") $filter = $filter . " AND ";
                $filter = $filter . " `" . $data['supplier_date_type'] . "` <= '" . $data['supplier_date_end'] . "'";
            }
        }

        if ($filter != "") $filter = "WHERE " . $filter;
    }
    return  $filter;
}

function getPublisherFilterSQL($data)
{
    $filter = "";
    if (!empty($data)) {
        if (!empty($data['publisher_name'])) {
            if ($filter != "") $filter = $filter . " AND ";
            $filter = $filter . "`name` LIKE '%" . $data['publisher_name'] . "%'";
        }
        if (!empty($data['publisher_id'])) {
            if ($filter != "") $filter = $filter . " AND ";
            $filter = $filter . " id = " . $data['publisher_id'];
        }
        if (!empty($data['publisher_email'])) {
            if ($filter != "") $filter = $filter . " AND ";
            $filter = $filter . "`email` LIKE '%" . $data['publisher_email'] . "%'";
        }
        if (!empty($data['publisher_status'])) {
            if ($filter != "") $filter = $filter . " AND ";
            if ($data['publisher_status'] == "active") {
               
                $filter = $filter . "status = 1 " ;
            } elseif ($data['publisher_status'] == "inactive") {
                $filter = $filter . "status = 0 " ;
            }
            
        }

        if ($filter != "") $filter = "WHERE " . $filter;
    }
    return $filter;
}
function getRoleFilterSQL($data)
{
    $filter = "";
    if (!empty($data)) {
        if (!empty($data['role_name'])) {
            if ($filter != "") $filter = $filter . " AND ";
            $filter = $filter . "`name` LIKE '%" . $data['role_name'] . "%'";
        }
        if (!empty($data['role_id'])) {     
            if ($filter != "") $filter = $filter . " AND ";
            $filter = $filter . " id = " . $data['role_id'];
        }
        if (!empty($data['role_status'])) {
            if ($filter != "") $filter = $filter . " AND ";
            if ($data['role_status'] == "active") {
               
                $filter = $filter . "status = 1 " ;
            } else if ($data['role_status'] == "inactive") {
                $filter = $filter . "status = 0 " ;
            }
            
        }

        if ($filter != "") $filter = "WHERE " . $filter;
    }
    return  $filter;
}
function getDiscountFilterSQL($data)
{
    $filter = "";
    if (!empty($data)) {
        if (!empty($data['discount_name'])) {
            if ($filter != "") $filter = $filter . " AND ";
            $filter = $filter . "`discount_code` LIKE '%" . $data['discount_name'] . "%'";
        }       
        if (!empty($data['discount_status'])) {
            if ($filter != "") $filter = $filter . " AND ";
            if ($data['discount_status'] == "active") {

                $filter = $filter . "status = 1 " ;
            } elseif ($data['discount_status'] == "inactive") {
                $filter = $filter . "status = 0 " ;
            }

        }



        if ($filter != "") $filter = "WHERE " . $filter;
    }
    return  $filter;
}
function getUserFilterSQL($data)
{
    $filter = "";
    if (!empty($data)) {
        if (!empty($data['user_id'])) {
            if ($filter != "") $filter = $filter . " AND ";
            $filter = $filter . "`username` LIKE '%" . $data['user_id'] . "%'";
        }
        if (!empty($data['user_role'])) {     
            if ($filter != "") $filter = $filter . " AND ";
            $filter = $filter . " role_id = " . $data['user_role'];
        }
        if (!empty($data['user_status'])) {
            if ($filter != "") $filter = $filter . " AND ";
            if ($data['user_status'] == "active") {
                $filter = $filter . "status = 1 " ;
            } else if ($data['user_status'] == "inactive") {
                $filter = $filter . "status = 0 " ;
            }
        }

        if ($filter != "") $filter = "WHERE " . $filter;
    }
    return  $filter;
}