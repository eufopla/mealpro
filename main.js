

const PROFILES = {
  chatouni: {
    name: 'CHATOUNI',
    img: 'assets/img/cat.jpeg',
    neon: '#FCEE0A',
    soft: 'rgba(252,238,10,.35)',
  },
  lapinou: {
    name: 'LAPINOU',
    img: 'assets/img/rabbit.jpeg',
    neon: '#00FF9C',
    soft: 'rgba(0,255,156,.35)',
  },
}

const profilesScreen = document.getElementById('profiles')
const faceScan = document.getElementById('face-scan')
const scanAvatar = document.getElementById('scan-avatar')
const scanName = document.getElementById('scan-name')
const scanStatus = document.getElementById('scan-status')
const scanBar = document.getElementById('scan-bar')
const scanPct = document.getElementById('scan-pct')
const scanFrame = faceScan.querySelector('.scan-frame')

const STEPS = [
  'INITIALISATION SCAN BIOMÉTRIQUE...',
  'ANALYSE DES POINTS FACIAUX...',
  'COMPARAISON AVEC BASE DE DONNÉES...',
  'VÉRIFICATION SIGNATURE RÉTINIENNE...',
  'AUTHENTIFICATION ACCEPTÉE',
]

function runScan(profile) {
  const data = PROFILES[profile]
  if (!data) return

  scanAvatar.src = data.img
  scanAvatar.alt = data.name
  scanName.textContent = data.name

  scanFrame.style.setProperty('--neon', data.neon)
  scanFrame.style.setProperty('--neon-soft', data.soft)

  scanName.style.color = data.neon
  scanName.style.textShadow = `0 0 14px ${data.soft}`

  scanStatus.classList.remove('ok')
  scanStatus.textContent = STEPS[0]

  scanBar.style.width = '0%'
  scanPct.textContent = '0%'

  profilesScreen.classList.add('hidden')
  faceScan.classList.add('active')

  const duration = 3200
  const start = performance.now()

  function tick(now) {
    const elapsed = now - start
    const pct = Math.min(100, (elapsed / duration) * 100)

    scanBar.style.width = pct + '%'
    scanPct.textContent = Math.floor(pct) + '%'

    const step = Math.min(
      STEPS.length - 1,
      Math.floor((pct / 100) * STEPS.length)
    )

    scanStatus.textContent = STEPS[step]

    if (step === STEPS.length - 1) {
      scanStatus.classList.add('ok')
    }

    if (pct < 100) {
      requestAnimationFrame(tick)
    } else {
      authenticate(profile)
    }
  }

  requestAnimationFrame(tick)
}

async function authenticate(profile) {
  try {
    const response = await fetch('login_action.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        profile: profile,
      }),
    })

    const data = await response.json()

    if (data.success) {
      setTimeout(() => {
        window.location.href = 'index.php'
      }, 500)
    } else {
      alert('Connexion refusée')
      location.reload()
    }
  } catch (e) {
    console.error(e)
    alert('Erreur de connexion')
    location.reload()
  }
}

document.querySelectorAll('.profile').forEach(el => {
  el.addEventListener('click', e => {
    e.preventDefault()
    runScan(el.dataset.profile)
  })
})