<?php @session_start(); 
if (empty($_SESSION['user_s'])) {
    echo "<meta http-equiv='refresh' content='0;url=index.php'/>";
    exit();
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>ระบบพัสดุโรงพยาบาล</title>
    <LINK REL="SHORTCUT ICON" HREF="images/logo.png">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="../dist/css/skins/_all-skins.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="../plugins/iCheck/flat/blue.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.css">
    <!-- Morris chart -->
    <link rel="stylesheet" href="../plugins/morris/morris.css">
    <!-- jvectormap -->
    <link rel="stylesheet" href="../plugins/jvectormap/jquery-jvectormap-1.2.2.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="../plugins/select2/select2.min.css">
    <!-- Date Picker -->
    <!--<link rel="stylesheet" href="plugins/datepicker/datepicker3.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker-bs3.css">
    <!-- bootstrap wysihtml5 - text editor -->
    <link rel="stylesheet" href="../plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
  </head>

    <?php
    $bo_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
/*if ($_SESSION['Status']=='USER') {
    $empno = $_SESSION['user_s'];
}   */ 
require '../class/db_mng.php';
$myconn=new Db_mng();
$myconn->read="../connection/conn_DB.txt";
$myconn->config();
$db=$myconn->conn_mysqli();
    $sql = "SELECT bo.bo_no,CONCAT(e.firstname,' ',e.lastname) as fullname,d.depName,bo.bo_date,
bo.bo_status,m.mate_name,bor.amount,m.mate_unit,pb.pbo_date,pb.amount AS pbo_amount,
(SELECT CONCAT(e.firstname,' ',e.lastname) FROM emppersonal e
WHERE e.empno=pb.empno AND bo.bo_id=pb.bo_id AND bo.bo_id='$bo_id')payer,
(SELECT CONCAT(e.firstname,' ',e.lastname) FROM emppersonal e
WHERE e.empno=pb.receiver AND bo.bo_id=pb.bo_id AND bo.bo_id='$bo_id')receiver
FROM se_borrow_order bo
LEFT OUTER JOIN se_borrow bor ON bor.bo_id=bo.bo_id
LEFT OUTER JOIN se_pay_borrow pb ON pb.bo_id=bo.bo_id AND pb.borrow_id=bor.borrow_id
INNER JOIN emppersonal e ON e.empno=bo.empno
INNER JOIN department d ON d.depId=bo.dep_id
INNER JOIN se_material m ON m.mate_id=bor.mate_id
WHERE bo.bo_id='$bo_id'
ORDER BY bor.borrow_id ASC,pb.pbo_id ASC";
    $myconn->db_m($sql);
    $result=$myconn->select();
    $myconn->close_mysqli();
   include_once ('../plugins/funcDateThai.php');
    ?>
    <!--<div class="row">
              <div class="col-lg-12">
                <h1><font color='blue'>  รายละเอียดข้อมูลบุคลากร </font></h1> 
                <ol class="breadcrumb alert-success">
                  <li><a href="index.php"><i class="fa fa-home"></i> หน้าหลัก</a></li>
                  <li class="active"><a href="pre_person.php"><i class="fa fa-edit"></i> ข้อมูลพื้นฐาน</a></li>
                  <li class="active"><i class="fa fa-edit"></i> รายละเอียดข้อมูลบุคลากร</li>
                </ol>
              </div>
          </div>-->
    <body class="hold-transition skin-green fixed sidebar-mini">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">ข้อมูลวัสดุ</h3>
                    </div>
                    <div class="panel-body">
                        <div class="col-lg-12">
              <div class="box box-success box-solid">
                <div class="box-header with-border">
                  <h3 class="box-title"><img src='../images/icon_set2/dolly.ico' width='25'> ข้อมูลทั่วไป</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                  </div><!-- /.box-tools -->
                </div><!-- /.box-header -->
                <div class="box-body">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td colspan="4">เลขที่ทะเบียนเอกสาร :&nbsp;<b><?= $result[0]['bo_no']?></b></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">วันที่ขอยืม :&nbsp;<b><?= DateThai2($result[0]['bo_date'])?></b></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">ผู้ขอเบิก :&nbsp;<b><?= $result[0]['fullname']?></b></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">หน่วยงาน :&nbsp;<b><?= $result[0]['depName']?></b></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">สถานะการยืม :&nbsp;<b><?php if($result[0]['bo_status']=='Y' or $result[0]['bo_status']=='A'){
                                        echo 'จ่ายแล้ว';}  else {echo 'ยังไม่จ่าย';} ?></b></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">รายการที่ขอยืม : </td>
                                    </tr>
                                    <?php $c=1; for($i=0;$i<count($result);$i++){ ?>
                                    <tr>
                                        <td><b><?= $c?>.</b></td>
                                        <td><b><?= $result[$i]['mate_name']?></b></td>
                                        <td>จำนวน <b><?= $result[$i]['amount']?></b> <?= $result[$i]['mate_unit']?></td>
                                        <td><b>&nbsp;</b></td>
                                    </tr>
                                    <?php $c++; }?>
                                    <?php if(!empty($result[0]['payer'])){ ?>
                                    <tr>
                                        <td><b>&nbsp;</b></td>
                                    </tr>
                                        <td colspan="4">วันที่จ่าย :&nbsp;<b><?= DateThai2($result[0]['pbo_date'])?></b></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">ผู้จ่าย :&nbsp;<b><?= $result[0]['payer']?></b></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">ผู้รับวัสดุ :&nbsp;<b><?= $result[0]['receiver']?></b></td>
                                    </tr>
                                   <tr>
                                        <td colspan="4">รายการที่จ่าย : </td>
                                    </tr>
                                    <?php $c=1; for($i=0;$i<count($result);$i++){ ?>
                                    <tr>
                                        <td><b><?= $c?>.</b></td>
                                        <td><b><?= $result[$i]['mate_name']?></b></td>
                                        <td>จำนวน <b><?= $result[$i]['pbo_amount']?></b> <?= $result[$i]['mate_unit']?></td>
                                    </tr>
                                    <?php $c++; }}?>
                                </table>
                            </div>
                        </div>

              
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="control-sidebar-bg"></div>
        <!-- jQuery 2.1.4 -->
     <div class="control-sidebar-bg"></div>
    </div><!-- ./wrapper -->
        <!-- jQuery 2.1.4 -->
    <script src="../plugins/jQuery/jQuery-2.1.4.min.js"></script>
        <!-- jQuery UI 1.11.4 -->
    <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
      $.widget.bridge('uibutton', $.ui.button);
    </script>
    <!-- Bootstrap 3.3.5 -->
    <script src="../bootstrap/js/bootstrap.min.js"></script>
    <!-- DataTables -->
    <script src="../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../plugins/datatables/dataTables.bootstrap.min.js"></script>
    <!-- page script -->
    <script>
      $(function () {
        $("#example1").DataTable();
        $('#example2').DataTable({
          "paging": true,
          "lengthChange": false,
          "searching": false,
          "ordering": true,
          "info": true,
          "autoWidth": false
        });
      });
    </script>
    <!-- Morris.js charts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="../plugins/morris/morris.min.js"></script>
    <!-- Sparkline -->
    <script src="../plugins/sparkline/jquery.sparkline.min.js"></script>
    <!-- jvectormap -->
    <script src="../plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
    <script src="../plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <!-- jQuery Knob Chart -->
    <script src="../plugins/knob/jquery.knob.js"></script>
    <!-- daterangepicker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
    <script src="../plugins/daterangepicker/daterangepicker.js"></script>
    <!-- datepicker -->
    <script src="../plugins/datepicker/bootstrap-datepicker.js"></script>
    <!-- Bootstrap WYSIHTML5 -->
    <script src="../plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
    <!-- Slimscroll -->
    <script src="../plugins/slimScroll/jquery.slimscroll.min.js"></script>
    <!-- FastClick -->
    <script src="../plugins/fastclick/fastclick.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/app.min.js"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="../dist/js/pages/dashboard.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../dist/js/demo.js"></script>

  </body>
</html>
