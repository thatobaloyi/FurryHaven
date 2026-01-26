<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/databaseconnection.php';

include_once __DIR__ . '/models/Adoption.php';
include_once __DIR__ . '/models/Foster.php';

include_once './notification.php';

$adopter = new Adoption();
$foster = new Foster();

// Fetch approved adopters

$adoptersResult = $adopter->findAll();



$approvedFosterers = $foster->readAll();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approved Adopters and Fosterers</title>
    <link rel="stylesheet" href="style2.css">
</head>

<body>

    <div class="dashboard-container">
        <?php include('sidebar2.php'); ?>
        <div class="main-content" id="mainContent" style="padding-top: 5em;">

            <div class="card">

                <h1>Approved Adopters</h1>
                <table>
                    <thead>
                            <tr>
                                <th>Name</th>
                                <th>Animal</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <!-- <th>Address</th> -->
                                <th>Action</th>
                                <th>Social</th>
                            </tr>
                    </thead>
                    <tbody>
                        <?php if ($adoptersResult->num_rows > 0): ?>
                            <?php while ($adopterData = $adoptersResult->fetch_assoc()): ?>
                                <?php
                                    // attempt to fetch the animal's name for display in the modal
                                    $animalName = '';
                                    if (!empty($adopterData['AnimalID'])){
                                        $aid = $conn->real_escape_string($adopterData['AnimalID']);
                                        $ra = $conn->query("SELECT Animal_Name FROM animal WHERE Animal_ID='".$aid."' LIMIT 1");
                                        if($ra && $rowA = $ra->fetch_assoc()) $animalName = $rowA['Animal_Name'];
                                    }
                                    // attach AnimalName into the adopterData passed to JS
                                    $adopterData['AnimalName'] = $animalName;
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($adopterData['FirstName'] . ' ' . $adopterData['LastName']); ?></td>
                                    <td><?php echo htmlspecialchars($adopterData['AnimalName'] ?: $adopterData['AnimalID']); ?></td>
                                    <td><?php echo htmlspecialchars($adopterData['email']); ?></td>
                                    <td><?php echo htmlspecialchars($adopterData['phone']); ?></td>
                                    <!-- <td><?php echo htmlspecialchars($adopterData['AddressLine1'] . ', ' . $adopterData['City']); ?></td> -->
                                    <td>
                                        <button class="action-btn" onclick="openFollowUpModal('<?php echo $adopterData['AdopterID']; ?>', '<?php echo $adopterData['FollowUpNotes']; ?>')">Add Follow-Up</button>
                                    </td>
                                    <td>
                                        <button class="action-btn" style="margin-left:6px;" onclick="openFacebookModal(<?php echo htmlspecialchars(json_encode($adopterData)); ?>)">Post to Facebook</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">No approved adopters found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="card">

                <h1 style="margin-top: 2rem;">Approved Fosterers</h1>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Animal ID</th>
                            <th>Duration</th>
                            <th>Approved By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($approvedFosterers->num_rows>0): ?>
                            <?php while($fosterData = $approvedFosterers->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($fosterData['fosterer']); ?></td>
                                    <td><?php echo htmlspecialchars($fosterData['animalID']); ?></td>
                                    <td><?php echo htmlspecialchars($fosterData['fosterDuration']); ?></td>
                                    <td><?php echo htmlspecialchars($fosterData['approvedBy']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">No approved fosterers found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="./sidebar2.js"></script>

    <!-- Follow-Up Notes Modal -->
    <div id="followUpModal" style="display:none; position:fixed; z-index:2500; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.4);">
        <div class="modal-content" style="background:#FFF8F0; border:3px solid #FF8C00; border-radius:15px; box-shadow:0 8px 25px rgba(0,0,0,0.25); max-width:500px; margin:5% auto; padding:24px; position:relative;">
            <span id="closeFollowUpModal" style="cursor:pointer; float:right; font-size:1.5em;">&times;</span>
            <h3 style="text-align:center; color:#18436e; border-bottom:2px solid #FF8C00; padding-bottom:0.5rem;">Add Follow-Up Note</h3>
            <form id="followUpForm" method="POST" action="./controllers/AdoptionController.php">
                <input type="hidden" name="adopter_id" id="followUpAdopterId">
                <input type="hidden" name="action" value="followup">
                <label style="font-weight:600; color:#18436e;">Note:</label>
                <textarea name="note" id="followUpNote" rows="5" style="width:100%; border-radius:8px; border:1px solid #ccc; margin-bottom:16px; padding:8px;" required></textarea>
                <div class="button-container" style="display:flex; justify-content:center; gap:10px;">
                    <button type="submit" class="savebtn" style="background-color:#98b06f;">Save Note</button>
                    <button type="button" class="deletebtn" id="cancelFollowUpBtn" style="background-color:#df7100;">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function openFollowUpModal(adopterId, followUp) {
            document.getElementById('followUpAdopterId').value = adopterId;
            document.getElementById('followUpNote').value = followUp;
            document.getElementById('followUpModal').style.display = 'block';
        }
        document.getElementById('closeFollowUpModal').onclick = function() {
            document.getElementById('followUpModal').style.display = 'none';
        };
        document.getElementById('cancelFollowUpBtn').onclick = function() {
            document.getElementById('followUpModal').style.display = 'none';
        };
        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == document.getElementById('followUpModal')) {
                document.getElementById('followUpModal').style.display = 'none';
            }
        };
    </script>

        <!-- Facebook Post Modal -->
        <div id="facebookModal" style="display:none; position:fixed; z-index:2600; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
            <div class="modal-content" style="background:#FFF8F0; border:3px solid #FF8C00; border-radius:15px; box-shadow:0 8px 25px rgba(0,0,0,0.25); max-width:780px; margin:3% auto; padding:20px; position:relative; max-height:80vh; overflow:auto;">
                <span id="closeFacebookModal" style="cursor:pointer; float:right; font-size:1.5em;">&times;</span>
                <h3 style="text-align:center; color:#18436e; border-bottom:2px solid #1877f2; padding-bottom:0.5rem;">Post Adoption to Facebook</h3>
                <div style="display:flex; gap:16px;">
                    <div style="flex:1;">
                        <label style="font-weight:600; color:#18436e;">Message</label>
                        <textarea id="fbMessage" rows="8" style="width:100%; border-radius:8px; border:1px solid #ccc; padding:8px;"></textarea>
                        <div id="fbImagePreview" style="display:flex;gap:8px;margin-top:12px;flex-wrap:wrap;"></div>
                    </div>
                    <div style="width:320px;">
                        <label style="font-weight:600;color:#18436e;display:block;">Preview</label>
                        <div id="fbPreviewBox" style="border:1px solid #eee;padding:8px;border-radius:8px;min-height:200px;background:#fafafa;overflow:auto;"></div>
                    </div>
                </div>
                <div style="display:flex;justify-content:center;gap:10px;margin-top:16px;">
                    <button id="fbSendBtn" class="savebtn" style="background-color:#98b06f;">Post</button>
                    <button id="fbCancelBtn" class="deletebtn" style="background-color:#df7100;">Cancel</button>
                </div>
            </div>
        </div>

        <script>
document.addEventListener('DOMContentLoaded', function () {
  window.currentAdopterData = null;

  // expose globally so inline onclick handlers can call it safely
  window.openFacebookModal = function(adopterJson){
    try {
      window.currentAdopterData = (typeof adopterJson === 'string') ? JSON.parse(adopterJson) : adopterJson || {};
      const animalName = window.currentAdopterData.AnimalName || window.currentAdopterData.Animal_Name || '';
      const name = (window.currentAdopterData.FirstName ? window.currentAdopterData.FirstName + ' ' : '') + (window.currentAdopterData.LastName || '');
      const adoptedTarget = animalName || 'their adopted animal';
      const pre = `🎉 Adoption Announcement! \n\nWe're happy to announce that ${name} has adopted ${adoptedTarget}. Welcome to their new home!\n\nIf you have a nice photo, add it below and then press Send.`;
      const fbMessageEl = document.getElementById('fbMessage');
      const fbPreviewBox = document.getElementById('fbPreviewBox');
      const fbImagePreview = document.getElementById('fbImagePreview');

      if (fbMessageEl) fbMessageEl.value = pre;
      if (fbImagePreview) fbImagePreview.innerHTML = '';
      if (fbPreviewBox) fbPreviewBox.innerHTML = '<strong>' + escapeHtml(name) + '</strong><br/>' + (animalName ? 'Animal: ' + escapeHtml(animalName) : 'Animal: their adopted animal') + '<br/><br/>' + pre.replace(/\n/g,'<br/>');

      const modal = document.getElementById('facebookModal');
      if (modal) modal.style.display = 'block';
    } catch (err) {
      console.error('openFacebookModal error:', err);
      alert('Could not open Facebook modal (see console).');
    }
  };

  // helper: simple escape
  function escapeHtml(s){ return String(s||'').replace(/[&<>"']/g, function(m){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]; }); }

  // Attach image input listener if present (optional)
  const fbImagesEl = document.getElementById('fbImages');
  if (fbImagesEl) {
    fbImagesEl.addEventListener('change', function (ev) {
      try {
        const files = ev.target.files || [];
        const preview = document.getElementById('fbImagePreview');
        const right = document.getElementById('fbPreviewBox');
        if (preview) preview.innerHTML = '';
        if (right) right.innerHTML = '';
        for (let i = 0; i < files.length; i++) {
          const f = files[i];
          if (!f.type.startsWith('image/')) continue;
          const reader = new FileReader();
          reader.onload = function (e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.width = '140px';
            img.style.height = 'auto';
            img.style.borderRadius = '8px';
            img.style.objectFit = 'cover';
            if (preview) preview.appendChild(img);
            if (right) {
              const img2 = document.createElement('img');
              img2.src = e.target.result;
              img2.style.width = '100%';
              img2.style.marginTop = '8px';
              right.appendChild(img2);
            }
          };
          reader.readAsDataURL(f);
        }
      } catch (err) { console.error('fbImages change error', err); }
    });
  }

  // Send button handling (guarded)
  const fbSendBtn = document.getElementById('fbSendBtn');
  if (fbSendBtn) {
    fbSendBtn.addEventListener('click', function () {
      try {
        const messageEl = document.getElementById('fbMessage');
        const message = messageEl ? messageEl.value : '';
        const imgs = [];
        const previewImgs = document.getElementById('fbImagePreview');
        if (previewImgs) previewImgs.querySelectorAll('img').forEach(im => imgs.push(im.src));

        const payload = { adopter: window.currentAdopterData || null, message: message, images: imgs };

        if (!confirm('Post this message to Facebook via Zapier?')) return;

        fbSendBtn.disabled = true;
        const originalText = fbSendBtn.textContent;
        fbSendBtn.textContent = 'Posting...';

        fetch('zapier_forward.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        }).then(function (res) {
          if (!res.ok) return res.text().then(t => { throw new Error('Upstream error ' + res.status + ': ' + t); });
          return res.json().catch(() => ({ ok: true }));
        }).then(function () {
          alert('Posted to Zapier successfully.');
          const modal = document.getElementById('facebookModal');
          if (modal) modal.style.display = 'none';
        }).catch(function (err) {
          console.error('Zapier post failed', err);
          alert('Failed to post: ' + err.message);
        }).finally(function () {
          fbSendBtn.disabled = false;
          fbSendBtn.textContent = originalText;
        });
      } catch (err) {
        console.error('fbSendBtn click error', err);
        alert('Failed to post (see console).');
        fbSendBtn.disabled = false;
      }
    });
  }

  // Close controls
  const closeFacebookModalBtn = document.getElementById('closeFacebookModal');
  if (closeFacebookModalBtn) closeFacebookModalBtn.addEventListener('click', function () { const m = document.getElementById('facebookModal'); if (m) m.style.display = 'none'; });
  const fbCancelBtn = document.getElementById('fbCancelBtn');
  if (fbCancelBtn) fbCancelBtn.addEventListener('click', function () { const m = document.getElementById('facebookModal'); if (m) m.style.display = 'none'; });

  // close when clicking outside
  window.addEventListener('click', function (ev) {
    const modal = document.getElementById('facebookModal');
    if (modal && ev.target === modal) modal.style.display = 'none';
  });

  // Debug: log if openFacebookModal is available
  console.log('openFacebookModal ready', typeof window.openFacebookModal);
});
</script>
</body>
</html>