<?php
$api_key = getenv('FLICKR_API_KEY') ?: require __DIR__ . '/config.php';

if (!empty($_GET['lookup']) || !empty($_GET['url'])) {
    header('Content-Type: application/json');
    $base = 'https://api.flickr.com/services/rest/?api_key=' . $api_key . '&format=json&nojsoncallback=1';
    if (!empty($_GET['url'])) {
        $url = $base . '&method=flickr.urls.lookupUser&url=' . urlencode($_GET['url']);
    } else {
        $url = $base . '&method=flickr.people.findByUsername&username=' . urlencode($_GET['lookup']);
    }
    echo file_get_contents($url);
    exit;
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Flickr Username → User ID</title>
<link rel="stylesheet" href="https://tools-static.wmflabs.org/cdnjs/ajax/libs/twitter-bootstrap/4.0.0/css/bootstrap.min.css">
<style>
  body { background: #f8f9fa; }
  .idgettr-wrap { max-width: 520px; margin: 2rem auto; }

  /* ── Input form panel ─────────────────────── */
  .input-form {
    background: #fff;
    border: 1px solid #d0d7de;
    border-top: 3px solid #36c;
    border-radius: 2px;
    padding: 16px;
    margin-bottom: 8px;
  }

  /* ── Status message ───────────────────────── */
  .idgettr-message {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    border: 1px solid #a2a9b1;
    border-radius: 2px;
    background: #fff;
    color: #202122;
  }
  .idgettr-message--progressive { background: #fff; border-color: #a2a9b1; }
  .idgettr-message--error { border-inline-start: 3px solid #b32424; }
  .idgettr-message--error .idgettr-message__icon { color: #b32424; }
  .idgettr-message__icon {
    display: inline-flex;
    flex: 0 0 20px;
    height: 20px;
    align-items: center;
    color: #36c;
  }
  .idgettr-message__icon svg { fill: currentColor; }
  .idgettr-message__content {
    flex: 1;
    min-width: 0;
    font-size: .875rem;
    line-height: 1.4;
  }
  .idgettr-message__text { display: block; width: 100%; }

  /* ── Logo ─────────────────────────────────── */
  .idgettr-logo {
    font-family: Georgia, "Times New Roman", serif;
    font-weight: 700;
    font-size: 1.5rem;
    margin-bottom: 1rem;
    color: #202122;
  }
  .idgettr-logo .text-muted { font-weight: 400; }

  /* ── Copy control ─────────────────────────── */
  .idgettr-copy-label {
    display: block;
    margin-bottom: 4px;
    font-weight: 700;
    font-size: 13px;
    color: #202122;
  }
  .idgettr-copy-control {
    display: flex;
    align-items: stretch;
    width: 100%;
  }
  .idgettr-copy-input {
    flex: 1;
    min-width: 0;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    box-sizing: border-box;
    border: 1px solid #a2a9b1;
    border-right: 0;
    background: #f8f9fa;
    border-radius: 2px 0 0 2px;
    font-family: monospace;
    height: 32px;
    padding: 5px 8px;
    font-size: 13px;
    line-height: 1.4;
    outline: 0;
  }
  .idgettr-copy-button {
    flex: 0 0 auto;
    margin-left: -1px;
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    height: 32px;
    padding: 0 12px;
    box-sizing: border-box;
    border: 1px solid #a2a9b1;
    background: #f8f9fa;
    border-radius: 0 2px 2px 0;
    color: #202122;
    cursor: pointer;
    font-size: 13px;
    font-weight: 700;
  }
  .idgettr-copy-button:hover { background: #fff; }
  .idgettr-copied { display: none; color: #14866d; font-size: .875rem; margin-top: 4px; }
  .idgettr-copied--show { display: block; }

  /* ── Footer ────────────────────────────────── */
  .idgettr-footer { margin-top: 2rem; font-size: .8rem; color: #72777d; text-align: center; }
  .idgettr-footer a { color: #36c; }
</style>
</head>
<body>
<div class="idgettr-wrap">
  <h1 class="idgettr-logo">Flickr <span class="cdx-icon cdx-icon--medium"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" aria-hidden="true"><path d="M18 9.804v1.392l-5.688 5.883-1.436-1.39L14.93 11.5H1v-2h13.923l-4.047-4.165 1.434-1.394z"></path></svg></span> User ID</h1>
  <div class="input-form mt-3">
    <form id="lookupForm" class="mb-0">
      <div class="form-group mb-3">
        <label for="username" class="idgettr-copy-label">Flickr username or profile URL</label>
        <div class="d-flex" style="gap:8px">
          <input type="text" id="username" class="form-control" autofocus>
          <button type="submit" class="btn btn-primary" style="font-weight:700;white-space:nowrap">Look up</button>
        </div>
      </div>
    </form>
    <div id="result" class="idgettr-message" style="display:none">
      <span class="idgettr-message__icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" aria-hidden="true"><path d="M19.765 16.059 18 19H2L.235 16.059l8-15h3.53zM9 14v2h2v-2zm0-8v6h2V6z"></path></svg></span>
      <span class="idgettr-message__content"><span class="idgettr-message__text" id="resultText"></span></span>
    </div>
    <div id="output" style="display:none">
      <label for="nsidInput" class="idgettr-copy-label">Flickr user ID</label>
      <div class="idgettr-copy-control">
        <input type="text" id="nsidInput" class="idgettr-copy-input" readonly>
        <button id="copyBtn" class="idgettr-copy-button" title="Copy to clipboard">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" aria-hidden="true" style="fill:currentColor"><path d="M3 3h8v2h2V3c0-1.1-.895-2-2-2H3c-1.1 0-2 .895-2 2v8c0 1.1.895 2 2 2h2v-2H3z"/><path d="M9 9h8v8H9zm0-2c-1.1 0-2 .895-2 2v8c0 1.1.895 2 2 2h8c1.1 0 2-.895 2-2V9c0-1.1-.895-2-2-2z"/></svg>
          <span>Copy</span>
        </button>
      </div>
      <span id="copiedMsg" class="idgettr-copied">Copied!</span>
      <a id="profileUrl" target="_blank" class="small d-inline-flex align-items-center mt-1">
        Open Flickr profile
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 20 20" aria-hidden="true" class="ml-1" style="fill:currentColor"><path d="M8 7H3v10h10v-5h2v7H1V5h7z"></path><path d="M19.001 2 19 8h-2V4.415l-9 9L6.586 12l9-8.999L12 3V1h6.001z"></path></svg>
      </a>
    </div>
  </div>
  <footer class="idgettr-footer">
    <a href="https://github.com/VDK/idGettr">idGettr</a> · made by <a href="https://github.com/VDK">VDK</a> · <a href="https://github.com/VDK/idGettr/issues">report an issue</a>
  </footer>
</div>
<script>
document.getElementById('lookupForm').addEventListener('submit', function (e) {
  e.preventDefault();
  var raw = document.getElementById('username').value.trim();
  var resultEl = document.getElementById('result');
  var resultText = document.getElementById('resultText');
  var outputEl = document.getElementById('output');
  if (!raw) return;

  var isUrl = /flickr\.com\//i.test(raw);
  var username = isUrl ? '' : raw;

  resultEl.style.display = 'flex';
  resultEl.className = 'idgettr-message idgettr-message--progressive';
  resultEl.querySelector('.idgettr-message__icon').style.display = 'none';
  resultText.textContent = 'Looking up\u2026';
  outputEl.style.display = 'none';

  var url = isUrl
    ? '?url=' + encodeURIComponent(raw)
    : '?lookup=' + encodeURIComponent(username);

  fetch(url)
    .then(function (r) { return r.json(); })
    .then(function (data) {
      var nsid = data.user && (data.user.nsid || data.user.id);
      if (data.stat === 'ok' && nsid) {
        document.getElementById('nsidInput').value = nsid;
        document.getElementById('profileUrl').href = 'https://www.flickr.com/people/' + nsid + '/';
        resultEl.style.display = 'none';
        outputEl.style.display = 'block';
      } else {
        resultEl.className = 'idgettr-message idgettr-message--error';
        resultEl.querySelector('.idgettr-message__icon').style.display = '';
        resultText.textContent = 'User not found.';
        outputEl.style.display = 'none';
      }
    })
    .catch(function () {
      resultEl.className = 'idgettr-message idgettr-message--error';
      resultEl.querySelector('.idgettr-message__icon').style.display = '';
      resultText.textContent = 'Request failed. Check your connection.';
      outputEl.style.display = 'none';
    });
});

document.getElementById('copyBtn').addEventListener('click', function () {
  var nsid = document.getElementById('nsidInput').value;
  if (!nsid) return;
  navigator.clipboard.writeText(nsid).then(function () {
    var msg = document.getElementById('copiedMsg');
    msg.classList.add('idgettr-copied--show');
    setTimeout(function () { msg.classList.remove('idgettr-copied--show'); }, 1800);
  });
});
</script>
</body>
</html>
