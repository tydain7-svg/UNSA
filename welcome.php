<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$first = htmlspecialchars($_SESSION['firstname'] ?? 'Artist');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Art Albums — Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root{
  --gold:#d4af37;
  --gold-2:#f5e6b1;
  --bg:#ffffff;
  --ink:#1b1b1b;
  --muted:#5c5c5c;
  --card:#fffdfa;
}
*{box-sizing:border-box}
html,body{height:100%}
body{
  margin:0;background:
  radial-gradient(1200px 600px at 100% -20%, rgba(212,175,55,.15), transparent 60%),
  radial-gradient(900px 500px at -10% 120%, rgba(245,230,177,.18), transparent 60%),
  var(--bg);
  color:var(--ink);font-family:Poppins,system-ui,Segoe UI,Roboto,Arial,sans-serif;
}

/* Header */
.header{
  position:sticky;top:0;z-index:50;
  backdrop-filter:saturate(1.2) blur(6px);
  background:linear-gradient(180deg, rgba(255,255,255,.9), rgba(255,255,255,.7));
  border-bottom:1px solid rgba(212,175,55,.25);
}
.container{max-width:1200px;margin:0 auto;padding:18px 20px}
.brand{
  display:flex;align-items:center;gap:12px
}
.brand .logo{
  width:44px;height:44px;border-radius:12px;
  background:conic-gradient(from 220deg, var(--gold), var(--gold-2), var(--gold));
  box-shadow:0 6px 20px rgba(212,175,55,.35), inset 0 2px 8px rgba(255,255,255,.55);
}
.brand h1{font-size:20px;margin:0;font-weight:600;letter-spacing:.4px}
.brand small{display:block;color:var(--muted);font-weight:400;font-size:12px;margin-top:2px}

/* Topbar right */
.actions{margin-left:auto;display:flex;align-items:center;gap:10px}
.welcome{
  padding:8px 12px;border-radius:999px;
  background:rgba(212,175,55,.12);border:1px solid rgba(212,175,55,.35);
  font-size:13px
}
.logout{
  text-decoration:none;color:#fff;background:linear-gradient(135deg,var(--gold),#b08d2c);
  border:0;padding:9px 14px;border-radius:999px;font-weight:600;
  box-shadow:0 6px 14px rgba(212,175,55,.35);
  transition:transform .15s ease, box-shadow .2s ease;
}
.logout:hover{transform:translateY(-1px);box-shadow:0 10px 22px rgba(212,175,55,.4)}

/* Albums grid */
.main{padding:24px 20px 60px}
.grid{
  display:grid;gap:18px;
  grid-template-columns:repeat(12,1fr)
}
@media (max-width:1100px){.grid{grid-template-columns:repeat(8,1fr)}}
@media (max-width:720px){.grid{grid-template-columns:repeat(4,1fr)}}

.card{
  grid-column:span 4;
  position:relative;overflow:hidden;border-radius:24px;background:var(--card);
  border:1px solid rgba(212,175,55,.25);
  box-shadow:0 10px 30px rgba(212,175,55,.15), 0 4px 9px rgba(0,0,0,.04);
  cursor:pointer;min-height:220px;
  transition:transform .25s ease, box-shadow .25s ease, border-color .25s ease
}
.card:hover{
  transform:translateY(-4px);
  box-shadow:0 18px 40px rgba(212,175,55,.24), 0 6px 14px rgba(0,0,0,.06);
  border-color:rgba(212,175,55,.5)
}
.card .cover{
  position:absolute;inset:0;overflow:hidden
}
.card .cover img{
  width:100%;height:100%;object-fit:cover;display:block;
  transform:scale(1.02);transition:transform .5s ease, filter .5s ease
}
.card:hover .cover img{transform:scale(1.06);filter:contrast(1.05) saturate(1.05)}
.card .veil{
  position:absolute;inset:0;background:linear-gradient(180deg, rgba(0,0,0,.05), rgba(0,0,0,.45));
}
.card .label{
  position:absolute;left:16px;bottom:16px;right:16px;color:#fff
}
.card .title{font-size:18px;font-weight:600;text-shadow:0 2px 8px rgba(0,0,0,.55)}
.card .meta{
  margin-top:6px;font-size:12px;opacity:.95;
  display:inline-flex;align-items:center;gap:6px;
  background:rgba(255,255,255,.15);padding:6px 10px;border-radius:999px;border:1px solid rgba(255,255,255,.25)
}

/* Shimmer skeleton for any image before load */
.skel{
  position:absolute;inset:0;border-radius:inherit;
  background:linear-gradient(100deg, #f6f0dc 8%, #fff 18%, #f6f0dc 33%);
  background-size:300% 100%;animation:shimmer 1.2s infinite;
}
@keyframes shimmer{0%{background-position:200% 0}100%{background-position:-200% 0}}
img.loaded + .skel{display:none}

/* Modal gallery */
.modal{
  position:fixed;inset:0;display:none;z-index:1000;
  background:rgba(0,0,0,.55);backdrop-filter:blur(4px);
}
.modal.open{display:block}
.sheet{
  position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);
  width:min(1000px,92vw);max-height:88vh;overflow:auto;border-radius:28px;
  background:linear-gradient(180deg,#fff, #fffdfa);
  border:1px solid rgba(212,175,55,.35);
  box-shadow:0 24px 70px rgba(0,0,0,.35), 0 10px 28px rgba(212,175,55,.25);
}
.sheet header{
  position:sticky;top:0;background:linear-gradient(180deg, #ffffff 70%, rgba(255,255,255,.85));
  padding:18px 20px;border-bottom:1px solid rgba(212,175,55,.25);
  display:flex;align-items:center;gap:14px
}
.badge{
  width:36px;height:36px;border-radius:10px;flex:0 0 auto;
  background:conic-gradient(from 220deg, var(--gold), var(--gold-2), var(--gold));
  box-shadow:inset 0 2px 8px rgba(255,255,255,.6)
}
.sheet h2{margin:0;font-size:18px}
.close{
  margin-left:auto;border:0;cursor:pointer;border-radius:999px;padding:8px 12px;
  background:linear-gradient(135deg,var(--gold),#b08d2c);color:#fff;font-weight:600
}
.gallery{
  display:grid;gap:12px;padding:18px 20px 24px;
  grid-template-columns:repeat(2,1fr)
}
.gallery figure{
  position:relative;margin:0;border-radius:18px;overflow:hidden;
  border:1px solid rgba(212,175,55,.25);background:#fff;
  box-shadow:0 8px 22px rgba(0,0,0,.06)
}
.gallery img{width:100%;height:280px;object-fit:cover;display:block}
.caption{
  position:absolute;left:10px;bottom:10px;padding:6px 10px;border-radius:999px;
  background:rgba(0,0,0,.45);color:#fff;font-size:12px;border:1px solid rgba(255,255,255,.25)
}
@media (max-width:640px){
  .gallery{grid-template-columns:1fr}
  .gallery img{height:220px}
}

/* Tiny helpers */
.hidden{display:none !important}
</style>
</head>
<body>

<!-- Header -->
<div class="header">
  <div class="container" style="display:flex;align-items:center;gap:16px">
    <div class="brand">
      <div class="logo" aria-hidden="true"></div>
      <div>
        <h1>Orchid Noir Studio</h1>
        <small>Our Art Album</small>
      </div>
    </div>
    <div class="actions">
      <div class="welcome">Welcome, <?= $first; ?> 🎉</div>
      <a class="logout" href="logout.php">Logout</a>
    </div>
  </div>
</div>

<!-- Body -->
<main class="main container">
  <div class="grid" id="albums"><!-- injected by JS --></div>
</main>

<!-- Modal -->
<div class="modal" id="modal">
  <div class="sheet" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <header>
      <div class="badge" aria-hidden="true"></div>
      <h2 id="modalTitle">Album</h2>
      <button class="close" id="closeBtn" type="button">Close</button>
    </header>
    <div class="gallery" id="gallery"><!-- injected --></div>
  </div>
</div>

<script>
/**
 * ✅ Real images, no empty placeholders:
 * These use Unsplash curated topics/queries. Replace with your own URLs if desired.
 * You can add more categories easily—just follow the structure.
 */
const ALBUMS = [
  {
    title: "Painting",
    cover: "painting (3).jpg",
    images: [
      "painting (2).jpg",
      "painting (1).jpg",
      "painting (4).jpg",
      "painting (3).jpg"
    ]
  },
  {
    title: "Pottery",
    cover: "pottery  (4).jpg",
    images: [
      "pottery  (1).jpg",
      "pottery  (3).jpg",
      "pottery  (1).jpg",
      "pottery  (4).jpg"
    ]
  },
  {
    title: "Photography",
    cover: "photo1 (3).jpg",
    images: [
      "photo1 (2).jpg",
      "photo1 (1).jpg",
      "photo1 (4).jpg",
      "photo1 (3).jpg"
    ]
  },
  {
    title: "Collage Making",
    cover: "collage (2).jpg",
    images: [
      "collage (1).jpg",
      "collage (3).jpg",
      "collage (4).jpg",
      "collage (2).jpg"
    ]
  },

];

const grid = document.getElementById('albums');
const modal = document.getElementById('modal');
const closeBtn = document.getElementById('closeBtn');
const gallery = document.getElementById('gallery');
const modalTitle = document.getElementById('modalTitle');

/* Build album cards */
ALBUMS.forEach(album=>{
  const card = document.createElement('article'); card.className = 'card'; card.tabIndex = 0;
  card.setAttribute('role','button'); card.setAttribute('aria-label',`Open ${album.title} album`);
  card.innerHTML = `
    <div class="cover">
      <img src="${album.cover}" alt="${album.title} cover image" loading="lazy" />
      <div class="skel" aria-hidden="true"></div>
    </div>
    <div class="veil" aria-hidden="true"></div>
    <div class="label">
      <div class="title">${album.title}</div>
      <div class="meta">${album.images.length} photos • curated</div>
    </div>
  `;
  /* Mark img loaded to hide skeleton */
  const img = card.querySelector('img');
  if(img.complete){ img.classList.add('loaded'); } else {
    img.addEventListener('load', ()=> img.classList.add('loaded'));
    img.addEventListener('error', ()=> img.classList.add('loaded')); // still hide skeleton if error
  }
  card.addEventListener('click', ()=> openAlbum(album));
  card.addEventListener('keydown', (e)=>{ if(e.key==='Enter' || e.key===' ') { e.preventDefault(); openAlbum(album);} });
  grid.appendChild(card);
});

/* Open modal with gallery */
function openAlbum(album){
  modalTitle.textContent = album.title;
  gallery.innerHTML = '';
  album.images.forEach((src, i)=>{
    const fig = document.createElement('figure');
    const im = document.createElement('img');
    im.src = src; im.loading = 'lazy'; im.alt = `${album.title} ${i+1}`;
    const skel = document.createElement('div'); skel.className = 'skel'; skel.setAttribute('aria-hidden','true');
    if(im.complete){ im.classList.add('loaded'); } else {
      im.addEventListener('load', ()=> im.classList.add('loaded'));
      im.addEventListener('error', ()=> im.classList.add('loaded'));
    }
    const cap = document.createElement('figcaption'); cap.className='caption'; cap.textContent = `${album.title} #${i+1}`;
    fig.appendChild(im); fig.appendChild(skel); fig.appendChild(cap);
    gallery.appendChild(fig);
  });
  modal.classList.add('open');
}

/* Close modal */
function closeModal(){ modal.classList.remove('open'); }
closeBtn.addEventListener('click', closeModal);
modal.addEventListener('click', (e)=>{ if(e.target === modal) closeModal(); });
document.addEventListener('keydown', (e)=>{ if(e.key==='Escape') closeModal(); });

</script>
</body>
</html>
