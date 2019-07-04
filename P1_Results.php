<?php
   echo "<center><h1>Round Robin Scheduling</h1></center>";
   $conn = new mysqli('localhost','root','','oppertunity2') or die(mysql_error($conn));
   
   $sql_pname="SELECT pname FROM processes";
   $p_name = array();
   
   if ($result=mysqli_query($conn,$sql_pname))
   {
      while ($row=mysqli_fetch_row($result))
      {
         array_push($p_name, $row[0]); 
      }
      mysqli_free_result($result);
   }
   
   $sql_at="SELECT at FROM processes";
   $p_at = array();
   
   if ($result=mysqli_query($conn,$sql_at))
   {
      while ($row=mysqli_fetch_row($result))
      {
         array_push($p_at, $row[0]); 
      }
      mysqli_free_result($result);
   }
   
   $sql_bt="SELECT bt FROM processes";
   $p_bt = array();
   $p_bt_temp = array();

   if ($result=mysqli_query($conn,$sql_bt))
   {
      while ($row=mysqli_fetch_row($result))
      {
         array_push($p_bt, $row[0]);
         array_push($p_bt_temp, $row[0]);
      }
      mysqli_free_result($result);
   }
   
   $p_tt = array();
   $p_wt = array();
   $p_rt = array();
   
   $wait_start = array();
   $wait_end = array();
   $reacted = array();
   
   $ready = array();
   $started = array();
   
   $first = 0;
   $last = 0;
   $time = 0;
   
   $sum_tt = 0;
   $sum_wt = 0;
   $sum_rt = 0;
   
   if(!isset($_POST['tq']))
   {
      $tq = 1;
   }
   else
   {
      $tq = $_POST['tq'];
   }
   
   $tq_run = 0;
   $start = 0;
   
   
   for($x = 0; $x <= count($p_name); $x++)
   {
      array_push($ready, -1);
   }
   
   for($x = 0; $x < count($p_name); $x++)
   {
      array_push($started, 0);
      
      array_push($p_tt, 0);
      array_push($p_wt, 0);
      
      array_push($wait_end, 0);
      array_push($wait_start, 0);
      array_push($reacted, 0);
      
      array_push($p_rt, 0);
   }
   
   do
   {
      for($x = 0; $x < count($p_name); $x++)
      {
         if(($time == $p_at[$x])&&($started[$x] == 0))
         {
            $started[$x] = 1;
            $wait_start[$x] = 1;
            $start++;
            $ready[$last] = $x;
            $last = ($last + 1)%count($ready);
         }
         
         if(($wait_start[$x] == 1)&&($reacted[$x] == 0))
         {
            if($ready[$first] == $x)
            {
               $reacted[$x] = 1;
            }
            else
            {
               $p_rt[$x]++;
            }
         }
         
         
         if(($wait_start[$x] == 1)&&($wait_end[$x] == 0)&&($ready[$first] != $x))
         {
            if($p_bt_temp[$x] > 0)
            {
               $p_wt[$x]++;
            }
            else
            {
               $wait_end[$x] = 1;
            }
         }
      }
      
      if($ready[$first] == -1)
      {
         echo "<pre>".$time."\t". "No Process Running" . "</pre>";
      }
      else
      {
         echo "<pre>".$time."\t".$p_name[$ready[$first]] . "</pre>";
         
         if($p_bt_temp[$ready[$first]]>0)
         {
            $p_bt_temp[$ready[$first]]--;
         }  
      }
      
      $time++;
      $tq_run++;
      
      
      
      if($tq == $tq_run)
      {
         $tq_run = 0;
         
         if($p_bt_temp[$ready[$first]] < 1)
         {

               $ready[$first] = -1;
               $first = ($first + 1) % count($ready);
         }
         else
         {
            $ready[$last] = $ready[$first];
            $ready[$first] = -1;
            $last = ($last + 1)%count($ready);
            $first = ($first + 1) % count($ready);
         }
      }
      
      $end = 0;
      for($x = 0; $x< count($ready); $x++)
      {
         
         if(($ready[$x] == -1)&&($start == count($p_name)))
         {
            $end++;
         }
      }
   }
   while($end < (count($ready)));

   for($x = 0; $x < count($p_name); $x++)
   {
      $y = $x + 1;
      
      $p_tt[$x] = $p_wt[$x] + $p_bt[$x];
      
      $sum_tt = $sum_tt + $p_tt[$x];
      $sum_wt = $sum_wt + $p_wt[$x];
      $sum_rt = $sum_rt + $p_rt[$x];
      
      $sql_tt = "UPDATE processes SET tt=$p_tt[$x] WHERE pro_id=$y";
      $sql_wt = "UPDATE processes SET wt=$p_wt[$x] WHERE pro_id=$y";
      $sql_rt = "UPDATE processes SET rt=$p_rt[$x] WHERE pro_id=$y";
      
      mysqli_query($conn,$sql_tt);
      mysqli_query($conn,$sql_wt);
      mysqli_query($conn,$sql_rt);
   }
   
   $avg_tt = $sum_tt / count($p_name);
   $avg_wt = $sum_wt / count($p_name);
   $avg_rt = $sum_rt / count($p_name);
   
   echo "Average wait time: " . $avg_wt . "<br>";
   echo "Average turn around time: " . $avg_tt . "<br>";
   echo "Average reaction time: " . $avg_rt . "<br>";
?>
