<?php 
ini_set("allow_url_fopen", 1);
date_default_timezone_set("Asia/Calcutta");
// Set access header
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Origin, HTTP_X_REQUESTED_WITH, Content-Type, Accept, Authorization");

use \Datetime;
$servername = "localhost";
$username = "newgeetamenswear_newgeetamenswea";
$password = "shaikh@25";
$db= "newgeetamenswear_emp_attendance";


ini_set("display_errors", 1);
ini_set("track_errors", 1);
ini_set("html_errors", 1);
ini_set('memory_limit', '256M');
ini_set('max_execution_time', '30');
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli($servername, $username, $password, $db);

//$jb_conn = new mysqli($servername, $username, $password, $db_jb);

if ($conn->connect_error) {
    die("connection failed" . $conn->connect_error);
}
// if ($jb_conn->connect_error) {
//     die("connection failed" . $jb_conn->connect_error);
// }
    $params = (array) json_decode(file_get_contents('php://input'), TRUE);
	$response = array();
	if(isset($_GET['apicall'])){
	
    	switch($_GET['apicall']){
            case 'quary':{
    		    $q=$_POST['q'];
    		    $stmt = $conn->prepare($q);
				if($stmt->execute()){
				    echo 'done';
				}else{
				    echo 'failed';
				}
    		}
    		break;
    		case 'login':{
    		    $phone='';
    		    $password='';
    		    if (isset($params['phone'])) {
                    $phone=$params['phone'];
                }else{
                    $phone=$_POST['phone'];
                }
                if (isset($params['password'])) {
                    $password=$params['password'];
                }else{
                    $password=$_POST['password'];
                }
		        $data=array();
		        $t=1;
    		    $stmt = $conn->prepare("SELECT id, name, phone, pass, email, dob, location,lunch, cut_off, type, salary_type, salary, hours, buffer, holiday, uid, verify, finger, status FROM user_master WHERE phone='$phone' LIMIT 1");
				$stmt->execute();
				$stmt->store_result();
				
				//if the user already exist in the database 
				if($stmt->num_rows > 0){
				    
				    $stmt->bind_result($id, $name, $phone, $pass, $email, $dob, $location,$lunch, $cut_off, $type, $salary_type, $salary, $hours, $buffer, $holiday, $uid, $verify, $finger, $status);
					$stmt->fetch();
					if ($password===$pass) {
					    
						$data = array(
                            'id' => $id,
                            'name' => $name,
                            'phone' => $phone,
                            'pass' => $pass,
                            'email' => $email,
                            'dob' => $dob,
                            'location' => $location,
                            'lunch' => $lunch,
                            'cut_off' => $cut_off,
                            'type' => $type,
                            'salary_type' => $salary_type,
                            'salary' => $salary,
                            'hours' => $hours,
                            'buffer' => $buffer,
                            'holiday' => $holiday,
                            'uid' => $uid,
                            'verify' => $verify,
                            'finger' => $finger,
                            'status' => $status
                            );
                            
                        if($status==1){
    					    $response['error'] = false;
    						$response['status'] = 1;
    						$response['data']=$data;
    						$response['message'] = 'login success';
                            
                        }else{
                            $response['error'] = false;
    						$response['status'] = 2;
    						$response['data']=[];
    						$response['message'] = 'login success but account is suspended contact to admin!';
                        }

    				}else{
        				$response['error'] = false;
        				$response['status'] = 2;
        				$response['data']=null;
        				$response['message'] = 'wrong password';
    				}
    			}else{
    			    $response['error'] = false;
    				$response['status'] = 3;
    				$response['data']=null;
    				$response['message'] = 'user not found';
    			}
    		    
    		}
    		break;
    		case 'users':{
    		    $fn='';
    		    if (isset($params['fn'])) {
                    $fn=$params['fn'];
                    
                }else{
                    $fn=$_POST['fn'];
                    $params=$_POST;
                }
                
                //SELECT id, name, phone, pass, email, dob, location, face, finger, type, salary, hours, buffer,status FROM user_master
                
                if($fn==='add_up'){
    		       $us_id=$params['us_id'];
    		       $id=$params['id'];
    		       $name=$params['name'];
    		       $phone=$params['phone'];
    		       $pass=$params['pass'];
    		       $email=$params['email'];
    		       $dob=$params['dob'];
    		       $location=$params['location'];
    		       $lunch=$params['lunch'];
    		       $cut_off=$params['cut_off'];
    		       $type=$params['type'];
    		       $salary_type=$params['salary_type'];
    		       $salary=$params['salary'];
    		       $hours=$params['hours'];
    		       $buffer=$params['buffer'];
    		       $holiday=$params['holiday'];
    		       $uid=$params['uid'];
    		       $verify=$params['verify'];
    		       $status=$params['status'];
    		       $date=$params['date'];
    		       $file=$params['isfile'];
    		        
        		   if($id!=='0'){
            			    
        			    $stmt = $conn->prepare("UPDATE user_master SET name='$name', phone='$phone', pass='$pass', email='$email', dob='$dob', location='$location',lunch=$lunch,cut_off=$cut_off, type=$type, salary_type=$salary_type,  salary='$salary', hours='$hours', buffer='$buffer', holiday=$holiday, uid='$uid', verify=$verify,status=$status, up_id=$us_id, up_date='$date' WHERE id=$id");

        				if($stmt->execute()){
        				    if($file==='1'){
        				        $dir="uploads/profile/";
                                $file = $_FILES['file']['name'];
                                saveFilePic($file,$dir,$id);
        				    }
        				    
        				   
            			    $response['error'] = false;
        					$response['status'] = 2;
        					$response['message'] = 'user details has been updated';
        				    
        				}else{
        				    
            			    $response['error'] = false;
        					$response['status'] = 20;
        					$response['message'] = 'some error occurred';
        				}
        			    
        		    }else{
        		        $stmt = $conn->prepare("SELECT id FROM user_master WHERE phone='$phone' LIMIT 1");
        				$stmt->execute();
        				$stmt->store_result();
        				
        				//if the user already exist in the database 
        				if($stmt->num_rows > 0){
        				    $stmt->bind_result($id);
					        $stmt->fetch();
					        $response['error'] = false;
        				    $response['status'] = 3;
        				    $response['message'] = 'user details already exist!';
					       
        				}else{
            			    $stmt = $conn->prepare("INSERT INTO user_master(name, phone, pass, email, dob, location, lunch, cut_off, type, salary_type,salary, hours, buffer, holiday, uid, verify,status,cr_id,cr_date,up_id,up_date) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            				$stmt->bind_param("sssssssssssssssssssss", $name, $phone, $pass, $email, $dob, $location,$lunch, $cut_off, $type, $salary_type,$salary, $hours, $buffer, $holiday, $uid,$verify,$status,$us_id,$date,$us_id,$date);
            				
            				if($stmt->execute()){
            				    
            				    $id = $stmt->insert_id;
            				    
            		        	if($file==='1'){
            				        $dir="uploads/profile/";
                                    $file = $_FILES['file']['name'];
                                    saveFilePic($file,$dir,$id);
            				    }
            				    
                			    $response['error'] = false;
            					$response['id'] = $id;
            					$response['status'] = 1;
            					$response['message'] = 'new user has been added';
            				    
            				}else{
            				    
                			    $response['error'] = false;
            					$response['status'] = 10;
            					$response['message'] = 'some error occurred';
            				}
        				}
            			    
            		}
    		    }
    		    if($fn==='update_finger'){
    		        $id=$params['id'];
    		        $us_id=$params['us_id'];
    		        $date=$params['date'];
    		        $stmt = $conn->prepare("UPDATE user_master SET finger=$id, up_id=$us_id, up_date='$date' WHERE id=$id");

    				if($stmt->execute()){
    				    
        			    $response['error'] = false;
    					$response['status'] = 1;
    					$response['message'] = 'user finger details has been updated';
    				    
    				}else{
    				    
        			    $response['error'] = false;
    					$response['status'] = 20;
    					$response['message'] = 'some error occurred';
    				}
    		    }
    		    if($fn==='get_users'){
    		        $data=array();
            
        		    $stmt = $conn->prepare("SELECT id, name, phone, pass, email, dob, location, lunch, cut_off, type, salary_type, salary, hours, buffer, holiday, uid, verify, finger, status FROM user_master ORDER BY id ASC");
        
            		$stmt->execute();
            		$stmt->store_result();
				    $stmt->bind_result($id, $name, $phone, $pass, $email, $dob, $location,$lunch, $cut_off, $type, $salary_type, $salary, $hours, $buffer, $holiday, $uid, $verify, $finger, $status);
             
            		while ($stmt->fetch()) {
            		    
						$att = array(
                            'id' => $id,
                            'name' => $name,
                            'phone' => $phone,
                            'pass' => $pass,
                            'email' => $email,
                            'dob' => $dob,
                            'location' => $location,
                            'lunch' => $lunch,
                            'cut_off' => $cut_off,
                            'type' => $type,
                            'salary_type' => $salary_type,
                            'salary' => $salary,
                            'hours' => $hours,
                            'buffer' => $buffer,
                            'holiday' => $holiday,
                            'uid' => $uid,
                            'verify' => $verify,
                            'finger' => $finger,
                            'status' => $status
                            );
            		    array_push($data,$att);
            		}
            		if(empty($data)){
            		    $response['error'] = false;
    					$response['status'] = 2;
    					$response['data']=$data;
    					$response['message'] = 'users details not list found';
            		}else{
                		$response['error'] = false;
    					$response['status'] = 1;
    					$response['data']=$data;
    					$response['message'] = 'users details list';
            		}

    		        
    		    }
    		    if($fn==='get_users_id'){
    		        $us_id=$params['us_id'];
    		        $data=array();
            
        		    $stmt = $conn->prepare("SELECT id, name, phone, pass, email, dob, location,lunch, cut_off, type, salary_type, salary, hours, buffer, holiday, uid, verify, finger, status FROM user_master WHERE id=$us_id LIMIT 1");
    				$stmt->execute();
    				$stmt->store_result();
    				
    				//if the user already exist in the database 
    				if($stmt->num_rows > 0){
    				    
    				    $stmt->bind_result($id, $name, $phone, $pass, $email, $dob, $location,$lunch, $cut_off, $type, $salary_type, $salary, $hours, $buffer, $holiday, $uid, $verify, $finger, $status);
    					$stmt->fetch();
    					
    					$data = array(
                            'id' => $id,
                            'name' => $name,
                            'phone' => $phone,
                            'pass' => $pass,
                            'email' => $email,
                            'dob' => $dob,
                            'location' => $location,
                            'lunch' => $lunch,
                            'cut_off' => $cut_off,
                            'type' => $type,
                            'salary_type' => $salary_type,
                            'salary' => $salary,
                            'hours' => $hours,
                            'buffer' => $buffer,
                            'holiday' => $holiday,
                            'uid' => $uid,
                            'verify' => $verify,
                            'finger' => $finger,
                            'status' => $status
                            );
                            
                            $response['error'] = false;
    						$response['status'] = 1;
    						$response['data']=$data;
    						$response['message'] = $name.' details found';
        			}else{
        			    $response['error'] = false;
        				$response['status'] = 10;
        				$response['data']=null;
        				$response['message'] = 'user not found';
        			}

    		        
    		    }
    		}
    		break;
    		
    		case 'master_data':{
    		    $data=array();
            
    		    $stmt = $conn->prepare("SELECT id, name, phone, pass, email, dob, location, lunch, cut_off, type, salary_type, salary, hours, buffer, holiday, uid, verify, finger, status FROM user_master GROUP BY name ORDER BY id ASC");
    
        		$stmt->execute();
        		$stmt->store_result();
			    $stmt->bind_result($id, $name, $phone, $pass, $email, $dob, $location,$lunch, $cut_off, $type, $salary_type, $salary, $hours, $buffer, $holiday, $uid, $verify, $finger, $status);
         
        		while ($stmt->fetch()) {
        		    
					$att = array(
                        'id' => $id,
                        'name' => $name,
                        'phone' => $phone,
                        'pass' => $pass,
                        'email' => $email,
                        'dob' => $dob,
                        'location' => $location,
                        'lunch' => $lunch,
                        'cut_off' => $cut_off,
                        'type' => $type,
                        'salary_type' => $salary_type,
                        'salary' => $salary,
                        'hours' => $hours,
                        'buffer' => $buffer,
                        'holiday' => $holiday,
                        'uid' => $uid,
                        'verify' => $verify,
                        'finger' => $finger,
                        'status' => $status
                        );
        		    array_push($data,$att);
        		}
        		if(empty($data)){
        		    $response['error'] = false;
					$response['status'] = 2;
					$response['user']=[];
					$response['message'] = 'users details not list found';
        		}else{
            		$response['error'] = false;
					$response['status'] = 1;
					$response['user']=$data;
					$response['message'] = 'users details list';
        		}
    		}
    		break;
    		case 'attendance':{
    		      $fn='';
        		  if (isset($params['fn'])) {
                      $fn=$params['fn'];
                  }else{
                      $fn=$_POST['fn'];
                      $params=$_POST;
                  }
                  
                  if($fn==='chk'){
                      $id=$params['id'];
                      $date=$params['date'];
                      $c=chkAtt($conn,$id,$date);
                      $d=getAttData($conn,$id,$date);
                      if($c===0){
                        $response['error'] = false;
    					$response['status'] = 0;
    					$response['data'] = $d;
    					$response['message'] = 'CHECK-IN';
                      }elseif($c===1){
                        $response['error'] = false;
    					$response['status'] = 1;
    					$response['data'] = $d;
    					$response['message'] = 'CHECK-OUT';
                      }elseif($c===2){
                        $response['error'] = false;
    					$response['status'] = 2;
    					$response['data'] = $d;
    					$response['message'] = 'CHECK-DONE';
                      }else{
                        $response['error'] = false;
    					$response['status'] = 3;
    					$response['data'] = $d;
    					$response['message'] = 'Try Again';
                      }
                  }
                  
                  
                  if($fn==='check_in_out_finger' || $fn==='check_in_out_face_finger'){
                      $id=$params['id'];
                      $date=$params['date'];
                      $time=$params['time'];
                      $chk=chk($conn,$id,$date,$time);
                      $opt=isset($params['opt'])?$params['opt']:1;
                      
                      //Check_IN
                      if($chk['c']===0){
                        $stmt = $conn->prepare("INSERT INTO attendance(emp_id, src, in_time, date) VALUES (?,?,?,?)");
        				$stmt->bind_param("ssss", $id, $opt,  $time, $date);
            				
        				if($stmt->execute()){
                            $response['error'] = false;
        					$response['status'] = 0;
        					$response['data'] = getEmpDailyAttendance($conn,$id,$date);
        					$response['message'] = 'DONE CHECK-IN';
        				}else{
        				    $response['error'] = false;
        					$response['status'] = 10;
        					$response['data'] = [];
        					$response['message'] = 'unabel to process request!';
        				}
                      }
                      
                      if($chk['c']===1){
                            //Check_IN_DONE
                            if($chk['v']===false){
                                $response['error'] = false;
            					$response['status'] = 1;
            					$response['data'] = getEmpDailyAttendance($conn,$id,$date);
            					$response['message'] = 'DONE CHECK-IN IN LAST 5 MINUTES';
                            }else{
                                $t=getAMP($time);
                            //Check_OUT_LUNCH
                                if($t==3){
                                    $stmt = $conn->prepare("UPDATE attendance SET l_out='$time', l_in='$time' , out_time='$time' WHERE emp_id=$id AND date='$date'");

                                    if($stmt->execute()){
                                        $response['error'] = false;
                                        $response['status'] = 4;
                                        $response['data'] = getEmpDailyAttendance($conn,$id,$date);
                                        $response['message'] = 'DONE CHECK-OUT';
                                    }else{
                                        $response['error'] = false;
                                        $response['status'] = 10;
                                        $response['data'] = [];
                                        $response['message'] = 'unabel to process request!';
                                    }

                                }else{
                                    $stmt = $conn->prepare("UPDATE attendance SET l_out='$time' WHERE emp_id=$id AND date='$date'");

                                    if($stmt->execute()){
                                        $response['error'] = false;
                                        $response['status'] = 2;
                                        $response['data'] = getEmpDailyAttendance($conn,$id,$date);
                                        $response['message'] = 'DONE CHECK-OUT_LUNCH';
                                    }else{
                                        $response['error'] = false;
                                        $response['status'] = 10;
                                        $response['data'] = [];
                                        $response['message'] = 'unabel to process request!';
                                    }
                                }
                            }
                      }
                      if($chk['c']===2){
                            //Check_OUT_LUNCH_DONE
                            if($chk['v']===false){
                                $response['error'] = false;
            					$response['status'] = 2;
            					$response['data'] = getEmpDailyAttendance($conn,$id,$date);
            					$response['message'] = 'DONE CHECK OUT LUNCH IN LAST 5 MINUTES';
                            }else{
                                $t=getAMP($time);
                                if($t==3){
                                    $stmt = $conn->prepare("UPDATE attendance SET l_in='$time' , out_time='$time' WHERE emp_id=$id AND date='$date'");

                                    if($stmt->execute()){
                                        $response['error'] = false;
                                        $response['status'] = 4;
                                        $response['data'] = getEmpDailyAttendance($conn,$id,$date);
                                        $response['message'] = 'DONE CHECK-OUT';
                                    }else{
                                        $response['error'] = false;
                                        $response['status'] = 10;
                                        $response['data'] = [];
                                        $response['message'] = 'unabel to process request!';
                                    }
                                }else{
                                //Check_IN_LUNCH
                                    $stmt = $conn->prepare("UPDATE attendance SET l_in='$time' WHERE emp_id=$id AND date='$date'");

                                    if($stmt->execute()){
                                        $response['error'] = false;
                                        $response['status'] = 3;
                                        $response['data'] = getEmpDailyAttendance($conn,$id,$date);
                                        $response['message'] = 'DONE CHECK-IN_LUNCH';
                                    }else{
                                        $response['error'] = false;
                                        $response['status'] = 10;
                                        $response['data'] = [];
                                        $response['message'] = 'unabel to process request!';
                                    }
                                }

                            }
                      }
                      if($chk['c']===3){
                            //Check_IN_LUNCH_DONE
                            if($chk['v']===false){
                                $response['error'] = false;
            					$response['status'] = 3;
            					$response['data'] = getEmpDailyAttendance($conn,$id,$date);
            					$response['message'] = 'DONE CHECK IN LUNCH IN LAST 5 MINUTES';
                            }else{
                            //Check_OUT
                                $stmt = $conn->prepare("UPDATE attendance SET out_time='$time' WHERE emp_id=$id AND date='$date'");

                				if($stmt->execute()){
                                    $response['error'] = false;
                					$response['status'] = 4;
                					$response['data'] = getEmpDailyAttendance($conn,$id,$date);
                					$response['message'] = 'DONE CHECK-OUT';
                				}else{
                				    $response['error'] = false;
                					$response['status'] = 10;
                					$response['data'] = [];
                					$response['message'] = 'unabel to process request!';
                				}
                            }
                      }
                      if($chk['c']===4){
                            //Check_OUT_DONE
                           
                                $response['error'] = false;
            					$response['status'] = 4;
            					$response['data'] = getEmpDailyAttendance($conn,$id,$date);
            					$response['message'] = 'DONE CHECK-OUT';
                           
                      }
                     
                      
                      
                  }
                  
                  
                  if($fn==='check_in_out'){
                      $opt=$params['opt'];
                      $ffid=$params['ffid'];
                      $date=isset($params['date'])?$params['date']:date('Y-m-d');
                      $time=isset($params['time'])?$params['time']:date('H:i:s');
                      $emp_id=0;
                      if($opt===1){
                        $emp_id=$ffid;
                      }else if($opt===2){
                        $emp_id=$ffid;  
                      }else{
                        $emp_id=getEmpId($conn,$ffid,$opt);
                      }
                      if($emp_id===0){
                        $response['error'] = false;
    					$response['status'] = 10;
    					$response['message'] = 'invalid request {Employee not found!}';
                      }else{
                          $chk=chkAtt($conn,$emp_id,$date);
                          $c=$chk;
                          if($c===0){
                            
                            $stmt = $conn->prepare("INSERT INTO attendance(emp_id, src, in_time, date) VALUES (?,?,?,?)");
            				$stmt->bind_param("ssss", $emp_id, $opt,  $time, $date);
                				
            				if($stmt->execute()){
                                $response['error'] = false;
            					$response['status'] = 0;
            					$response['data'] = getEmpDailyAtt($conn,$emp_id,$date);
            					$response['message'] = 'DONE CHECK-IN';
            				}else{
            				    $response['error'] = false;
            					$response['status'] = 10;
            					$response['data'] = [];
            					$response['message'] = 'unabel to process request!';
            				}
                          }elseif($c===1){
                              
                            
                            $stmt = $conn->prepare("UPDATE attendance SET out_time='$time' WHERE emp_id=$emp_id AND date='$date'");

            				if($stmt->execute()){
                                $response['error'] = false;
            					$response['status'] = 1;
            					$response['data'] = getEmpDailyAtt($conn,$emp_id,$date);
            					$response['message'] = 'DONE CHECK-OUT';
            				}else{
            				    $response['error'] = false;
            					$response['status'] = 10;
            					$response['data'] = [];
            					$response['message'] = 'unabel to process request!';
            				}
                            
                          }elseif($c===2){
                            $response['error'] = false;
        					$response['status'] = 2;
        					$response['data'] = getEmpDailyAtt($conn,$emp_id,$date);
        					$response['message'] = 'CHECK-DONE';
                          }
                          
                      }
                      
                      
                  }
                  if($fn==='get_daily'){
                        $date=$params['date'];
                        $data=array();
            
            		    $stmt = $conn->prepare("SELECT us.id, us.name, us.phone, us.location, us.type, a.id AS attendance_id, a.src, a.in_time, a.out_time, a.date FROM user_master AS us LEFT JOIN attendance AS a ON a.id = ( SELECT a1.id FROM attendance AS a1 WHERE a1.emp_id = us.id ORDER BY a1.date DESC, a1.in_time DESC LIMIT 1 ) WHERE a.date='$date';");
            
                		$stmt->execute();
                		$stmt->store_result();
    				    $stmt->bind_result($id,$name,$phone,$location,$type,$at_id,$src,$in_time,$out_time,$date);
                 
                		while ($stmt->fetch()) {
                		    
    						$att = array(
                                'id' => $id,
                                'name' => $name,
                                'phone' => $phone,
                                'location' => $location,
                                'type' => $type,
                                'at_id' => $at_id,
                                'src' => $src,
                                'in_time' => $in_time,
                                'out_time' => $out_time,
                                'date' => dmy($date)
                                );
                		    array_push($data,$att);
                		}
                		if(empty($data)){
                		    $response['error'] = false;
        					$response['status'] = 2;
        					$response['data']=$data;
        					$response['message'] = 'daily attendance list not';
                		}else{
                    		$response['error'] = false;
        					$response['status'] = 1;
        					$response['data']=$data;
        					$response['message'] = 'daily attendance list';
                		}

                      
                  }
                  if($fn==='user_att_details'){
                        $us_id=$params['us_id'];
                        $sdate=$params['sdate'];
                        $edate=$params['edate'];
                        $data=array();
            
            		    $stmt = $conn->prepare("SELECT id, src, in_time, out_time, l_in, l_out, date FROM attendance WHERE emp_id=$us_id AND (date>='$sdate' AND date<='$edate') ORDER BY date ASC;");
            
                		$stmt->execute();
                		$stmt->store_result();
    				    $stmt->bind_result($id, $src, $in_time, $out_time, $l_in, $l_out, $date);
                 
                		while ($stmt->fetch()) {
                		    
    						$att = array(
                                'id' => $id,
                                'src' => $src,
                                'in_time' => $in_time,
                                'out_time' => $out_time,
                                'l_in' => $l_in,
                                'l_out' => $l_out,
                                'hour' => chbt($in_time,$out_time),
                                'date' => dmy($date)
                                );
                		    array_push($data,$att);
                		}
                		if(empty($data)){
                		    $response['error'] = false;
        					$response['status'] = 2;
        					$response['data']=$data;
        					$response['message'] = 'daily attendance list not';
                		}else{
                    		$response['error'] = false;
        					$response['status'] = 1;
        					$response['data']=$data;
        					$response['message'] = 'daily attendance list';
                		}
                  }
                  if($fn==='lunch'){
                    $id=$params['id'];
                    $time=$params['time'];
                      
                    $stmt = $conn->prepare("SELECT id, emp_id, src, in_time, out_time,l_in,l_out, date FROM attendance WHERE id=$id ORDER BY id DESC LIMIT 1");
                    $stmt->execute();
                    
                    $stmt->store_result();
                    
                    if($stmt-> num_rows > 0){
                        $stmt->bind_result($ids, $emp_id, $src, $in_time, $out_time,$l_in,$l_out, $date);
                        $stmt->fetch();
                        
                        if($l_in=='00:00:00'){
                            $stmt = $conn->prepare("UPDATE attendance set l_in='$time' WHERE id=$id");
                            if($stmt->execute()){
                                $response['error'] = false;
            					$response['status'] = 1;
            					$response['message'] = 'you can go for lunch.' ;
                            }else{
                                $response['error'] = false;
            					$response['status'] = 10;
            					$response['message'] = 'failed to lunche check-in please try again!' ;
                            }
                        }elseif($l_out=='00:00:00'){
                            $stmt = $conn->prepare("UPDATE attendance set l_out='$time' WHERE id=$id");
                            if($stmt->execute()){
                                $response['error'] = false;
            					$response['status'] = 1;
            					$response['message'] = 'welcome back. hope your lunch was delicious.' ;
                            }else{
                                $response['error'] = false;
            					$response['status'] = 10;
            					$response['message'] = 'failed to lunche check-out please try again!' ;
                            }
                        }else{
                            $response['error'] = false;
        					$response['status'] = 10;
        					$response['message'] = "please try again!";
                        }
                        
                        
                    }else{
                    	$response['error'] = false;
    					$response['status'] = 10;
    					$response['message'] = "you haven't checked in!";
                    }
                  }
                
    		}
    		break;
    		case 'in_out':{
    		      $fn='';
    		      if (isset($params['fn'])) {
                      $fn=$params['fn'];
                  }else{
                      $fn=$_POST['fn'];
                      $params=$_POST;
                  }
                  
                  if($fn==='get_in_out'){
                        $aid=$params['aid'];
                      
                        $data=array();
            
            		    $stmt = $conn->prepare("SELECT id, a_id, date, in_time, out_time FROM in_out WHERE a_id=$aid ORDER BY id ASC");
            
                		$stmt->execute();
                		$stmt->store_result();
    				    $stmt->bind_result($id, $a_id, $date, $in_time, $out_time);
                 
                		while ($stmt->fetch()) {
                		    
    						$att = array(
                                'id' => $id,
                                'a_id' => $a_id,
                                'date' => $date,
                                'in_time' => $in_time,
                                'out_time' => $out_time
                                );
                		    array_push($data,$att);
                		}
                		if(empty($data)){
                		    $response['error'] = false;
        					$response['status'] = 2;
        					$response['data']=$data;
        					$response['message'] = 'in out list not';
                		}else{
                    		$response['error'] = false;
        					$response['status'] = 1;
        					$response['data']=$data;
        					$response['message'] = 'in out list';
                		}
                  }
                  if($fn==='add_in_out'){
                      $id=$params['id'];
                      $aid=$params['aid'];
                      $date=$params['date'];
                      $time=$params['time'];
                      
                      if($id===0){
                          $stmt = $conn->prepare("INSERT INTO in_out(a_id, date, out_time) VALUES (?,?,?)");
            			  $stmt->bind_param("sss", $aid, $date,  $time);
                				
        				  if($stmt->execute()){
                            $response['error'] = false;
        					$response['status'] = 1;
        					$response['message'] = 'DONE OUT';
        				  }else{
        				    $response['error'] = false;
        					$response['status'] = 10;
        					$response['message'] = 'unabel to process request!';
        				  }
                      }else{
                          $stmt = $conn->prepare("UPDATE in_out set in_time='$time' WHERE id=$id");
                          if($stmt->execute()){
                            $response['error'] = false;
        					$response['status'] = 1;
        					$response['message'] = 'DONE IN';
        				  }else{
        				    $response['error'] = false;
        					$response['status'] = 10;
        					$response['message'] = 'unabel to process request!';
        				  }
                      }
                      
                  }
    		    
    		}
    		break;
    		case 'aadhar':{
    		        
    		      $fn='';
    		      
    		      $t='eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJmcmVzaCI6ZmFsc2UsImlhdCI6MTcyMTE5ODc3NiwianRpIjoiODFkYjFhZmEtNWQ4ZS00NzM1LThlZDEtMmJlN2Y2YjA5OTY4IiwidHlwZSI6ImFjY2VzcyIsImlkZW50aXR5IjoiZGV2LmludGVybGlua0BzdXJlcGFzcy5pbyIsIm5iZiI6MTcyMTE5ODc3NiwiZXhwIjoyMDM2NTU4Nzc2LCJlbWFpbCI6ImludGVybGlua0BzdXJlcGFzcy5pbyIsInRlbmFudF9pZCI6Im1haW4iLCJ1c2VyX2NsYWltcyI6eyJzY29wZXMiOlsidXNlciJdfX0.fT7W8WPBcf-QEkiC0YoB70Jn2lVFuV3FUOKMmSJskiY';
        		  if (isset($params['fn'])) {
                      $fn=$params['fn'];
                  }else{
                      $fn=$_POST['fn'];
                      $params=$_POST;
                  }
                  
                  
                  if($fn==='send_otp'){
                        $id_number=$params['id_number'];
                        $data=array('id_number'=>$id_number);
                        $url='https://kyc-api.surepass.io/api/v1/aadhaar-v2/generate-otp';  
                        
                        $ch = curl_init($url);
                        
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                            'Content-Type: application/json',
                            'Authorization: Bearer ' . $t
                        ]);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                        
                        $res = curl_exec($ch);
                        curl_close($ch);
                        
                        if ($res === false) {
                            $response['error'] = false;
        					$response['status'] = 10;
        					$response['message'] = "please try again!";
                        }else{
                            $data=json_decode($res, true);
                            if($data['success']===true){
                                $data=$data['data'];
                                
                                $response['error'] = false;
            					$response['status'] = 1;
            					$response['client_id'] = $data['client_id'];
            					$response['otp_sent'] = $data['otp_sent'];
            					$response['if_number'] = $data['if_number'];
            					$response['valid_aadhaar'] = $data['valid_aadhaar'];
            					$response['message_status'] = $data['status'];
                            }else{
                                $response['error'] = false;
            					$response['status'] = 10;
            					$response['message'] = $data['message'];
                            }
                            
                        }
                  }
                  if($fn==='verify_otp'){
                    $client_id=$params['client_id'];
                    $otp=$params['otp'];
                    $data=array('client_id'=>$client_id,'otp'=>$otp);
                    $url='https://kyc-api.surepass.io/api/v1/aadhaar-v2/submit-otp';    
                    $ch = curl_init($url);
                        
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $t
                    ]);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                    
                    $res = curl_exec($ch);
                    curl_close($ch);
                    
                    if ($res === false) {
                        $response['error'] = false;
    					$response['status'] = 10;
    					$response['message'] = "please try again!";
                    }else{
                        $data=json_decode($res, true);
                        if($data['success']===true){
                            $data=$data['data'];
                            
                            $response['error'] = false;
        					$response['status'] = 1;
        					$response['data'] = $data;
        					$response['message_status'] = $data['status'];
                        }else{
                            $response['error'] = false;
        					$response['status'] = 10;
        					$response['message'] = $data['message_code']==null?$data['message']:$data['message_code'];
                        }
                        
                    }
                      
                  }
                  
    		}
    		break;
            case 'leaves':{
                $fn='';
                if (isset($params['fn'])) {
                    $fn=$params['fn'];
                }else{
                    $fn=$_POST['fn'];
                    $params=$_POST;
                }
                
                if($fn==='add_leave'){
                    //SELECT id, us_id, date, remark, cr_id, cr_date, up_id, up_date FROM leave_master
                    $id=$params['id'];
                    $us_id=$params['us_id'];
                    $l_date=$params['l_date'];
                    $reason=$params['reason'];
                    $up_id=$params['up_id'];
                    $up_date=$params['up_date'];

                    if($id===0){
                        $stmt = $conn->prepare("INSERT INTO leave_master(us_id, date, remark, cr_id, cr_date, up_id, up_date) VALUES (?,?,?,?,?,?,?)");
                        $stmt->bind_param("sssssss", $us_id, $l_date, $reason, $up_id, $up_date, $up_id, $up_date);
                        if($stmt->execute()){
                            $response['error'] = false;
        					$response['status'] = 1;
        					$response['message'] = 'add leave done';
                        }else{
                            $response['error'] = false;
        					$response['status'] = 10;
        					$response['message'] = 'FAILED';   
                        }
                    }else{
                        $stmt = $conn->prepare("UPDATE leave_master set date='$l_date', remark='$reason', up_id='$up_id', up_date='$up_date' WHERE id=$id");
                        if($stmt->execute()){
                            $response['error'] = false;
        					$response['status'] = 2;
        					$response['message'] = 'update leave';
                        }else{
                            $response['error'] = false;
        					$response['status'] = 10;
        					$response['message'] = 'FAILED';
                        }
                    }   
                }
                if($fn==='get_leaves'){
                    $us_id=$params['us_id'];
                    $sdate=$params['sdate'];
                    $edate=$params['edate'];
                    $data=array();

                    $stmt = $conn->prepare("SELECT id, us_id, date, remark, up_id, up_date FROM leave_master WHERE us_id=$us_id AND date BETWEEN '$sdate' AND '$edate'");
                    $stmt->execute();
                    
                    $stmt->store_result();
                    
                    $stmt->bind_result($id, $us_id, $date, $remark, $up_id, $up_date);
                    
                    while ($stmt->fetch()) {
                        $att=array(
                            'id' => $id,
                            'us_id' => $us_id,
                            'date' => $date,
                            'remark' => $remark,
                            'up_id' => $up_id,
                            'up_date' => $up_date
                            );
                        array_push($data,$att);
                    }
                    if(empty($data)){
                        $response['error'] = false;
    					$response['status'] = 2;
    					$response['data'] = $data;
    					$response['message'] = 'leave list not found';
                    }else{
                        $response['error'] = false;
    					$response['status'] = 1;
    					$response['data'] = $data;
    					$response['message'] = 'leave list found';
                    }
                    
                }
            }
            break;
    		case 'report':{
    		     $fn='';
        		  if (isset($params['fn'])) {
                      $fn=$params['fn'];
                  }else{
                      $fn=$_POST['fn'];
                      $params=$_POST;
                  }
                  
                  if($fn==='daily_count'){
                      
                  }
                  if($fn==='get_att'){
                      
                  }
                  if($fn==='get_report'){
                      $m=$params['m'];
                      $y=$params['y'];
                      calculateSalary($m,$y);
                  }
                  
                  
    		}
    		break;
    		case 'punch_out':{
    		    exit();
    		    $date = date('Y-m-d');
    		    $stmt = $conn->prepare("UPDATE attendance set out_time='22:30:00' WHERE date='$date' AND out_time='00:00:00'");
        		$stmt->execute();
    		}
    		break;
    		default: 
    			$response['error'] = true; 
    			$response['message'] = 'Invalid Operation Called';
    		break;	
	    }    
    }else{
        $response['error'] = true; 
	    $response['message'] = 'Invalid API Call';
    }    
    
    
    //header('Content-Type: application/json; charset=utf-8');
    //echo json_encode($response);
    $show_json = json_encode($response ,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ( json_last_error_msg()=="Malformed UTF-8 characters, possibly incorrectly encoded" ) {
        $show_json = json_encode($response, JSON_PARTIAL_OUTPUT_ON_ERROR );
    }
    if ( $show_json !== false ) {
        echo($show_json);
    } else {
        die("json_encode fail: " . json_last_error_msg());
    }
	
	//function validating all the paramters are available
	//we will pass the required parameters to this function 
	function isTheseParametersAvailable($params){
		
		//traversing through all the parameters 
		foreach($params as $param){
			//if the paramter is not available
			if(!isset($_POST[$param])){
				//return false 
				return false; 
			}
		}
		//return true if every param is available 
		return true; 
	}
	
	function chkAtt($conn,$id,$date){
	    $stmt = $conn->prepare("SELECT id, emp_id, src, in_time, out_time, date FROM attendance WHERE date='$date' AND emp_id=$id LIMIT 1");
        $stmt->execute();
        
        $stmt->store_result();
        $stmt->bind_result($id, $emp_id, $src, $in_time, $out_time, $date);
        $c=0;
        
       
        if($stmt-> num_rows > 0){
            $stmt->fetch();
            if($in_time != '00:00:00'){
                $c=1;
                
            }
            
            if($out_time != '00:00:00'){
                $c=2;
                
            }
            // if($in_time != '00:00:00' && $out_time != '00:00:00'){
            //     $c=2;
            // }
        }
        
        return $c;
	}
	function chk($conn,$id,$date,$time){
	    $data=array();
	    $stmt = $conn->prepare("SELECT id, emp_id, src, in_time, out_time, l_in, l_out, date FROM attendance WHERE date='$date' AND emp_id=$id LIMIT 1");
        $stmt->execute();
        
        $stmt->store_result();
        $stmt->bind_result($id, $emp_id, $src, $in_time, $out_time, $l_in, $l_out, $date);
        $c=0;
        $data=['c'=>$c,'v'=>true];
       
        if($stmt-> num_rows > 0){
            $stmt->fetch();
            if($in_time != '00:00:00'){
                $c=1;
                $data=['c'=>$c,'v'=>isTimeWithinFiveMinutes($in_time, $time)];
            }
            
            if($l_out != '00:00:00'){
                $c=2;
                $data=['c'=>$c,'v'=>isTimeWithinFiveMinutes($l_out, $time)];
            }
            
            if($l_in != '00:00:00'){
                $c=3;
                $data=['c'=>$c,'v'=>isTimeWithinFiveMinutes($l_in, $time)];
               
            }
            if($out_time != '00:00:00'){
                $c=4;
                $data=['c'=>$c,'v'=>isTimeWithinFiveMinutes($out_time, $time)];
            }
            
        }
        
        return $data;
	}
	function isTimeWithinFiveMinutes($t1, $t2) {
        // Convert strings to DateTime objects
        $time1 = new DateTime($t1);
        $time2 = new DateTime($t2);
    
        // Add 5 minutes to $t1
        $time1->add(new DateInterval('PT5M'));
    
        // Check if $t2 is less than or equal to $t1 + 5 minutes
        if ($time2 <= $time1) {
            return false; // $t2 is within 5 minutes after $t1
        }
        return true; // $t2 is not within the 5-minute window
    }
	function getAttData($conn,$id,$date){
	    
	    $stmt = $conn->prepare("SELECT id, emp_id, src, in_time, out_time,l_in,l_out, date FROM attendance WHERE date='$date' AND emp_id=$id LIMIT 1");
        $stmt->execute();
        
        $stmt->store_result();
        $stmt->bind_result($ids, $emp_id, $src, $in_time, $out_time,$l_in,$l_out, $date);
        if($stmt-> num_rows > 0){
            $stmt->fetch();
            return ['id'=>$ids,'emp_id'=>$emp_id, 'src'=>$src, 'in_time'=>$in_time, 'out_time'=>$out_time, 'l_in'=>$l_in, 'l_out'=>$l_out,'date'=>$date];
        }else{
            return ['id'=>0,'emp_id'=>$id, 'src'=>0, 'in_time'=>'', 'out_time'=>'', 'l_in'=>'', 'l_out'=>'', 'date'=>$date];
        }
	    
	}

	function getEmpId($conn,$ffid,$opt){
	    
	    #FACE
	    if($opt==0){
	        $stmt = $conn->prepare("SELECT id FROM user_master WHERE face='$ffid' LIMIT 1");
	        
			$stmt->execute();
			$stmt->store_result();
				
			
			if($stmt-> num_rows > 0){
			    $stmt->bind_result($id);
				$stmt->fetch();
				return $id;
			}
	    }
	    
	    #FINGER
	    if($opt==1){
	        
	        $stmt = $conn->prepare("SELECT id FROM user_master WHERE finger='$ffid' LIMIT 1");
	        $stmt->execute();
			$stmt->store_result();
				
			
			if($stmt-> num_rows > 0){
			    $stmt->bind_result($id);
				$stmt->fetch();
				return $id;
			}
	    }
	    
	    return 0;
	}
	

	
	function dmy($date) {
        // Create a DateTime object from the input date
        $dateTime = DateTime::createFromFormat('Y-m-d', $date);

        // Check if the date is valid
        if ($dateTime) {
            // Return the date formatted as 'd-m-Y'
            return $dateTime->format('d-m-Y');
        } else {
            // Handle invalid date input
            return $date;
        }
    }
	
	function getEmpDailyAtt($conn,$emp_id,$date){
	    //SELECT us.id,us.name,us.phone,a.id,a.src,a.in_time,a.out_time,a.date FROM user_master AS us LEFT JOIN attendance AS a ON a.emp_id=us.id WHERE us.id=2 ORDER BY a.id DESC LIMIT 1;
	    
	    $stmt = $conn->prepare("SELECT us.id,us.name,us.phone,a.id,a.src,a.in_time,a.out_time,a.date FROM user_master AS us LEFT JOIN attendance AS a ON a.emp_id=us.id WHERE us.id=$emp_id AND a.date='$date' ORDER BY a.id DESC LIMIT 1");
		$stmt->execute();
		$stmt->store_result();
		
	    $stmt->bind_result($id, $name, $phone, $at_id, $src, $in_time, $out_time, $date);
		$stmt->fetch();
		
		    
			$data = array(
                'id' => $id==null?0:$id,
                'name' => $name==null?'User Not Found':$name,
                'phone' => $phone==null?'':$phone,
                'at_id' => $at_id==null?0:$at_id,
                'src' => $src==null?0:$src,
                'in_time' => $in_time==null?'':$in_time,
                'out_time' => $out_time==null?'':$out_time,
                'date' => $date==null?'':dmy($date)
                );
                
                     
    			
        return $data;	    
	}
	function getEmpDailyAttendance($conn,$emp_id,$date){
	    //SELECT us.id,us.name,us.phone,a.id,a.src,a.in_time,a.out_time,a.date FROM user_master AS us LEFT JOIN attendance AS a ON a.emp_id=us.id WHERE us.id=2 ORDER BY a.id DESC LIMIT 1;
	    
	    $stmt = $conn->prepare("SELECT us.id,us.name,us.phone,a.id,a.src,a.in_time,a.l_in,a.l_out,a.out_time,a.date FROM user_master AS us LEFT JOIN attendance AS a ON a.emp_id=us.id WHERE us.id=$emp_id AND a.date='$date' ORDER BY a.id DESC LIMIT 1");
		$stmt->execute();
		$stmt->store_result();
		
	    $stmt->bind_result($id, $name, $phone, $at_id, $src, $in_time, $l_in, $l_out, $out_time, $date);
		$stmt->fetch();
		
		    
			$data = array(
                'id' => $id==null?0:$id,
                'name' => $name==null?'User Not Found':$name,
                'phone' => $phone==null?'':$phone,
                'at_id' => $at_id==null?0:$at_id,
                'src' => $src==null?0:$src,
                'in_time' => $in_time==null?'':$in_time,
                'l_in' => $l_in==null?'':$l_in,
                'l_out' => $l_out==null?'':$l_out,
                'out_time' => $out_time==null?'':$out_time,
                'date' => $date==null?'':dmy($date)
                );
                
                     
    			
        return $data;	    
	}
	
	function calculateSalary($m,$y){
	    $d=$y.'-'.$m.'-01';
	    $d=getFirstAndLastDateOfMonth($d);
	    $s=$d['start'];
	    $e=$d['end'];
	    
	}
	
	function saveFilePic($file,$dir,$id){
	    $target_dir = $dir;
	
		$path = pathinfo($file);
		$filename = $path['filename'];
		$ext = $path['extension'];
		$temp_name = $_FILES['file']['tmp_name'];
		$file_path = $target_dir.$id.".".$ext;
		if(move_uploaded_file($temp_name,$file_path)){
		    $file_path = $target_dir.$id.".".$ext;
		    return $file_path;
		}else{
		    return "";
		}
	}

	function getFirstAndLastDateOfMonth($date) {
        // Create a DateTime object from the input date
        $inputDate = new DateTime($date);
        
        // Get the first day of the month
        $firstDateOfMonth = new DateTime($inputDate->format('Y-m-01'));
        
        // Get the last day of the month by modifying the date to the first day of the next month and then subtracting one day
        $lastDateOfMonth = new DateTime($inputDate->format('Y-m-01'));
        $lastDateOfMonth->modify('first day of next month')->modify('-1 day');
        
        // Return the dates as an array
        return [
            'start' => $firstDateOfMonth->format('Y-m-d'),
            'end' => $lastDateOfMonth->format('Y-m-d')
        ];
    }
	function notify($topic,$title,$body,$event,$data){
	    require_once __DIR__ . '/firebase/config.php';
        $url = 'https://fcm.googleapis.com/fcm/send';
        
        $fields = array(
                    'to' => '/topics/'.$topic,
                    'data'=>array('title'=>$title,'body'=>$body,'event'=>$event,'data'=>$data)
                );
        $headers = array(
            'Authorization: key=' . FIREBASE_API_KEY,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();
        
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        
        curl_close($ch);
        // $r=json_encode($fields);
        // echo $r;
	}
	function chbt($start_time, $end_time, $format = 'H:i:s') {
        // Create DateTime objects from the input time strings
        $start = DateTime::createFromFormat($format, $start_time);
        $end = DateTime::createFromFormat($format, $end_time);
    
        // Check if the DateTime objects were created successfully
        if (!$start || !$end) {
            return "--";
        }
        if ($start_time==='00:00:00' || $end_time==='00:00:00') {
            return "--";
        }
    
        // Calculate the difference in seconds
        $difference_in_seconds = $end->getTimestamp() - $start->getTimestamp();

        // Convert the difference to hours
        $hours_difference = $difference_in_seconds / 3600;
        
        return number_format($hours_difference, 2);
    }
    function getAMP($time) {
        $hour = date('H', strtotime($time)); // Extract the hour in 24-hour format
    
        if ($hour >= 5 && $hour < 12) {
            return 1; // Morning
        } elseif ($hour >= 12 && $hour < 17) {
            return 2; // Afternoon
        } elseif ($hour >= 17 && $hour < 23) {
            return 3; // Evening
        } else {
            return 0; // Night or other times
        }
    }
	