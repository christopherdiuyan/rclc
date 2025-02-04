<?php
include '../includes/conn.php';



if (isset($_POST['empID'])) {
  $empID = $_POST['empID'];
  $cutoffID = $_POST['cutoffID'];

  // $sql = "SELECT *, employees.id AS empid FROM employees LEFT JOIN position ON position.id=employees.position_id LEFT JOIN schedules ON schedules.id=employees.schedule_id WHERE employees.employee_id='$empID'";
  // $query = $conn->query($sql);
  // $row = $query->fetch_assoc();

  // $sql = "SELECT *, sum(num_hr) AS total_hr, attendance.employee_id AS empid FROM attendance LEFT JOIN employees ON employees.employee_id=attendance.employee_id LEFT JOIN position ON position.id=employees.position_id WHERE employee_id = '$empID'";
  // $query = $conn->query($sql);
  // $row = $query->fetch_assoc();

?>
  <div class="panel" id="payrollSummary">
    <div class="box-body">
      <div class="row">
        <h4><span class="glyphicon glyphicon-file"></span>Attendance Record (Cut-off date)</h4>
      </div>
      <table id="viewAttendanceTable" class="table table-bordered">
        <thead>
          <th>Date</th>
          <th>Day</th>
          <th>Shift Schedule</th>
          <th>QR Logs</th>
          <th>OT (hrs.)</th>
          <th>Total Hours</th>
          <th>Project Name</th>
          <th>Project Location</th>
          <!--<th>Actions</th> -->
        </thead>
        <tbody>
          <!--
                Position - Employee table link to Position table
                Total_Hr - sum of num_hr in attendance table
                Cash Advance - cashadvance table
                Cost of Damage Materials - price sa project_materials_log
                Deduction -  (deductions table)
                OT - Attendance table
              -->

          <?php
          //assumming "0" ay default value or current cut off date
          if ($cutoffID != 0) {
            $sql2 = "SELECT * FROM cutoff WHERE cutoff_id = '$cutoffID'";
            $query = $conn->query($sql2);
            $rowcutoff = $query->fetch_assoc();
            $cutoffstartdate = $rowcutoff['start_date'];
            $cutoffenddate = $rowcutoff['end_date'];
          } else {
            //bago magload yung page ito yung e-eexcute niya (current cutoff date)
            $sql2 = "SELECT * FROM cutoff WHERE cutoff_id = (SELECT MAX(cutoff_id) FROM cutoff)";
            $query = $conn->query($sql2);
            $rowcutoff = $query->fetch_assoc();
            $cutoffstartdate = $rowcutoff['start_date'];
            $cutoffenddate = $rowcutoff['end_date'];
          }

          // $sql = "SELECT *, attendance.employee_id AS empid FROM attendance LEFT JOIN employees ON employees.employee_id=attendance.employee_id LEFT JOIN position ON position.id=employees.position_id WHERE employees.employee_id = '$empID'";			 
          // $sql = "SELECT *, attendance.employee_id AS empid FROM attendance LEFT JOIN employees ON employees.employee_id=attendance.employee_id LEFT JOIN project_employee ON project_employee.name=employees.employee_id LEFT JOIN project ON project.project_id=project_employee.projectid LEFT JOIN position ON position.id=employees.position_id WHERE employees.employee_id = '$empID'";
          $sql = "SELECT *, attendance.employee_id AS empid, attendance.time_in as att_time_in, attendance.time_out as att_time_out  FROM attendance LEFT JOIN employees ON employees.employee_id=attendance.employee_id 
			  LEFT JOIN overtime ON overtime.employee_id=employees.employee_id LEFT JOIN project_employee ON project_employee.name=employees.employee_id 
			  LEFT JOIN project ON project.project_id=project_employee.projectid 
			  LEFT JOIN position ON position.id=employees.position_id LEFT JOIN schedules ON schedules.id=employees.schedule_id 
			  WHERE employees.employee_id = '$empID' AND attendance.date BETWEEN '$cutoffstartdate' AND '$cutoffenddate'";
          $query = $conn->query($sql);
          while ($row = $query->fetch_assoc()) {

            $dateAttendance = $row['date'];
            $sqlOT = "SELECT * FROM overtime WHERE employee_id = '$empID' AND date_overtime = '$dateAttendance' ";
            $resultOT = mysqli_query($conn, $sqlOT);
            $rowOT = mysqli_fetch_assoc($resultOT);

            if ($rowOT != NULL) {
              $rowOT = $rowOT['hours'] / 60;
            } else $rowOT = 0;

          ?>
            <tr>
              <td><?php echo date("M j, Y", strtotime($row['date'])) ?></td>
              <td><?php echo date('D', strtotime($row['date'])) ?></td>
              <td><?php echo date('h:i A', strtotime($row['time_in'])) . " - " . date('h:i A', strtotime($row['time_out']));  ?></td>
              <td><?php echo date('h:i A', strtotime($row['att_time_in'])) . " - " . date('h:i A', strtotime($row['att_time_out']));  ?></td>
              <td><?php echo number_format($rowOT, 2) ?></td>
              <td><?php echo number_format($row['num_hr'] + $rowOT, 2) ?></td>
              <td><?php echo $row['project_name']; ?></td>
              <td><?php echo $row['project_address']; ?></td>
              <!--<td>
					<button class="btn btn-success btn-sm edit btn-flat" data-toggle="tooltip" title="Print" id="<?php echo $row['employee_id']; ?>" onclick="window.addEventListener('load', window.print());"><i class="glyphicon glyphicon-print"></i> Print</button>
					</td> -->
            </tr>


          <?php
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
  <!-- DataTables -->

  <script>
    $('#viewAttendanceTable').DataTable({
      "order": [
        [0, "desc"]
      ],
      responsive: true,
      "paging": true,
      "lengthChange": false,
      pageLength: 5,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false
    })
  </script>
<?php
}
?>