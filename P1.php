<!DOCTYPE html>

<?php
   
   $conn = new mysqli('localhost','root','','oppertunity2') or die(mysql_error($conn));
   
   if(isset($_POST['pname']) && isset($_POST['submit']))
   {
      if($_POST['pname'] != "")
      {
         $pname = $_POST['pname'];
         $at = $_POST['at'];
         $bt = $_POST['bt'];
         $zero = 0;
         
         $sql = "INSERT INTO processes ". "(pro_id,pname, at, 
                        bt, tt, wt, rt) ". "VALUES('null','$pname','$at', '$bt', '$zero', '$zero', '$zero')";
          
         mysqli_query($conn, $sql);
      }
   }
   else
   {
      if(!isset($_POST['roundRobin']))
      {
         $drop = 'DROP TABLE processes';
         $droptbl = mysqli_query($conn, $drop ) or die(mysqli_error($conn));
   
         $create = 'CREATE TABLE processes'.'(pro_id SMALLINT UNSIGNED NOT NULL auto_increment, pname VARCHAR(15), 
                        at SMALLINT, bt SMALLINT, tt SMALLINT, wt SMALLINT, rt SMALLINT, CONSTRAINT pk_prosesses PRIMARY KEY (pro_id))';
                        
         $c_tbl = mysqli_query($conn, $create) or die(mysqli_error($conn));
      }
   }
   
   if(isset($_POST['roundRobin']))
   {
      $sql="SELECT pname, at, bt FROM processes";
      
      $result = mysqli_query($conn, $sql);
      
      if(mysqli_num_rows($result) == 0)
      {
         echo "Empty Table";
      }
      else
      {
         header("Location: P1_Round_Robin.php");
      }
   }

?>

<html>
   <body>
      <center>      
         <h1>Round Robin Scheduling</h1>
         <br>
         
         <form method="post">
         
            Process Name
            <br>
            <input  type="text" name="pname" id="pname" placeholder="Example: P1"/><br><br>
      
            Arival Time
            <br>
            <input  type="number" name="at" id="at" value=0 min=0 /><br><br>
      
            Burst Time
            <br>
            <input  type="number" name="bt" id="bt" value=1 min=1 /><br><br>
            
            <input type="submit" name="submit" value="Save Process" />
            <input type="submit" name="roundRobin" value="Next" />
            
         </form>         
      </center>
   </body>
</html> 