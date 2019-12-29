<?php
  $scheduleNum =count($schedules);
  $workerNum = count($workers);
?>

<script>

  var checked = 0;
  var unChecked = false;
  var totalCheckIn = 0;
  var totalWorkSecond = 0;

  var getInFind = false;
  var getOutFind = false;
  var classification = <?= $classification; ?>;
  var contractHour = 0;


  $(document).ready(function(){ 
    if(classification==1) set_days();
    else set_days_without();
   });
   
   function isWeekday(thisYear,thisMonth,thisDay) {
    
    let temp = '\"'+thisYear+'-'+thisMonth+'-'+thisDay+'\"';
    var d = new Date(temp);
    var n = d.getDay();

    if(n==0 || n==6) return true;
    else return false;
}


  function getTime(thisYear,thisMonth,thisDay,option) {
    var i=0;
    var num = <?=$scheduleNum?>;
    var workerID = <?=$workerId;?>;
    var array =  <?php echo json_encode($schedules); ?>;
    var temp = '';
    var isFind = false;

    for(i=0;i<num;i++){
      if(thisDay < 10) temp=thisYear+'-'+thisMonth+'-0'+thisDay;
      else temp=thisYear+'-'+thisMonth+'-'+thisDay;

      if(workerID==array[i].workerID && temp==array[i].day) {
        isFind = true;
        if(option==1) {
          if(array[i].getIn != "00:00:00") getInFind = true;
          return array[i].getIn;
        }
        else {
          if(array[i].getOut != "00:00:00") getOutFind = true;
          return array[i].getOut;
        }
      }
    }

    if(!isFind) {
      return '';
    }

  }

  function getPrintDiff(amout) {
    let hour = Math.floor(amout/3600);
    let minute = Math.floor((amout%3600)/60);

    return hour+"시간 "+minute+"분";
  }

  function getTimeDiff(thisYear,thisMonth,i,arrayIn,arrayOut) {

    var outDate = new Date(thisYear,thisMonth,i,arrayOut[0],arrayOut[1],arrayOut[2]);
    var inDate = new Date(thisYear,thisMonth,i,arrayIn[0],arrayIn[1],arrayIn[2]);

    var temp = (outDate.getTime()-inDate.getTime()) / 1000; //초단위
    totalWorkSecond += temp;
    
    return getPrintDiff(temp);

  }

    function set_days() { //아르바이트생 전용
        
        /********variables***********/
        var i=0;
        var numOfWeekEnd = 0;
        var totalNumOfWeekEnd = 0;
        var tempInTime = 0;
        var tempOutTime = 0;
        var arrayIn = [];
        var arrayOut = [];

        /********date***********/
        var d=new Date();
        var thisYear = d.getFullYear();
        var thisMonth = d.getMonth()+1;
        var thisDate = d.getDate();
        var last_day = getLastDayOfMonth(thisYear,thisMonth);

        var innerText = '<table class="table"><thead><tr><th scope="col">#</th><th scope="col">출근</th><th scope="col">퇴근</th><th></th>'
            +'<th>근무시간</th></tr></thead><tbody>';

        for(i=1;i<last_day+1;i++) {
            if(!isWeekday(thisYear,thisMonth,i)) {

                tempInTime=getTime(thisYear,thisMonth,i,1);
                tempOutTime=getTime(thisYear,thisMonth,i,2);

                arrayIn = tempInTime.split(':');
                arrayOut = tempOutTime.split(':');
                
                innerText += '<tr><th scope="row">'+thisYear+'-'+thisMonth+'-'+i+'</th>';
                innerText += '<td>'+tempInTime+'</td>';
                innerText += '<td>'+tempOutTime+'</td>';

                if(getInFind && getOutFind) {
                checked++;
                unChecked = false;
                }
                else unChecked = true;
                getInFind = false;
                getOutFind = false;
                
                if(unChecked) innerText += '<td style="color:red;"><strong>미확인</strong></td>';
                else {
                  innerText += '<td style="color:green;"><strong>확인</strong></td>';
                  innerText += '<td>'+getTimeDiff(thisYear,thisMonth-1,i,arrayIn,arrayOut)+'</td>';
                }
            }else{
                totalNumOfWeekEnd++;
                if(thisDate>=i)numOfWeekEnd++;
            }
            
        }

        
        let totalDay = last_day-totalNumOfWeekEnd;
        let absent = thisDate-numOfWeekEnd-checked;

        $('#work_days').html(innerText+'</tbody></table>');

        $('#total_month').text('이번달 근무일 : '+totalDay+'일');
        $('#unchecked_day').text(' 미완료 및 결근일 : '+absent+'일');
        $('#checked_day').text('출근일 : '+checked+'일');
        $('#total_hour').text('근무시간 : '+getPrintDiff(totalWorkSecond));

    } 

    function set_days_without() { //계약직

        var i=0;
        var numOfWeekEnd = 0;
        var totalNumOfWeekEnd = 0;

        var d=new Date();
        var thisYear = d.getFullYear();
        var thisMonth = d.getMonth()+1;
        var thisDate = d.getDate();
        var last_day = getLastDayOfMonth(thisYear,thisMonth);

        var innerText = '<table class="table"><thead><tr><th scope="col">#</th><th scope="col">출근</th>'+
            '<th scope="col">확인</th></tr></thead><tbody>';


        for(i=1;i<last_day+1;i++) {
            if(!isWeekday(thisYear,thisMonth,i)) {
                innerText += '<tr><th scope="row">'+thisYear+'-'+thisMonth+'-'+i+'</th>';
                innerText += '<td>'+getTime(thisYear,thisMonth,i,1)+'</td>';

                if(getInFind) {
                    checked++;
                    unChecked = false;
                }
                else unChecked = true;
                getInFind = false;

                if(unChecked) innerText += '<td style="color:red;"><strong>미확인</strong></td>';
                else innerText += '<td style="color:green;"><strong>확인</strong></td>';
            }else{
                totalNumOfWeekEnd++;
                if(thisDate>=i) numOfWeekEnd++;
            }
            
        }

        let totalDay = last_day-totalNumOfWeekEnd;
        let absent = thisDate-numOfWeekEnd-checked;

        console.log("이번달 근무일 : 마지막날 - 주말갯수 => "+last_day+"-"+totalNumOfWeekEnd);
        console.log("결석 : 오늘 - 주말갯수 - 출석갯수 => "+thisDate+"-"+numOfWeekEnd+"-"+checked);

        $('#total_month').text('이번달 근무일 : '+totalDay+'일');
        $('#work_days').html(innerText+'</tbody></table>');
        $('#unchecked_day').text(' 미완료 및 결근일 : '+absent+'일');
        $('#checked_day').text('출근일 : '+checked+'일');
    }

</script>

<nav class="navbar navbar-dark bg-dark">
    <div class="container">
    <span class="navbar-brand mb-0 h1">코코스 현황</span>
        <div class="row">
          <div class="col-sm">
              <span class="navbar-brand mb-0 h1">근로자 수 : <?= $workerNum ?></span>
          </div>
          <div class="col-sm">
              <span class="navbar-brand mb-0 h1">학생 수 : </span>
          </div>
        </div>
    </div>
</nav>
<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <div class="row">
            <div class="col-sm">
            <span class="navbar-brand mb-0 h1"><p id="yulsan_students"></p></span>
            </div>
            <div class="col-sm">
            <span class="navbar-brand mb-0 h1"><p id="yulsan_teachers"></p></span>
            </div>
        </div>
    </div>
</nav> 

<ul class="nav nav-pills justify-content-end">
  <?php foreach($workers as $worker) {?>
    <li class="nav-item">
      <?php if($workerId==$worker->id) {?>
        <a class="nav-link active" href="/index.php/master/index/<?=$workerId?>/<?=$worker->classification?>" 
          style="background-color:black; color:white;"><?=$worker->name?></a>
      <?php }else{ ?>
        <a class="nav-link" href="/index.php/master/index/<?=$worker->id?>/<?=$worker->classification?>"
          style="color:black;"><?=$worker->name?></a>
      <?php } ?>
    </li>
  <?php } ?>
</ul> 

<div class="contianer">
    <div class="row">
        <div class="col-sm">
        <span class="navbar-brand mb-0 h1"><p id="total_month"></p></span>
        </div>
        <div class="col-sm">
        <span class="navbar-brand mb-0 h1"><p id="unchecked_day"></p></span>
        </div>
        <div class="col-sm">
        <span class="navbar-brand mb-0 h1"><p id="checked_day"></p></span>
        </div>
        <div class="col-sm">
        <span class="navbar-brand mb-0 h1"><p id="total_hour"></p></span>
        </div>
    </div>
    <div id="work_days"></div>
</div>
