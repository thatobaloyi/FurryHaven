<?php
// Shared helpers for analytics endpoints
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__.'/config/databaseconnection.php';

function dateRangeFromPreset($preset){
    $end = date('Y-m-d');
    if($preset === 'last7') $start = date('Y-m-d', strtotime('-6 days'));
    else if($preset === 'last30') $start = date('Y-m-d', strtotime('-29 days'));
    else if($preset === 'ytd') $start = date('Y-01-01');
    else $start = null;
    return array($start, $end);
}

function build_analytics_payload($conn, $analytic, $start=null, $end=null){
    $response = array('type'=>'bar','data'=>array('labels'=>array(), 'datasets'=>array()), 'options'=>array());
    if($analytic === 'monthlyTrends'){
        $labels = array(); $intakes = array(); $adopts = array();
        for($i=11;$i>=0;$i--){
            $m = date('M', strtotime("-{$i} months"));
            $y = date('Y', strtotime("-{$i} months"));
            $labels[] = $m;
            $res1 = $conn->query("SELECT COUNT(*) as c FROM animal WHERE MONTH(Animal_RescueDate)=".date('n',strtotime("-{$i} months"))." AND YEAR(Animal_RescueDate)={$y}");
            $intakes[] = $res1 ? intval($res1->fetch_assoc()['c']) : 0;
            $res2 = $conn->query("SELECT COUNT(*) as c FROM animal WHERE outtakeType='Adoption' AND MONTH(outtakeDate)=".date('n',strtotime("-{$i} months"))." AND YEAR(outtakeDate)={$y}");
            $adopts[] = $res2 ? intval($res2->fetch_assoc()['c']) : 0;
        }
        $response['type']='line';
        $response['data']=array('labels'=>$labels,'datasets'=>array(
            array('label'=>'Intakes','data'=>$intakes,'borderColor'=>'#007bff','fill'=>false),
            array('label'=>'Adoptions','data'=>$adopts,'borderColor'=>'#17a2b8','fill'=>false)
        ));
        $response['options']=array('plugins'=>array('title'=>array('display'=>true,'text'=>'Monthly Intakes vs Adoptions')));
    }
    elseif($analytic === 'donations'){
        if(!$start || !$end){ list($s,$e)=dateRangeFromPreset('last30'); $start=$s;$end=$e; }
        $labels = array(); $data = array();
        $cur = strtotime($start); $endts = strtotime($end);
        while($cur <= $endts){
            $d = date('Y-m-d',$cur);
            $labels[] = date('d M',$cur);
            $res = $conn->query("SELECT IFNULL(SUM(DonationAmount),0) as amt FROM donations WHERE DATE(DonationDate)='".$d."'");
            $row = $res ? $res->fetch_assoc() : null;
            $data[] = $row ? floatval($row['amt']) : 0;
            $cur = strtotime('+1 day',$cur);
        }
        $response['type']='bar';
        $response['data']=array('labels'=>$labels,'datasets'=>array(array('label'=>'Donations (R)','data'=>$data,'backgroundColor'=>'#ffc107')));
        $response['options']=array('plugins'=>array('title'=>array('display'=>true,'text'=>'Donations')));
    }
    elseif($analytic === 'species'){
        $labels = array(); $data = array();
        $res = $conn->query("SELECT Animal_Type, COUNT(*) as c FROM animal GROUP BY Animal_Type");
        if($res) while($r=$res->fetch_assoc()){ $labels[]=$r['Animal_Type']; $data[]=intval($r['c']); }
        $response['type']='pie';
        // build hsl colors without closures to maximize compatibility
        $bgColors = array();
        for($i=0;$i<count($data);$i++){
            $h = ($i * 47) % 360;
            $bgColors[] = 'hsl('.$h.',70%,55%)';
        }
        $response['data']=array('labels'=>$labels,'datasets'=>array(array('data'=>$data,'backgroundColor'=>$bgColors)));
        $response['options']=array('plugins'=>array('title'=>array('display'=>true,'text'=>'Species Distribution')));
    }
    elseif($analytic === 'gender'){
        $labels = array(); $data = array();
        $res = $conn->query("SELECT Animal_Gender, COUNT(*) as c FROM animal GROUP BY Animal_Gender");
        if($res) while($r=$res->fetch_assoc()){ $labels[]=$r['Animal_Gender']; $data[]=intval($r['c']); }
        $response['type']='doughnut';
        $response['data']=array('labels'=>$labels,'datasets'=>array(array('data'=>$data,'backgroundColor'=>array('#ff6384','#36a2eb'))));
        $response['options']=array('plugins'=>array('title'=>array('display'=>true,'text'=>'Gender Distribution')));
    }
    elseif($analytic === 'age'){
        // Use stored Animal_AgeGroup values where possible: Junior, Adult, Senior
        $labels = array('Junior','Adult','Senior');
        $counts = array(0,0,0);
        $res = $conn->query("SELECT Animal_AgeGroup, COUNT(*) as c FROM animal GROUP BY Animal_AgeGroup");
        if($res){
            while($r = $res->fetch_assoc()){
                $grp = trim(strtolower($r['Animal_AgeGroup']));
                $c = isset($r['c']) ? intval($r['c']) : 0;
                if($grp === 'junior' || $grp === 'juvenile' || $grp === 'puppy' || $grp === 'kitten') $counts[0] += $c;
                elseif($grp === 'adult' || $grp === 'adults') $counts[1] += $c;
                elseif($grp === 'senior' || $grp === 'seniors') $counts[2] += $c;
                else $counts[1] += $c; // unknown -> count as Adult
            }
        }
        $response['type']='bar';
        $response['data']=array('labels'=>$labels,'datasets'=>array(array('label'=>'Count','data'=>$counts,'backgroundColor'=>'#20c997')));
        $response['options']=array('plugins'=>array('title'=>array('display'=>true,'text'=>'Age Groups')));
    }
    elseif($analytic === 'status'){
        $resA=$conn->query("SELECT COUNT(*) as c FROM animal WHERE outtakeType IS NULL");
        $resB=$conn->query("SELECT COUNT(*) as c FROM animal WHERE outtakeType='Adoption'");
        $a=$resA?intval($resA->fetch_assoc()['c']):0; $b=$resB?intval($resB->fetch_assoc()['c']):0;
        $response['type']='doughnut';
        $response['data']=array('labels'=>array('Available','Adopted'),'datasets'=>array(array('data'=>array($a,$b),'backgroundColor'=>array('#ff8c00','#2c3e50'))));
        $response['options']=array('plugins'=>array('title'=>array('display'=>true,'text'=>'Animal Status')));
    }
    elseif($analytic === 'health'){
        $res1=$conn->query("SELECT COUNT(*) as c FROM animal WHERE Animal_HealthStatus='Healthy'");
        $res2=$conn->query("SELECT COUNT(*) as c FROM animal WHERE Animal_HealthStatus IN ('Sick','Injured','Recovering','Under Observation')");
        $h=$res1?intval($res1->fetch_assoc()['c']):0; $i=$res2?intval($res2->fetch_assoc()['c']):0;
        $response['type']='doughnut';
        $response['data']=array('labels'=>array('Healthy','In Treatment'),'datasets'=>array(array('data'=>array($h,$i),'backgroundColor'=>array('#28a745','#dc3545'))));
        $response['options']=array('plugins'=>array('title'=>array('display'=>true,'text'=>'Health Status')));
    }
    elseif($analytic === 'kennel'){
        // Build occupancy per kennel: show occupied vs free (capacity - occupancy)
        $labels = array(); $occupied = array(); $free = array();
        $res = $conn->query("SELECT Kennel_ID, Kennel_Name, Kennel_Capacity, Kennel_Occupancy FROM kennel ORDER BY Kennel_Name ASC");
        if($res){
            while($r = $res->fetch_assoc()){
                $name = !empty($r['Kennel_Name']) ? $r['Kennel_Name'] : $r['Kennel_ID'];
                $cap = isset($r['Kennel_Capacity']) ? intval($r['Kennel_Capacity']) : 0;
                $occ = isset($r['Kennel_Occupancy']) ? intval($r['Kennel_Occupancy']) : 0;
                $freeCount = max(0, $cap - $occ);
                $labels[] = $name;
                $occupied[] = $occ;
                $free[] = $freeCount;
            }
        }
        $response['type']='bar';
        $response['data']=array('labels'=>$labels,'datasets'=>array(
            array('label'=>'Occupied','data'=>$occupied,'backgroundColor'=>'#dc3545','stack'=>'stack1'),
            array('label'=>'Free','data'=>$free,'backgroundColor'=>'#28a745','stack'=>'stack1')
        ));
        $response['options']=array('plugins'=>array('title'=>array('display'=>true,'text'=>'Kennel Occupancy')),'scales'=>array('x'=>array('stacked'=>true),'y'=>array('stacked'=>true)));
    }

    return $response;
}

function build_csv_rows($conn, $analytic, $start=null, $end=null){
    $rows = array();
    if($analytic === 'donations'){
        if(!$start || !$end){ list($s,$e)=dateRangeFromPreset('last30'); $start=$s;$end=$e; }
        $cur = strtotime($start); $endts = strtotime($end);
        $rows[] = array('Date','Donations');
        while($cur <= $endts){
            $d = date('Y-m-d',$cur);
            $res = $conn->query("SELECT IFNULL(SUM(DonationAmount),0) as amt FROM donations WHERE DATE(DonationDate)='".$d."'");
            $row = $res ? $res->fetch_assoc() : null;
            $rows[] = array($d, $row ? $row['amt'] : 0);
            $cur = strtotime('+1 day',$cur);
        }
    }
    elseif($analytic === 'monthlyTrends'){
        $rows[] = array('Month','Intakes','Adoptions');
        for($i=11;$i>=0;$i--){
            $m = date('M Y', strtotime("-{$i} months")); $y = date('Y', strtotime("-{$i} months"));
            $res1 = $conn->query("SELECT COUNT(*) as c FROM animal WHERE MONTH(Animal_RescueDate)=".date('n',strtotime("-{$i} months"))." AND YEAR(Animal_RescueDate)={$y}"); $int = $res1?intval($res1->fetch_assoc()['c']):0;
            $res2 = $conn->query("SELECT COUNT(*) as c FROM animal WHERE outtakeType='Adoption' AND MONTH(outtakeDate)=".date('n',strtotime("-{$i} months"))." AND YEAR(outtakeDate)={$y}"); $ad = $res2?intval($res2->fetch_assoc()['c']):0;
            $rows[] = array($m,$int,$ad);
        }
    }
    elseif($analytic === 'age'){
        $rows[] = array('Age Group','Count');
        $groups = array('Junior'=>0,'Adult'=>0,'Senior'=>0);
        $res = $conn->query("SELECT Animal_AgeGroup, COUNT(*) as c FROM animal GROUP BY Animal_AgeGroup");
        if($res){
            while($r = $res->fetch_assoc()){
                $grp = trim(strtolower($r['Animal_AgeGroup']));
                $c = isset($r['c']) ? intval($r['c']) : 0;
                if($grp === 'junior' || $grp === 'juvenile' || $grp === 'puppy' || $grp === 'kitten') $groups['Junior'] += $c;
                elseif($grp === 'adult' || $grp === 'adults') $groups['Adult'] += $c;
                elseif($grp === 'senior' || $grp === 'seniors') $groups['Senior'] += $c;
                else $groups['Adult'] += $c;
            }
        }
        foreach($groups as $k=>$v) $rows[] = array($k,$v);
    }
    elseif($analytic === 'kennel'){
        $rows[] = array('Kennel Name','Capacity','Occupied','Free');
        $res = $conn->query("SELECT Kennel_ID, Kennel_Name, Kennel_Capacity, Kennel_Occupancy FROM kennel ORDER BY Kennel_Name ASC");
        if($res){
            while($r = $res->fetch_assoc()){
                $name = !empty($r['Kennel_Name']) ? $r['Kennel_Name'] : $r['Kennel_ID'];
                $cap = isset($r['Kennel_Capacity']) ? intval($r['Kennel_Capacity']) : 0;
                $occ = isset($r['Kennel_Occupancy']) ? intval($r['Kennel_Occupancy']) : 0;
                $free = max(0, $cap - $occ);
                $rows[] = array($name, $cap, $occ, $free);
            }
        }
    }
    else {
        // use build_analytics_payload to construct table-like CSV
        $payload = build_analytics_payload($conn, $analytic, $start, $end);
        if(isset($payload['data']['labels']) && isset($payload['data']['datasets'])){
            $labels = $payload['data']['labels']; $ds = $payload['data']['datasets'];
            // build header without using null coalesce
            $hdrLabels = array();
            for($di=0;$di<count($ds);$di++){
                $dItem = $ds[$di];
                $hdrLabels[] = isset($dItem['label']) ? $dItem['label'] : 'Series';
            }
            $hdr = array_merge(array('Label'), $hdrLabels);
            $rows[] = $hdr;
            for($i=0;$i<count($labels);$i++){
                $r = array($labels[$i]);
                for($d=0;$d<count($ds);$d++){
                    $val = isset($ds[$d]['data'][$i]) ? $ds[$d]['data'][$i] : '';
                    $r[] = $val;
                }
                $rows[] = $r;
            }
        } else { $rows[] = array('No data available'); }
    }
    return $rows;
}

?>