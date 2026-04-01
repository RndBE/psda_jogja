<html>
<head>
   <title>Data V-NOTCH</title>
</head>
<body>
<h1>Data Input V-NOTCH</h1> <?php echo 'ID LOGGER '.$this->session->userdata('log_id').' ##### |'.$this->session->userdata('tgl_awlr').'|'; ?>

<hr/>



<?php echo form_open('datamasuk/sesi_loggervnotch');?>
Id Logger <input type="text" name="logger_id" />
<input name="btnlog" value="Cari" type="submit"/>
<?php echo form_close();?>
	<?php 
	//echo form_open('datamasuk/tgl_awlr');?>
<!--	<label for="tgl">Tanggal:</label>
	<input type="date" id="tgl" name="tgl">
	<input name="btntgl" value="Cari" type="submit"/>
-->
	<?php //echo form_close();?>
<?php echo form_open('datamasuk/data_vnotch');?>
<input value="Refresh" name="btnrefresh" type="submit"/>
<?php echo form_close();?>
<hr/>
	
	<table border="1" cellspacing="1" cellpadding="1"> 
	<tr>
	<td>&nbsp;Id Logger&nbsp;</td>
         <td>&nbsp;Waktu&nbsp;</td>
         <td>&nbsp;Sensor1&nbsp;</td>
         <td>&nbsp;Sensor2&nbsp;</td>
         <td>&nbsp;Sensor3&nbsp;</td>
         <td>&nbsp;Sensor4&nbsp;</td>
         <td>&nbsp;Sensor5&nbsp;</td>
         <td>&nbsp;Sensor6&nbsp;</td>
         <td>&nbsp;Sensor7&nbsp;</td>
         <td>&nbsp;Sensor8&nbsp;</td>
         <td>&nbsp;Sensor9&nbsp;</td>
         <td>&nbsp;Sensor10&nbsp;</td>
         <td>&nbsp;Sensor11&nbsp;</td>
         <td>&nbsp;Sensor12&nbsp;</td> 
         <td>&nbsp;Sensor13&nbsp;</td>
         <td>&nbsp;Sensor14&nbsp;</td>
         <td>&nbsp;Sensor15&nbsp;</td>
         <td>&nbsp;Sensor16&nbsp;</td>

         

       </tr>
<?php      
   foreach($data_vnotch->result() as $row){
      ?>
     <tr>
         <td>&nbsp;<?php echo $row->code_logger ?>&nbsp;</td>
         <td>&nbsp;<?php echo $row->waktu ?>&nbsp;</td>
         <td>&nbsp;<?php echo $row->sensor1 ?>&nbsp;</td>
         <td>&nbsp;<?php echo $row->sensor2 ?>&nbsp;</td>
         <td>&nbsp;<?php echo $row->sensor3 ?>&nbsp;</td>
         <td>&nbsp;<?php echo $row->sensor4 ?>&nbsp;</td>
         <td>&nbsp;<?php echo $row->sensor5 ?>&nbsp;</td>
         <td>&nbsp;<?php echo $row->sensor6 ?>&nbsp;</td>
         <td>&nbsp;<?php echo $row->sensor7 ?>&nbsp;</td>
         <td>&nbsp;<?php echo $row->sensor8 ?>&nbsp;</td>
         <td>&nbsp;<?php echo $row->sensor9 ?>&nbsp;</td>
         <td>&nbsp;<?php echo $row->sensor10 ?>&nbsp;</td>
         <td>&nbsp;<?php echo $row->sensor11 ?>&nbsp;</td>
         <td>&nbsp;<?php echo $row->sensor12 ?>&nbsp;</td> 
         <td>&nbsp;<?php echo $row->sensor13 ?>&nbsp;</td>
         <td>&nbsp;<?php echo $row->sensor14 ?>&nbsp;</td>
         <td>&nbsp;<?php echo $row->sensor15 ?>&nbsp;</td>
         <td>&nbsp;<?php echo $row->sensor16 ?>&nbsp;</td>

        
       </tr>

 <?php    
	   }
?>
</table>
</body>
</html>