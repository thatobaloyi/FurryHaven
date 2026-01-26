<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require('config/databaseconnection.php');
include_once __DIR__ . '/models/Notification.php';

$currentUserID = $_SESSION['username'];
$notification = new Notification();
$unreadNotifications = $notification->getUnread($currentUserID);
$unreadCount = $unreadNotifications ? $unreadNotifications->num_rows : 0;

// Safe query helper
function safeQuery($conn, $sql) {
    $res = $conn->query($sql);
    if ($res) return $res->fetch_assoc()['c'] ?? 0;
    else {
        error_log("Query failed: $sql | Error: ".$conn->error);
        return 0;
    }
}

// Analytics Queries
$totalAnimals   = safeQuery($conn, "SELECT COUNT(*) as c FROM animal");
$adopted        = safeQuery($conn, "SELECT COUNT(*) as c FROM animal WHERE outtakeType='Adoption'");
$available      = safeQuery($conn, "SELECT COUNT(*) as c FROM animal WHERE outtakeType IS NULL");
$healthy        = safeQuery($conn, "SELECT COUNT(*) as c FROM animal WHERE Animal_HealthStatus='Healthy'");
$inTreatment    = safeQuery($conn, "SELECT COUNT(*) as c FROM animal WHERE Animal_HealthStatus IN ('Sick','Injured','Recovering','Under Observation')");
$adoptionRate   = $totalAnimals > 0 ? round(($adopted / $totalAnimals) * 100, 1) : 0;

$currentYear = date('Y');

// Monthly trends
$monthlyData = [];
for ($i = 1; $i <= 12; $i++) {
    $monthName = date("M", mktime(0,0,0,$i,1));
    $intakes = safeQuery($conn, "SELECT COUNT(*) as c FROM animal WHERE MONTH(Animal_RescueDate)=$i AND YEAR(Animal_RescueDate)=$currentYear");
    $adopts = safeQuery($conn, "SELECT COUNT(*) as c FROM animal WHERE outtakeType='Adoption' AND MONTH(outtakeDate)=$i AND YEAR(outtakeDate)=$currentYear");
    $monthlyData[] = ["month"=>$monthName,"intakes"=>$intakes,"adopts"=>$adopts];
}

// Species breakdown
$speciesData = [];
$speciesRes = $conn->query("SELECT Animal_Type, COUNT(*) as c FROM animal GROUP BY Animal_Type");
if($speciesRes) while($row = $speciesRes->fetch_assoc()) $speciesData[] = $row;

// Top species
$topSpecies = "N/A";
if(!empty($speciesData)){
    $topRow = $speciesData[0];
    foreach($speciesData as $row){
        if($row['c'] > $topRow['c']) $topRow = $row;
    }
    $topSpecies = $topRow['Animal_Type'];
}

// Gender breakdown
$genderData = [];
$genderRes = $conn->query("SELECT Animal_Gender, COUNT(*) as c FROM animal GROUP BY Animal_Gender");
if($genderRes) while($row = $genderRes->fetch_assoc()) $genderData[] = $row;

// Age groups — use the stored Animal_AgeGroup values (Junior, Adult, Senior)
$ageGroups = ['Junior'=>0,'Adult'=>0,'Senior'=>0];
$ageRes = $conn->query("SELECT Animal_AgeGroup, COUNT(*) as c FROM animal GROUP BY Animal_AgeGroup");
if($ageRes){
  while($row = $ageRes->fetch_assoc()){
    $grp = trim($row['Animal_AgeGroup']);
    $count = (int)($row['c'] ?? 0);
    if(strcasecmp($grp,'junior') === 0 || strcasecmp($grp,'juniors') === 0 || strcasecmp($grp,'juvenile') === 0){
      $ageGroups['Junior'] += $count;
    } elseif(strcasecmp($grp,'adult') === 0 || strcasecmp($grp,'adults') === 0){
      $ageGroups['Adult'] += $count;
    } elseif(strcasecmp($grp,'senior') === 0 || strcasecmp($grp,'seniors') === 0){
      $ageGroups['Senior'] += $count;
    } else {
      // unknown labels: add to Adult as a safe default
      if($grp !== '') $ageGroups['Adult'] += $count;
    }
  }
}

// Donations per month
$donationData = [];
for ($i = 1; $i <= 12; $i++) {
    $res = $conn->query("SELECT SUM(DonationAmount) as amt FROM donations WHERE MONTH(DonationDate)=$i AND YEAR(DonationDate)=$currentYear");
    $amt = ($res && $row = $res->fetch_assoc()) ? ($row['amt'] ?? 0) : 0;
    $donationData[] = $amt;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Analytics Dashboard</title>
<link rel="stylesheet" href="style2.css">
<link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Chart.js DataLabels plugin to show values on charts (visible in exports/prints) -->
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
<!-- jsPDF for client-side PDF export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<style>
  .hidden { display: none !important; }
  /* GLOBAL STYLES */
body {
  font-family: 'Segoe UI', Arial, sans-serif;
  background: #f8fafc; /* lighter neutral */
  color: #333;
  margin: 0;
}

.dashboard-container {
  display: flex;
  min-height: 100vh;
}

/* MAIN CONTENT */
.main-content {
  flex: 1;
  padding: 2rem;
}

/* HEADING */
h1 {
  color: #003366;
  text-align: center;
  border-bottom: 4px solid #FF8C00;
  padding-bottom: 0.5rem;
  font-size: 2.0rem;
  font-family: 'Lexend', sans-serif;
}

blockquote {
  font-style: italic;
  text-align: center;
  color: #003366;
  font-size: 1.1rem;
  margin: 1.5rem 0;
}

/* SUMMARY CARDS */
.cards {
  display: flex;
  justify-content: center;       /* centers the whole row */
  align-items: center;
  flex-wrap: wrap;               /* so it wraps nicely on small screens */
  gap: 30px;                     /* space between circles */
  margin: 40px auto;
}

/* Circle Cards */
.card {
  width: 150px;                  /* circle width */
  height: 150px;                 /* circle height */
  background: #fff;
  border: 4px solid #df7100;       /* orange border */
  border-radius: 50%;             /* makes it a circle */
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
  cursor: pointer;
}

.card h2 {
  margin: 0;
  font-size: 1.8rem;
  color: #18436e;
  font-weight: bold;
}

/* Dynamic report area styling */
#reportArea {
  max-width: 1100px;
  margin: 20px auto;
  padding: 16px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 6px 18px rgba(0,0,0,0.06);
}

#dynamicChart {
  width: 100% !important;
  height: 1000px !important;
  display: block;
}

.card p {
  margin: 5px 0 0;
  font-size: 0.9rem;
  color: #18436e;
}

/* CHARTS */
.charts {
  display: flex;
  flex-direction: column;
  gap: 6rem; /* space between rows */
  margin-top: 2rem;
}

.charts canvas {
  background: #fff;
  padding: 1rem;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  width: 100% !important;
  height: 500px;
  max-height: 600px;
}

.chart-row {
  display: flex;
  justify-content: center; /* center charts in the row */
  gap: 2rem;               /* space between individual charts */
  flex-wrap: wrap;         /* allow charts to wrap on small screens */
}

.chart-row canvas {
  background: #fff;
  border-radius: 12px;
  padding: 1rem;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  width: 100%;           /* take full width of container */
  max-width: 500px;      /* limit maximum width */
  height: 450px;          /* fixed height for better readability */
}

/* Donations card heading */
.card.donation h3 {
  color:#18436e; /* bright blue */
}

.cards .card {
  width: 130px;  /* smaller width */
  height: 130px; /* smaller height */
}

/* MOBILE TWEAKS */
@media (max-width: 700px) {
  .main-content {
    padding: 1rem;
  }
  .card h2 {
    font-size: 1.5rem;
  }
}

</style>
</head>
<body>
<div class="dashboard-container">
  <?php include 'sidebar2.php'; ?>


  <div class="main-content">
    <h1>Analytics</h1>
    <blockquote>"Record every rescue — every life matters."</blockquote>

    <!-- Controls: analytic selector, timeframe, actions -->
    <div class="analytics-controls" style="display:flex;gap:12px;align-items:center;justify-content:center;margin:20px 0;flex-wrap:wrap;">
      <label style="display:flex;flex-direction:column;align-items:flex-start;">
        Select Analytic
        <select id="analyticSelect">
          <option value="monthlyTrends">Monthly Intakes vs Adoptions</option>
          <option value="donations">Donations</option>
          <option value="species">Species Distribution</option>
          <option value="gender">Gender Distribution</option>
          <option value="kennel">Kennel Occupancy</option>
          <option value="age">Age Groups</option>
          <option value="status">Animal Status</option>
          <option value="health">Health Status</option>
        </select>
      </label>

      <label style="display:flex;flex-direction:column;align-items:flex-start;">
        Preset
        <select id="presetRange">
          <option value="last7">Last 7 days</option>
          <option value="last30">Last 30 days</option>
          <option value="ytd">Year to date</option>
          <option value="custom">Custom</option>
        </select>
      </label>

      <label id="customRange" style="display:none;flex-direction:column;align-items:flex-start;">
        From
        <input type="date" id="startDate">
        To
        <input type="date" id="endDate">
      </label>

      <div style="display:flex;gap:8px;align-items:center;">
        <button id="btnApply">Apply</button>
        <label style="display:flex;align-items:center;gap:6px;margin-left:8px;"><input type="checkbox" id="toggleValues" checked> Show values</label>
        <button id="btnPrint">Print</button>
        <button id="btnExport">Export CSV</button>
        <button id="btnExportPdf">Export PDF</button>
        <input id="emailAddress" type="email" placeholder="email@domain.com" style="padding:6px;border-radius:6px;border:1px solid #ccc;">
        <button id="btnEmail">Email CSV</button>
        <button id="btnEmailPdf">Email PDF</button>
      </div>
    </div>

    <!-- Summary Cards -->
    <div class="cards hidden">
      <div class="card" data-analytics="status"><h2><?php echo $totalAnimals; ?></h2><p>Total Animals</p></div>
      <div class="card" data-analytics="status"><h2><?php echo $adopted; ?></h2><p>Adopted</p></div>
      <div class="card" data-analytics="status"><h2><?php echo $available; ?></h2><p>Available</p></div>
      <div class="card" data-analytics="health"><h2><?php echo $healthy; ?></h2><p>Healthy</p></div>
      <div class="card" data-analytics="health"><h2><?php echo $inTreatment; ?></h2><p>In Treatment</p></div>
      <div class="card" data-analytics="status"><h2><?php echo $adoptionRate; ?>%</h2><p>Adoption Rate</p></div>
      <div class="card donation" data-analytics="donations"><h3>Donations This Year</h3><p>R<?php echo number_format(array_sum($donationData)); ?></p></div>
    </div>


    <!-- Dynamic chart area -->
    <div id="reportArea" class="hidden">
      <canvas id="dynamicChart"></canvas>
    </div>
</div>

<script>
// Register DataLabels plugin and set sensible defaults
if(window.Chart){
  try{
    Chart.register(window['chartjs-plugin-datalabels']);
  }catch(e){ /* already registered or plugin missing */ }
  // global defaults for datalabels
  if(Chart.defaults && Chart.defaults.plugins){
    Chart.defaults.plugins.datalabels = {
      color: '#222',
      anchor: 'end',
      align: 'end',
      font: { weight: '600', size: 12 },
      formatter: function(value){
        if(value === null || value === undefined) return '';
        if(typeof value === 'number'){
          // format large numbers with comma separators
          return value.toLocaleString();
        }
        return value;
      }
    };
  }
}
// Analytics client behavior
(function(){
  var currentChart = null;
  var ctxEl = document.getElementById('dynamicChart');
  if(!ctxEl) return; // safety
  var ctx = ctxEl.getContext('2d');

  function showCustomRange(show){
    document.getElementById('customRange').style.display = show ? 'flex' : 'none';
  }

  var preset = document.getElementById('presetRange');
  if(preset) preset.addEventListener('change', function(e){ showCustomRange(e.target.value === 'custom'); });

  function buildParams(){
    var analytic = document.getElementById('analyticSelect').value;
    var preset = document.getElementById('presetRange').value;
    var params = { analytic: analytic, preset: preset };
    if (preset === 'custom'){
      var s = document.getElementById('startDate').value;
      var t = document.getElementById('endDate').value;
      if(s) params.start = s;
      if(t) params.end = t;
    }
    return params;
  }

  function fetchDataAndRender(){
    var params = buildParams();
    var q = Object.keys(params).map(k=>encodeURIComponent(k)+'='+encodeURIComponent(params[k]||'')).join('&');
    fetch('analytics_data.php?'+q).then(r=>r.json()).then(data=>{ renderChart(data); }).catch(err=>{ console.error('analytics fetch error', err); alert('Failed to fetch analytics data'); });
  }

  // helper: return a dataURL from the chart canvas with a white background to avoid dark/black transparency in JPEG exports
  function getOpaqueDataURL(srcCanvas, mime, quality){
    mime = mime || 'image/png';
    quality = quality || 1.0;
    var w = srcCanvas.width || srcCanvas.offsetWidth;
    var h = srcCanvas.height || srcCanvas.offsetHeight;
    var tmp = document.createElement('canvas');
    tmp.width = w; tmp.height = h;
    var tctx = tmp.getContext('2d');
    // fill white background
    tctx.fillStyle = '#ffffff';
    tctx.fillRect(0,0,w,h);
    // draw the source canvas onto it
    tctx.drawImage(srcCanvas, 0, 0, w, h);
    return tmp.toDataURL(mime, quality);
  }

  function renderChart(payload){
    try{ if(currentChart) currentChart.destroy(); }catch(e){}
    // Set global Chart defaults for consistent styling
    Chart.defaults.font.family = 'Lexend, "Segoe UI", Arial, sans-serif';
    Chart.defaults.color = '#18436e';
    Chart.defaults.plugins.legend.labels.color = '#18436e';
    Chart.defaults.plugins.title.color = '#003366';

    // system color palette (matches site theme)
    var themePalette = ['#FF8C00','#003366','#17a2b8','#6f42c1','#20c997','#ffc107','#dc3545','#2c3e50'];
    var cfg = { type: payload.type || 'bar', data: payload.data || {labels:[],datasets:[]}, options: payload.options || {} };
    // ensure datasets have colors that match theme when not specified
    var showValues = document.getElementById('toggleValues') && document.getElementById('toggleValues').checked;
    if(cfg.data && Array.isArray(cfg.data.datasets)){
      cfg.data.datasets.forEach(function(ds, i){
        if(!ds.backgroundColor){
          // choose color from palette
          var c = themePalette[i % themePalette.length];
          if(cfg.type === 'line' || ds.type === 'line'){
            ds.borderColor = ds.borderColor || c;
            // subtle fill for area/line
            var rgbaFill = c.replace('#','');
            ds.backgroundColor = ds.backgroundColor || 'rgba('+parseInt(c.slice(1,3),16)+','+parseInt(c.slice(3,5),16)+','+parseInt(c.slice(5,7),16)+',0.06)';
          } else {
            // bars and doughnuts get a stronger, slightly transparent fill
            var r = parseInt(c.slice(1,3),16), g = parseInt(c.slice(3,5),16), b = parseInt(c.slice(5,7),16);
            ds.backgroundColor = ds.backgroundColor || 'rgba('+r+','+g+','+b+',0.92)';
            ds.borderColor = ds.borderColor || 'rgba('+r+','+g+','+b+',1)';
          }
        }
        // enable datalabels only if toggle enabled and chart is not a crowded line
        if(showValues){
          var isLine = (cfg.type === 'line' || ds.type === 'line');
          var tooManyPoints = (cfg.data.labels && cfg.data.labels.length > 20);
          ds.datalabels = ds.datalabels === undefined ? { display: !isLine || !tooManyPoints } : ds.datalabels;
        } else {
          ds.datalabels = { display: false };
        }
      });
    }
    currentChart = new Chart(ctx, cfg);
  }

  document.getElementById('btnPrint').addEventListener('click', function(){
    if(!currentChart){ alert('No chart to print. Apply a selection first.'); return; }
    var canvas = document.getElementById('dynamicChart');
    var url = getOpaqueDataURL(canvas, 'image/png');
    var w = window.open('','_blank');
    w.document.write('<img src="'+url+'" style="max-width:100%;"/>');
    w.document.close(); w.focus(); w.print();
  });

  document.getElementById('btnExport').addEventListener('click', function(){
    var params = buildParams(); var q = Object.keys(params).map(k=>encodeURIComponent(k)+'='+encodeURIComponent(params[k]||'')).join('&');
    window.location = 'analytics_export.php?'+q;
  });

  // Export PDF using jsPDF (chart canvas -> image -> PDF)
  function generatePdfBlob(callback){
    var canvas = document.getElementById('dynamicChart');
    if(!canvas){ alert('Nothing to export'); return; }
    // create an opaque JPEG dataURL so transparency doesn't become black in the exported JPEG
    var dataURL = getOpaqueDataURL(canvas, 'image/jpeg', 1.0);
    // minimal jsPDF usage without external libs: create a PDF with img
    if(window.jspdf && window.jspdf.jsPDF){
      var pdf = new jspdf.jsPDF('landscape');
      var imgProps = pdf.getImageProperties(dataURL);
      var pdfWidth = pdf.internal.pageSize.getWidth();
      var pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
      pdf.addImage(dataURL, 'JPEG', 0, 10, pdfWidth, pdfHeight);
      var blob = pdf.output('blob');
      callback(blob);
    } else {
      // fallback: create a tiny HTML and use window.print as fallback
      alert('PDF export requires jsPDF. Please include jsPDF to enable PDF export.');
    }
  }

  document.getElementById('btnExportPdf').addEventListener('click', function(){
    // trigger download
    generatePdfBlob(function(blob){
      var url = URL.createObjectURL(blob);
      var a = document.createElement('a'); a.href = url; a.download = 'analytics_'+(new Date()).toISOString().replace(/[:.]/g,'_')+'.pdf'; document.body.appendChild(a); a.click(); a.remove();
      setTimeout(()=>URL.revokeObjectURL(url), 5000);
    });
  });

  // Email PDF: generate blob, base64-encode, send to server
  document.getElementById('btnEmailPdf').addEventListener('click', function(){
    var email = document.getElementById('emailAddress').value.trim(); if(!email){ alert('Please enter an email'); return; }
    generatePdfBlob(function(blob){
      var reader = new FileReader(); reader.onload = function(){
        var base64 = reader.result.split(',')[1];
        var params = buildParams(); params.email = email; params.pdf_base64 = base64; params.pdf_name = 'analytics_'+(new Date()).toISOString().replace(/[:.]/g,'_')+'.pdf';
        fetch('analytics_email.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(params) })
          .then(r=>r.json()).then(j=>{ if(j.success) alert('Email sent'); else alert('Email failed: '+(j.error||'unknown')); }).catch(e=>{ console.error(e); alert('Email request failed'); });
      };
      reader.readAsDataURL(blob);
    });
  });

  document.getElementById('btnEmail').addEventListener('click', function(){
    var email = document.getElementById('emailAddress').value.trim();
    if(!email){ alert('Please enter an email address'); return; }
    var params = buildParams(); params.email = email;
    fetch('analytics_email.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(params) })
      .then(r=>r.json()).then(j=>{ if(j.success) alert('Email sent'); else alert('Email failed: '+(j.error||'unknown')); }).catch(err=>{ console.error(err); alert('Email request failed'); });
  });

  // Only render when user clicks Apply; reveal report area then fetch the generated analytic
  document.getElementById('btnApply').addEventListener('click', function(){
    var report = document.getElementById('reportArea');
    if(report) report.classList.remove('hidden');
    fetchDataAndRender();
  });

})();
</script>

<script>
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        title: { display: true, font: { size: 18, weight: '600' }, color: '#003366' },
        legend: { labels: { font: { size: 14 } } }
    }
};

// Static small charts removed — dynamic report (canvas #dynamicChart) is the single source of generated analytics.
</script>

</body>
</html>

<script src="sidebar2.js"></script>
