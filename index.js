<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Perritos y Gatitos</title>
  <style>
    :root{
      --bg:#f6f8fb;
      --card:#ffffff;
      --accent:#ff6b6b;
      --accent2:#4f9cff;
      --text:#263238;
      --muted:#6b7280;
    }
    *{box-sizing:border-box}
    body{
      margin:0;
      font-family:Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      background:linear-gradient(180deg,#eef3fb 0%,var(--bg) 100%);
      color:var(--text);
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
      padding:28px;
    }

    header{
      display:flex;
      gap:16px;
      align-items:center;
      justify-content:space-between;
      margin-bottom:20px;
    }
    .brand{
      display:flex;
      gap:12px;
      align-items:center;
    }
    .logo{
      width:56px;height:56px;border-radius:12px;
      background:linear-gradient(135deg,var(--accent),var(--accent2));
      display:flex;align-items:center;justify-content:center;color:white;font-weight:700;
      box-shadow:0 6px 18px rgba(79,156,255,0.12);
    }
    h1{margin:0;font-size:20px}
    p.lead{margin:0;color:var(--muted);font-size:14px}

    main{max-width:1100px;margin:0 auto}

    .grid{
      display:grid;
      grid-template-columns:1fr;
      gap:18px;
    }
    @media(min-width:880px){
      .grid{grid-template-columns:1fr 380px;}
    }

    /* Gallery / cards */
    .section{
      background:var(--card);
      border-radius:16px;
      padding:18px;
      box-shadow:0 6px 18px rgba(20,30,60,0.06);
    }
    .section h2{margin:0 0 8px 0}
    .gallery{
      display:grid;
      grid-template-columns:repeat(auto-fill,minmax(160px,1fr));
      gap:12px;
    }
    .card{
      background:#fff;border-radius:12px;overflow:hidden;
      border:1px solid rgba(20,30,60,0.04);
      display:flex;flex-direction:column;
    }
    .card img{width:100%;height:130px;object-fit:cover;display:block;cursor:pointer}
    .card .info{padding:10px}
    .card .title{font-weight:600;margin:0;font-size:14px}
    .card .sub{margin-top:6px;color:var(--muted);font-size:13px}

    /* sidebar form */
    aside .box{display:flex;flex-direction:column;gap:8px}
    label{font-size:13px;color:var(--muted)}
    input[type="text"], select, textarea{
      padding:8px;border-radius:8px;border:1px solid rgba(20,30,60,0.06);
      font-size:14px;width:100%;outline:none;background:#fbfdff;
    }
    button{
      padding:10px 12px;border-radius:10px;border:0;background:linear-gradient(90deg,var(--accent),var(--accent2));
      color:white;font-weight:600;cursor:pointer;
    }

    .tabs{display:flex;gap:8px;margin-bottom:12px}
    .tab{padding:8px 12px;border-radius:999px;background:#f1f6ff;color:var(--accent2);font-weight:600;cursor:pointer;border:1px solid rgba(79,156,255,0.08)}
    .tab.active{background:linear-gradient(90deg,#eef7ff,#ffffff);box-shadow:0 4px 10px rgba(79,156,255,0.06)}

    /* modal */
    .modal{
      position:fixed;inset:0;display:none;align-items:center;justify-content:center;
      background:linear-gradient(180deg,rgba(10,20,40,0.35),rgba(10,20,40,0.5));
      z-index:999;
    }
    .modal.open{display:flex}
    .modal .sheet{
      max-width:920px;width:94%;background:white;border-radius:12px;overflow:hidden;
      box-shadow:0 30px 80px rgba(10,20,40,0.6);
      display:flex;gap:0;
    }
    .modal img{width:100%;height:520px;object-fit:cover}
    .modal .meta{padding:16px;width:320px}
    .close{
      position:absolute;top:20px;right:20px;background:#ffffffaa;border-radius:999px;padding:6px 8px;border:0;cursor:pointer;
    }
    footer{margin-top:18px;text-align:center;color:var(--muted);font-size:13px}
  </style>
</head>
<body>
  <header>
    <div class="brand">
      <div class="logo">P&G</div>
      <div>
        <h1>Perritos & Gatitos</h1>
        <p class="lead">Galería adorable — aprende y agrega tus fotos</p>
      </div>
    </div>
    <div>
      <small class="lead">Hecho con ♥ — guarda este archivo como <code>index.html</code></small>
    </div>
  </header>

  <main>
    <div class="grid">
      <!-- Left: contenido principal -->
      <section class="section">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
          <div>
            <h2>Perritos</h2>
            <p class="lead" style="color:var(--muted)">Una colección de perritos tiernos y curiosos.</p>
          </div>
          <div class="tabs">
            <div class="tab active" data-filter="all">Todos</div>
            <div class="tab" data-filter="perro">Perritos</div>
            <div class="tab" data-filter="gato">Gatitos</div>
          </div>
        </div>

        <div class="gallery" id="gallery">
          <!-- Cards iniciales -->
          <!-- Usamos source.unsplash para imágenes aleatorias según consulta -->
          <article class="card" data-type="perro">
            <img src="https://source.unsplash.com/800x600/?puppy" alt="Perrito feliz" loading="lazy" />
            <div class="info">
              <div class="title">Cody</div>
              <div class="sub">Perrito juguetón que ama correr.</div>
            </div>
          </article>

          <article class="card" data-type="perro">
            <img src="https://source.unsplash.com/800x600/?dog,puppy" alt="Perrito" loading="lazy" />
            <div class="info">
              <div class="title">Luna</div>
              <div class="sub">Juguete favorita: pelota.</div>
            </div>
          </article>

          <article class="card" data-type="gato">
            <img src="https://source.unsplash.com/800x600/?kitten" alt="Gatito" loading="lazy" />
            <div class="info">
              <div class="title">Milo</div>
              <div class="sub">Curioso y dormilón.</div>
            </div>
          </article>

          <article class="card" data-type="gato">
            <img src="https://source.unsplash.com/800x600/?cat,kitten" alt="Gatita" loading="lazy" />
            <div class="info">
              <div class="title">Nala</div>
              <div class="sub">Le encanta las cajas.</div>
            </div>
          </article>
        </div>

        <footer>
          Tips: haz clic en cualquier imagen para verla ampliada. Usa el formulario a la derecha para añadir tu mascota.
        </footer>
      </section>

      <!-- Right: formulario / info -->
      <aside class="section">
        <h2>Añadir mascota</h2>
        <div class="box">
          <label for="tipo">Tipo</label>
          <select id="tipo">
            <option value="perro">Perrito</option>
            <option value="gato">Gatito</option>
          </select>

          <label for="nombre">Nombre</label>
          <input id="nombre" type="text" placeholder="Ej: Rocky" />

          <label for="desc">Descripción corta</label>
          <textarea id="desc" rows="3" placeholder="Ej: Le gusta dormir en la ventana"></textarea>

          <label for="img">URL de imagen (opcional)</label>
          <input id="img" type="text" placeholder="https://..." />

          <button id="addBtn">Agregar mascota</button>

          <hr style="border:none;border-top:1px solid #f1f4fb;margin:8px 0">

          <h3>¿No tienes imagen?</h3>
          <p style="color:var(--muted);font-size:13px">Deja el campo de imagen vacío y se usará una foto aleatoria.</p>

          <div style="display:flex;gap:8px;margin-top:8px">
            <button id="randomDog">Foto aleatoria (perro)</button>
            <button id="randomCat">Foto aleatoria (gato)</button>
          </div>
        </div>
      </aside>
    </div>
  </main>

  <!-- Modal para ver imagen grande -->
  <div id="modal" class="modal" aria-hidden="true">
    <button class="close" id="closeModal">✕</button>
    <div class="sheet" role="dialog" aria-modal="true">
      <img id="modalImg" src="" alt="Imagen ampliada" />
      <div class="meta">
        <h3 id="modalTitle">Nombre</h3>
        <p id="modalDesc" style="color:var(--muted)">Descripción</p>
        <div style="margin-top:16px">
          <button id="likeBtn">Me gusta ❤️</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Referencias
    const gallery = document.getElementById('gallery');
    const addBtn = document.getElementById('addBtn');
    const tipo = document.getElementById('tipo');
    const nombre = document.getElementById('nombre');
    const desc = document.getElementById('desc');
    const imgInput = document.getElementById('img');
    const modal = document.getElementById('modal');
    const modalImg = document.getElementById('modalImg');
    const modalTitle = document.getElementById('modalTitle');
    const modalDesc = document.getElementById('modalDesc');
    const closeModal = document.getElementById('closeModal');
    const tabs = document.querySelectorAll('.tab');
    const randomDog = document.getElementById('randomDog');
    const randomCat = document.getElementById('randomCat');

    // Función para crear tarjeta
    function createCard({type, name, description, image}) {
      const art = document.createElement('article');
      art.className = 'card';
      art.dataset.type = type;
      art.innerHTML = `
        <img src="${image}" alt="${name}" loading="lazy" />
        <div class="info">
          <div class="title">${escapeHtml(name)}</div>
          <div class="sub">${escapeHtml(description)}</div>
        </div>
      `;
      // click en imagen -> abrir modal
      art.querySelector('img').addEventListener('click', () => {
        openModal(image, name, description);
      });
      return art;
    }

    // Añadir mascota (evento)
    addBtn.addEventListener('click', () => {
      const type = tipo.value === 'perro' ? 'perro' : 'gato';
      const name = nombre.value.trim() || (type === 'perro' ? 'Perrito' : 'Gatito');
      const description = desc.value.trim() || 'Una mascota muy linda';
      let image = imgInput.value.trim();
      if (!image) {
        // imagen aleatoria según tipo: usamos source.unsplash
        image = type === 'perro' ? 'https://source.unsplash.com/800x600/?puppy,dog' : 'https://source.unsplash.com/800x600/?kitten,cat';
      }
      const card = createCard({type, name, description, image});
      gallery.prepend(card);
      // limpiar formulario
      nombre.value = ''; desc.value = ''; imgInput.value = '';
    });

    // Abrir modal
    function openModal(src, title, description){
      modalImg.src = src;
      modalTitle.textContent = title;
      modalDesc.textContent = description;
      modal.classList.add('open');
      modal.setAttribute('aria-hidden', 'false');
    }
    closeModal.addEventListener('click', () => {
      modal.classList.remove('open');
      modal.setAttribute('aria-hidden', 'true');
    });
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
      }
    });

    // Filtrar por pestañas
    tabs.forEach(t => {
      t.addEventListener('click', () => {
        tabs.forEach(x => x.classList.remove('active'));
        t.classList.add('active');
        const filter = t.dataset.filter;
        const cards = document.querySelectorAll('.card');
        cards.forEach(c => {
          if (filter === 'all') c.style.display = '';
          else c.style.display = (c.dataset.type === filter) ? '' : 'none';
        });
      });
    });

    // Botones foto aleatoria
    randomDog.addEventListener('click', () => {
      const card = createCard({type:'perro', name:'Perrito Aleatorio', description:'Foto aleatoria', image:'https://source.unsplash.com/800x600/?puppy'});
      gallery.prepend(card);
    });
    randomCat.addEventListener('click', () => {
      const card = createCard({type:'gato', name:'Gatito Aleatorio', description:'Foto aleatoria', image:'https://source.unsplash.com/800x600/?kitten'});
      gallery.prepend(card);
    });

    // Delegación: abrir modal si se hace click en imágenes existentes
    document.querySelectorAll('.card img').forEach(img => {
      img.addEventListener('click', (e) => {
        const card = e.target.closest('.card');
        openModal(e.target.src, card.querySelector('.title').textContent, card.querySelector('.sub').textContent);
      });
    });

    // Helper: escapar texto simple
    function escapeHtml(str) {
      return String(str).replace(/[&<>"']/g, function(m){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]; });
    }

    // Tecla ESC cierra modal
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && modal.classList.contains('open')) {
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden','true');
      }
    });
  </script>
</body>
</html>
