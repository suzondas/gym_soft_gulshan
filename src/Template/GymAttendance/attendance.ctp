<?php $session = $this->request->session()->read("User");?>
<div class="container">
<table class="table" style="width:60%;" border="1">
    <tbody  style="border:1px solid black;"><tr>
        <!--<td><a href="newEntry">-->
        <!--        <button>New Entry</button>-->
        <!--    </a></td>-->
        <!--<td><a href="searchUser">-->
        <!--        <button>Search User</button>-->
        <!--    </a></td>-->
        <td>Select Members to get Attendance Report based on Date : <br><a target="_blank" href="http://103.91.228.56/csl/report">
                <button>Attendance Report</button>
            </a></td>
            <td>To see Members Entry/Exit Realtime: <br><a target="_blank" href="https://door.fitnessplusbd.com/realtime">
                <button>Real Time Monitoring</button>
            </a></td>
    </tr>
</tbody></table>
</div>