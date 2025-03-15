<?php
$con=mysqli_connect("localhost","root","","myhmsdb");

session_start();




  if (isset($_GET['file_id'])) {
  $id = $_GET['file_id'];

  // fetch file to download from database
  $sql = "SELECT * FROM lab WHERE id=$id";
  $result = mysqli_query($con, $sql);

  $file = mysqli_fetch_assoc($result);
  $filepath = 'uploads/' . $file['reportfile'];

  if (file_exists($filepath)) {
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename=' . basename($filepath));
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize('uploads/' . $file['reportfile']));
      
      //This part of code prevents files from being corrupted after download
      ob_clean();
      flush();
      
      readfile('uploads/' . $file['reportfile']);

      // Now update downloads count
      // $newCount = $file['downloads'] + 1;
      // $updateQuery = "UPDATE files SET downloads=$newCount WHERE id=$id";
      // mysqli_query($conn, $updateQuery);
      exit;
  }

}





if(isset($_POST['search_submit']))
{

$stamp = date('Y-m-d H:i:s',strtotime('+5 minutes'));
$id = $_POST['id'];
$userinfo = $_SESSION['companyId'];

$sql_query = "UPDATE files SET `token`='$jwt', `timing`='$stamp' WHERE id = $id  AND userinfo = $userinfo "; 

if (mysqli_query($conn, $sql_query)) {   

?>
<script type="text/javascript">
alert('Data Are Updated Successfully');
window.location.href='UploadView.php';
</script>
<?php
} 
else {
    echo "Error: " . $sql_query . "" . mysqli_error($conn);
}


}



?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/css/bootstrap.css">
  <script src="assets/jquery-3.3.1.slim.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>

  
<style type="text/css">
body {
font-family: 'Open Sans', sans-serif;
line-height:28px;

}

.menu-section {
background-color: #f7f7f7;
border-bottom: 5px solid #9170E4;
width: 100%;
}




.row{
margin-top:40px;
padding: 0 10px;
}
.clickable{
cursor: pointer;   
}

.panel-heading div {
margin-top: -18px;
font-size: 15px;
}
.panel-heading div span{
margin-left:5px;
}
.panel-body{
display: none;
}

</style> 
<script type="text/javascript">
(function(){
    'use strict';
    var $ = jQuery;
    $.fn.extend({
        filterTable: function(){
            return this.each(function(){
                $(this).on('keyup', function(e){
                    $('.filterTable_no_results').remove();
                    var $this = $(this), 
                        search = $this.val().toLowerCase(), 
                        target = $this.attr('data-filters'), 
                        $target = $(target), 
                        $rows = $target.find('tbody tr');
                        
                    if(search == '') {
                        $rows.show(); 
                    } else {
                        $rows.each(function(){
                            var $this = $(this);
                            $this.text().toLowerCase().indexOf(search) === -1 ? $this.hide() : $this.show();
                        })
                        if($target.find('tbody tr:visible').size() === 0) {
                            var col_count = $target.find('tr').first().find('td').size();
                            var no_results = $('<tr class="filterTable_no_results"><td colspan="'+col_count+'">No results found</td></tr>')
                            $target.find('tbody').append(no_results);
                        }
                    }
                });
            });
        }
    });
    $('[data-action="filter"]').filterTable();
})(jQuery);


$(function(){
    // attach table filter plugin to inputs
    $('[data-action="filter"]').filterTable();
    
    $('.container').on('click', '.panel-heading span.filter', function(e){
        var $this = $(this), 
            $panel = $this.parents('.panel');
        
        $panel.find('.panel-body').slideToggle();
        if($this.css('display') != 'none') {
            $panel.find('.panel-body input').focus();
        }
    });
    $('[data-toggle="tooltip"]').tooltip();
})


</script>
  
</head>
<body>
<?php
 include 'header.php'; 
 ?>


<div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Shared  files</h3>
                        <div class="pull-right">
                            <span class="clickable filter" data-toggle="tooltip" title="Toggle table filter" data-container="body">
                                <i class="glyphicon glyphicon-filter"></i>
                            </span>
                        </div>
                    </div>
                    <div class="panel-body">
                        <input type="text" class="form-control" id="dev-table-filter" data-action="filter" data-filters="#dev-table" placeholder="Filter Developers" />
                    </div>
                    <div class="table-responsive">
                    <table class="table table-hover" id="dev-table">
                        <thead>
                            <tr>
                            <th>ID</th>
                            <th>Filename</th>
                            <th>size (in mb)</th>
                            <th>Downloads</th>
                            <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            
                            $sql = "SELECT * FROM files";
                            $result = $conn->query($sql);
                            $result = mysqli_query($conn, $sql);
                            $i = 1;
                            while($row = $result->fetch_assoc()) { ?>


                                    <tr>

                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo $row['name']; ?></td>
                                        <td><?php echo floor($row['size'] / 1000) . ' KB'; ?></td>
                                        <td><?php echo $row['downloads']; ?></td>
                                        <td>

                                                                                <!-- Button trigger modal -->
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal<?php echo $row['id'] ?>">
                                            Download File
                                        </button>

                                        <!-- Modal -->
                                        <div class="modal fade" id="exampleModal<?php echo $row['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="" method = "post" role="form">
                                                <input type="hidden" name="fileid" value="<?php echo $row['id'] ?>">
                                                <div class="form-group">
                                                <label>File Token</label>
                                                <input class="form-control" type="text" name="token" required />
                                                </div>
                                                </div>
                                                </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <input type="submit" name="checktoken" value="Check Token" class="btn btn-info">
                                            </form>
                                            </div>
                                            </div>
                                        </div>
                                        </div>




                                        </td>
                                    </tr>
                                    <?php

                                }
                            
                            ?>
                            
                        </tbody>
                    </table>
                </div>
                </div>
            </div>
        </div>
    </div>


</body>
</html>
