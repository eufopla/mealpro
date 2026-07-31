<!doctype html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/svg+xml" href="/vite.svg" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>NUTRINET // Connexion</title>
  </head>
  <body>
    <div class="scanlines"></div>
    <div class="grid-bg"></div>

    <!-- ECRAN SELECTION PROFIL -->
    <section id="profiles" class="screen profiles-screen">
      <header class="brand">
        <h1 class="glitch" data-text="MEALPRO">MEALPRO</h1>
        <p class="brand-sub">// SYSTEME ALIMENTAIRE v2.07.7</p>
      </header>

      <h2 class="who">QUI ES-TU ?</h2>

      <div class="profiles">
        <a href="#chatouni-pin" class="profile" style="--neon:#FCEE0A; --neon-soft:rgba(252,238,10,.35)">
          <div class="avatar-wrap">
            <div class="avatar">
              <img src="assets/img/cat.jpeg" alt="Chatouni" />
            </div>
            <div class="avatar-ring"></div>
          </div>
          <span class="profile-name">CHATOUNI</span>
          <span class="profile-tag">utilisateur_01</span>
        </a>

        <a href="#lapinou-pin" class="profile" style="--neon:#00FF9C; --neon-soft:rgba(0,255,156,.35)">
          <div class="avatar-wrap">
            <div class="avatar">
              <img src="assets/img/rabbit.jpeg" alt="Lapinou" />
            </div>
            <div class="avatar-ring"></div>
          </div>
          <span class="profile-name">LAPINOU</span>
          <span class="profile-tag">utilisateur_02</span>
        </a>
      </div>

      <footer class="status-bar">
        <span class="dot"></span> CONNEXION SÉCURISÉE — PROTOCOLE ARASAKA
      </footer>
    </section>

    <!-- ECRAN PIN CHATOUNI -->
    <section id="chatouni-pin" class="screen pin-screen">
      <a href="#profiles" class="back">&larr; RETOUR</a>
      <div class="pin-profile" style="--neon:#FCEE0A; --neon-soft:rgba(252,238,10,.35)">
        <div class="avatar mini">
          <img src="https://images.pexels.com/photos/20548749/pexels-photo-20548749.png?auto=compress&cs=tinysrgb&h=650&w=940" alt="Chatouni" />
        </div>
        <h2 class="pin-title">CHATOUNI</h2>
      </div>
      <p class="pin-prompt">SAISIS TON CODE D'ACCÈS</p>
      <div class="pin-dots">
        <span class="dot-pin"></span><span class="dot-pin"></span>
        <span class="dot-pin"></span><span class="dot-pin"></span>
      </div>
      <div class="keypad">
        <button class="key">1</button><button class="key">2</button><button class="key">3</button>
        <button class="key">4</button><button class="key">5</button><button class="key">6</button>
        <button class="key">7</button><button class="key">8</button><button class="key">9</button>
        <button class="key ghost">&nbsp;</button><button class="key">0</button>
        <button class="key del" aria-label="effacer">&times;</button>
      </div>
    </section>

    <!-- ECRAN PIN LAPINOU -->
    <section id="lapinou-pin" class="screen pin-screen">
      <a href="#profiles" class="back">&larr; RETOUR</a>
      <div class="pin-profile" style="--neon:#00FF9C; --neon-soft:rgba(0,255,156,.35)">
        <div class="avatar mini">
          <img src="https://images.pexels.com/photos/19904640/pexels-photo-19904640.jpeg?auto=compress&cs=tinysrgb&h=650&w=940" alt="Lapinou" />
        </div>
        <h2 class="pin-title">LAPINOU</h2>
      </div>
      <p class="pin-prompt">SAISIS TON CODE D'ACCÈS</p>
      <div class="pin-dots">
        <span class="dot-pin"></span><span class="dot-pin"></span>
        <span class="dot-pin"></span><span class="dot-pin"></span>
      </div>
      <div class="keypad">
        <button class="key">1</button><button class="key">2</button><button class="key">3</button>
        <button class="key">4</button><button class="key">5</button><button class="key">6</button>
        <button class="key">7</button><button class="key">8</button><button class="key">9</button>
        <button class="key ghost">&nbsp;</button><button class="key">0</button>
        <button class="key del" aria-label="effacer">&times;</button>
      </div>
    </section>

    <script type="module" src="/main.js"></script>
  </body>
</html>
