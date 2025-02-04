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
          Overtime
        </h1>
        <ol class="breadcrumb">
          <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
          <li>Employees</li>
          <li class="active">Overtime</li>
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
              <!-- <div class="box-header with-border">
                <a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm btn-flat"><i class="fa fa-plus"></i> New</a>
              </div> -->
              <div class="box-body">
                <table id="example1" class="table table-bordered">
                  <thead>
                    <th class="hidden"></th>
                    <th>Name</th> <!-- with position -->
                    <th>Shift Schedule</th>
                    <th>QR Logs</th>
                    <th>Minutes of OT</th>
                    <th>OT Amount</th>
                    <th>Tools</th>
                    <th>Reasons</th>
                  </thead>
                  <tbody>
                    <?php
                    // $sql = "SELECT *, overtime.id AS otid, employees.employee_id AS empid FROM overtime LEFT JOIN employees ON employees.id=overtime.employee_id ORDER BY date_overtime DESC";
                    $sql = "SELECT *, overtime.id AS otid, employees.employee_id AS empid, schedules.time_in AS stime_in, schedules.time_out AS stime_out, attendance.time_in AS ttime_in, attendance.time_out AS ttime_out FROM overtime LEFT JOIN employees ON employees.employee_id=overtime.employee_id LEFT JOIN position ON position.id=employees.position_id LEFT JOIN attendance ON attendance.employee_id=overtime.employee_id LEFT JOIN schedules ON schedules.id=employees.schedule_id WHERE overtime.date_overtime=attendance.date ORDER BY date_overtime DESC ";
                    $query = $conn->query($sql);
                    while ($row = $query->fetch_assoc()) {
                      echo "
                        <tr>
                          <td class='hidden'></td>
                          
                       <!--   <td>" . $row['empid'] . "</td> -->
                          <td><strong>" . $row['firstname'] . ' ' . $row['lastname'] . '</strong> | ' . $row['description'] . "</td>
                          <td>" . date('h:i A', strtotime($row['stime_in'])) . ' - ' . date('h:i A', strtotime($row['stime_out'])) . "</td> 
                          <td>(". date('M d, Y', strtotime($row['date_overtime'])) .' | ' . date('h:i A', strtotime($row['ttime_in'])) . ' - ' . date('h:i A', strtotime($row['ttime_out'])) . ")</td>
                          <td>" . $row['hours'] . "</td>
                          <td>" . number_format($row['amount'], 2) . "</td>
                          <td> ";
                      if ($row['ot_status'] == "New") {
                        echo "
                            <button class='btn btn-success btn-sm btn-flat edit' data-id='" . $row['otid'] . "'><i class='glyphicon glyphicon-ok-circle'></i> Approve</button>
                            <button class='btn btn-danger btn-sm btn-flat delete' data-id='" . $row['otid'] . "'><i class='glyphicon glyphicon-ban-circle'></i> Decline</button>
                          </td>
                          <td>REASONS</td>
                        </tr>
                      ";
                      } else if ($row['ot_status'] == "Approved") {
                        echo "
                            <button class='btn btn-success btn-sm btn-flat edit' disabled data-id='" . $row['otid'] . "'><i class=' glyphicon glyphicon-ok-circle'></i> Approved</button>
                          </td>
                        </tr>
                        <td>REASONS</td>
                        ";
                      } else if ($row['ot_status'] == "Declined") {
                        echo "
                            <button class='btn btn-danger btn-sm btn-flat delete' disabled data-id='" . $row['otid'] . "'><i class=' glyphicon glyphicon-ban-circle'></i> Declined</button>
                          </td>
                          <td>REASONS</td>
                        </tr>
                        ";
                      }
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
    <?php include 'includes/overtime_modal.php'; ?>
  </div>
  <?php include 'includes/scripts.php'; ?>
  <script>
    $(function() {
      $('.edit').click(function(e) {
        e.preventDefault();
        $('#edit').modal('show');
        var id = $(this).data('id');
        getRow(id);
      });

      $('.delete').click(function(e) {
        e.preventDefault();
        $('#delete').modal('show');
        var id = $(this).data('id');
        getRow(id);
      });
    });

    function getRow(id) {
      $.ajax({
        type: 'POST',
        url: 'overtime_row.php',
        data: {
          id: id
        },
        dataType: 'json',
        success: function(response) {
          var time = response.hours;
          var split = time.split('.');
          var hour = split[0];
          var min = '.' + split[1];
          min = min * 60;
          console.log(min);
          $('.employee_name').html(response.firstname + ' ' + response.lastname);
          $('.otid').val(response.otid);
          $('.othour').html(response.hours + ' minutes');
          $('#datepicker_edit').val(response.date_overtime);
          $('#overtime_date').html(response.date_overtime);
          $('#overtime_dated').html(response.date_overtime);
          $('#hours_edit').val(hour);
          $('#mins_edit').val(min);
          $('#rate_edit').val(response.rate);
        }
      });
    }
  </script>
</body>

</html>