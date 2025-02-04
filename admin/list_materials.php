<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>

<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">

        <?php include 'includes/navbar.php'; ?>
        <?php include 'includes/menubar.php'; ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Equipments
                </h1>
                <ol class="breadcrumb">
                    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active">Equipments</li>
                </ol>
            </section>
            <!-- Main content -->
            <section class="content">
                <?php
                if (isset($_SESSION['error'])) {
                    echo "
            <div class='alert alert-danger alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-warning'></i> Error!</h4>
              " . $_SESSION['error'] . "
            </div>
          ";
                    unset($_SESSION['error']);
                }
                if (isset($_SESSION['success'])) {
                    echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-check'></i> Success!</h4>
              " . $_SESSION['success'] . "
            </div>
          ";
                    unset($_SESSION['success']);
                }
                ?>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm btn-flat"><i class="fa fa-plus"></i> New</a>
                            </div>
                            <div class="box-body">
                                <table id="example1" class="table table-bordered">
                                    <thead>
                                        <!--<th>Materials ID</th>-->
                                        <th>Materials Name</th>
                                        <th>Quantity</th>
                                        <!--<th>Unit</th>-->
                                        <!--<th>Price</th>-->
                                        <!--<th>Stock</th>-->
                                        <!--<th>Tools</th>-->
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT * FROM materials_list";
                                        $query = $conn->query($sql);
                                        while ($row = $query->fetch_assoc()) {
                                            echo "
                        <tr>
                        
                          <td>" . $row['materials_name'] . "</td>
                          <td>" . $row['quantity'] . "</td>
                          
                          <td>
                         
                           
                            <button class='btn btn-primary btn-sm addstock btn-flat' data-id='" . $row['list_id'] . "'><i class='fa fa-edit'></i> Add Quantity</button>
                            <button class='btn btn-success btn-sm edit btn-flat' data-id='" . $row['list_id'] . "'><i class='fa fa-edit'></i> Edit</button>
                            <button class='btn btn-danger btn-sm delete btn-flat' data-id='" . $row['list_id'] . "'><i class='fa fa-trash'></i> Delete</button>
                          </td>
                        </tr>
                      ";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include 'includes/footer.php'; ?>
        <?php include 'includes/material_list_stock_modal.php'; ?>

        <?php include 'includes/material_list_modal.php'; ?>


    </div>
    <?php include 'includes/scripts.php'; ?>
    <script>
        $(function() {
            $(document).on('click', '.edit', function() {
                // e.preventDefault();
                $('#edit').modal('show');
                var list_id = $(this).data('id');
                getRow(list_id);
            });

            $(document).on('click', '.delete', function() {
                // e.preventDefault();
                $('#delete').modal('show');
                var list_id = $(this).data('id');
                getRow(list_id);
            });

        });

        function getRow(list_id) {
            $.ajax({
                type: 'POST',
                url: 'list_materials_row.php',
                data: {
                    list_id: list_id
                },
                dataType: 'json',
                success: function(response) {
                    //$('.materials_id').html(response.materials_id);
                    $('.list_id').val(response.list_id);

                    $('#list_id').val(response.list_id);
                    $('.del_materials_name').html(response.materials_name);
                    $('#edit_material_name').val(response.materials_name);

                    // $('#edit_material_description').val(response.description);
                    // $('#edit_material_unit').val(response.unit);
                    // $('#edit_material_price').val(response.price);
                    // $('#edit_material_pieces').val(response.stock);

                }
            });
        }


        $(function() {
            $(document).on('click', '.addstock', function() {
                // e.preventDefault();
                $('#addstock').modal('show');
                var list_id = $(this).data('id');
                getRow2(list_id);
            });

        });

        function getRow2(list_id) {
            $.ajax({
                type: 'POST',
                url: 'list_material_stock_add_row.php',
                data: {
                    list_id: list_id
                },
                dataType: 'json',
                success: function(response) {
                    //$('.materials_id').html(response.materials_id);
                    $('.list_id').val(response.list_id);

                    $('#listid').val(response.list_id);
                    $('#edit_quantity').val(response.quantity);
                }
            });
        }
    </script>

</body>

</html>