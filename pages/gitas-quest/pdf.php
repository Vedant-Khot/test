<?php
require_once '../../includes/header.php';
// require_once '../../includes/config.php';
// check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = 'Please log in to view the PDF.';
    header('Location: ' . BASE_URL . 'pages/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Custom PDF Viewer</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
  <style>
    #pdf-render {
      border: 1px solid #ccc;
      width: 100%;
      height: 100%;
      overflow: auto;
    }
    #controls {
      margin-bottom: 10px;
    }
    button {
      padding: 5px 10px;
      margin-right: 5px;
    }
  </style>
</head>
<body>
  <div id="wrapper">
    <!-- Thumbnails sidebar -->
    <aside id="sidebar"></aside>

    <div id="main">
      <!-- Controls / search / zoom -->
      <div id="topbar">
        <button id="prev">‹</button>
        <span id="page-num">1</span> / <span id="page-count">-</span>
        <button id="next">›</button>

        <button id="zoom-out">−</button>
        <button id="zoom-in">+</button>
        <span id="zoom-level">100%</span>

        <input id="searchBox" placeholder="Search…" />
        <button id="toggleInk">Draw ✏️</button>
      </div>

      <!-- Viewer -->
      <div id="viewer">
        <canvas id="pdf-canvas"></canvas>
      </div>
    </div>
  </div>

  <!-- -----------------------------
       ENHANCED PDF LOGIC (pure JS)
  ---------------------------------->
  <script>
    const url = '../../../JSD English HH BRSM.pdf';   // your PDF
 
    pdfjsLib.getDocument(url).promise.then(doc => {
    pdfDoc = doc;
    document.getElementById('page-count').textContent = doc.numPages;
    renderPage(pageNum);
    renderThumbs();
    });
    let pdfDoc  = null,
        pageNum = 1,
        scale   = 1.5,
        searchTerm = '',
        searchMatches = [],
        searchIdx = 0;

    const canv      = document.getElementById('pdf-canvas');
    const ctx       = canv.getContext('2d');
    const sidebar   = document.getElementById('sidebar');
    const viewer    = document.getElementById('viewer');
    const searchBox = document.getElementById('searchBox');

    /* PDF.js init */
    pdfjsLib.getDocument(url).promise.then(doc => {
      pdfDoc = doc;
      document.getElementById('page-count').textContent = doc.numPages;
      renderPage(pageNum);
      renderThumbs();
    });

    /* --------- RENDER PAGE --------- */
    function renderPage(num) {
      pdfDoc.getPage(num).then(page => {
        const viewport = page.getViewport({ scale });
        canv.height = viewport.height;
        canv.width  = viewport.width;

        page.render({ canvasContext: ctx, viewport }).promise.then(() => {
          document.getElementById('page-num').textContent = num;
          document.getElementById('zoom-level').textContent = Math.round(scale*100) + '%';
          // highlight search matches
          if (searchTerm) highlightMatches();
        });
      });
    }

    /* --------- THUMBNAILS --------- */
    function renderThumbs() {
      for (let i=1; i<=pdfDoc.numPages; i++) {
        pdfDoc.getPage(i).then(page => {
          const vp = page.getViewport({ scale: 0.2 });
          const thumb = document.createElement('canvas');
          thumb.width = vp.width; thumb.height = vp.height;
          thumb.className = 'thumb';
          thumb.title = `Page ${i}`;
          sidebar.appendChild(thumb);

          page.render({ canvasContext: thumb.getContext('2d'), viewport: vp });
          thumb.addEventListener('click', () => { pageNum = i; renderPage(pageNum); });
        });
      }
    }

    /* --------- ZOOM --------- */
    document.getElementById('zoom-in').onclick  = () => { scale += 0.25; renderPage(pageNum); };
    document.getElementById('zoom-out').onclick = () => { scale = Math.max(0.5, scale - 0.25); renderPage(pageNum); };

    /* --------- NAV --------- */
    document.getElementById('prev').onclick = () => { if (pageNum > 1) { pageNum--; renderPage(pageNum); } };
    document.getElementById('next').onclick = () => { if (pageNum < pdfDoc.numPages) { pageNum++; renderPage(pageNum); } };

    /* --------- SEARCH --------- */
    searchBox.addEventListener('keydown', e => {
      if (e.key === 'Enter') {
        searchTerm = searchBox.value.trim();
        if (searchTerm) performSearch();
      }
    });

    function performSearch() {
      searchMatches = []; searchIdx = 0;
      Promise.all(
        Array.from({ length: pdfDoc.numPages }, (_, i) =>
          pdfDoc.getPage(i + 1).then(page =>
            page.getTextContent().then(text => {
              const str = text.items.map(item => item.str).join('');
              if (str.toLowerCase().includes(searchTerm.toLowerCase())) searchMatches.push(i + 1);
            })
          )
        )
      ).then(() => {
        if (searchMatches.length) {
          pageNum = searchMatches[0];
          renderPage(pageNum);
        } else {
          alert('No matches found.');
        }
      });
    }

    function highlightMatches() {
      /* Simple placeholder – PDF.js text-layer required for real highlighting */
    }

    /* --------- KEYBOARD SHORTCUTS --------- */
    window.addEventListener('keydown', e => {
      if (e.ctrlKey || e.metaKey) {
        if (e.key === '+') { e.preventDefault(); document.getElementById('zoom-in').click(); }
        if (e.key === '-') { e.preventDefault(); document.getElementById('zoom-out').click(); }
        if (e.key === 'f') { e.preventDefault(); searchBox.focus(); }
      } else {
        if (e.key === 'ArrowLeft')  document.getElementById('prev').click();
        if (e.key === 'ArrowRight') document.getElementById('next').click();
      }
    });

    /* --------- INK ANNOTATION LAYER --------- */
    let inkMode = false;
    const inkCanvas = document.createElement('canvas');
    inkCanvas.id = 'ink-layer';
    inkCanvas.style.position = 'absolute';
    inkCanvas.style.top = 0; inkCanvas.style.left = 0;
    inkCanvas.style.pointerEvents = 'none';
    viewer.appendChild(inkCanvas);

    const inkCtx = inkCanvas.getContext('2d');
    let drawing = false;

    document.getElementById('toggleInk').onclick = () => {
      inkMode = !inkMode;
      inkCanvas.style.pointerEvents = inkMode ? 'auto' : 'none';
      inkCanvas.style.cursor = inkMode ? 'crosshair' : 'default';

      // resize overlay to match canvas
      inkCanvas.width  = canv.width;
      inkCanvas.height = canv.height;
    };

    inkCanvas.onmousedown = e => { if (!inkMode) return; drawing = true; inkCtx.beginPath(); inkCtx.moveTo(e.offsetX, e.offsetY); };
    inkCanvas.onmousemove = e => { if (!drawing) return; inkCtx.lineTo(e.offsetX, e.offsetY); inkCtx.stroke(); };
    inkCanvas.onmouseup   = () => { drawing = false; };
  </script>
</body>
</html>
