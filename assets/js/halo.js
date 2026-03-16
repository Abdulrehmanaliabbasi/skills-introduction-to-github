/**
 * INFINITYBINARY - Halo Orb Effect Engine
 * Animated gradient orbs that follow cursor
 */

class HaloEngine {
  constructor() {
    this.enabled = getComputedStyle(document.documentElement)
      .getPropertyValue('--halo-enabled').trim() === '1';

    if (!this.enabled) return;

    this.orbs = [];
    this.mouse = { x: window.innerWidth / 2, y: window.innerHeight / 2 };
    this.targetMouse = { x: this.mouse.x, y: this.mouse.y };

    this.init();
  }

  init() {
    // Create halo container
    this.container = document.createElement('div');
    this.container.className = 'halo-container';
    this.container.style.cssText = `
      position: fixed;
      inset: 0;
      overflow: hidden;
      pointer-events: none;
      z-index: 0;
    `;

    // Get CSS variables
    const styles = getComputedStyle(document.documentElement);
    const color1 = styles.getPropertyValue('--halo-color-1').trim();
    const color2 = styles.getPropertyValue('--halo-color-2').trim();
    const color3 = styles.getPropertyValue('--halo-color-3').trim();
    const intensity = parseFloat(styles.getPropertyValue('--halo-intensity')) || 0.35;
    const spread = parseInt(styles.getPropertyValue('--halo-spread')) || 600;
    const speed = parseFloat(styles.getPropertyValue('--halo-speed')) || 12;

    // Create 3 orbs
    const orbConfigs = [
      { color: color1, x: 0.2, y: 0.3, scale: 1.2 },
      { color: color2, x: 0.7, y: 0.5, scale: 1.0 },
      { color: color3, x: 0.5, y: 0.8, scale: 0.9 }
    ];

    orbConfigs.forEach((config, index) => {
      const orb = document.createElement('div');
      orb.className = `halo-orb halo-orb-${index + 1}`;
      orb.style.cssText = `
        position: absolute;
        width: ${spread * config.scale}px;
        height: ${spread * config.scale}px;
        border-radius: 50%;
        background: radial-gradient(circle, ${config.color}, transparent 70%);
        opacity: ${intensity};
        filter: blur(80px);
        transform: translate(-50%, -50%);
        will-change: transform;
        animation: float-orb-${index + 1} ${speed}s ease-in-out infinite;
      `;

      this.container.appendChild(orb);
      this.orbs.push({
        element: orb,
        baseX: config.x * window.innerWidth,
        baseY: config.y * window.innerHeight,
        currentX: config.x * window.innerWidth,
        currentY: config.y * window.innerHeight,
        influence: 0.05 + (index * 0.02)
      });
    });

    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
      @keyframes float-orb-1 {
        0%, 100% { transform: translate(-50%, -50%) translate(0, 0); }
        33% { transform: translate(-50%, -50%) translate(60px, -80px); }
        66% { transform: translate(-50%, -50%) translate(-40px, 60px); }
      }
      @keyframes float-orb-2 {
        0%, 100% { transform: translate(-50%, -50%) translate(0, 0); }
        33% { transform: translate(-50%, -50%) translate(-70px, 50px); }
        66% { transform: translate(-50%, -50%) translate(50px, -70px); }
      }
      @keyframes float-orb-3 {
        0%, 100% { transform: translate(-50%, -50%) translate(0, 0); }
        33% { transform: translate(-50%, -50%) translate(40px, 70px); }
        66% { transform: translate(-50%, -50%) translate(-60px, -40px); }
      }
    `;
    document.head.appendChild(style);

    // Insert at start of body
    document.body.insertBefore(this.container, document.body.firstChild);

    // Setup event listeners
    this.setupListeners();

    // Start animation loop
    this.animate();
  }

  setupListeners() {
    // Track mouse movement
    document.addEventListener('mousemove', (e) => {
      this.targetMouse.x = e.clientX;
      this.targetMouse.y = e.clientY;
    });

    // Handle resize
    let resizeTimeout;
    window.addEventListener('resize', () => {
      clearTimeout(resizeTimeout);
      resizeTimeout = setTimeout(() => {
        this.orbs.forEach((orb, index) => {
          const config = [
            { x: 0.2, y: 0.3 },
            { x: 0.7, y: 0.5 },
            { x: 0.5, y: 0.8 }
          ][index];

          orb.baseX = config.x * window.innerWidth;
          orb.baseY = config.y * window.innerHeight;
        });
      }, 250);
    });

    // Pause on visibility change
    document.addEventListener('visibilitychange', () => {
      if (document.hidden) {
        this.paused = true;
      } else {
        this.paused = false;
      }
    });
  }

  animate = () => {
    if (!this.paused) {
      // Smooth mouse following with lerp
      this.mouse.x += (this.targetMouse.x - this.mouse.x) * 0.1;
      this.mouse.y += (this.targetMouse.y - this.mouse.y) * 0.1;

      // Update each orb
      this.orbs.forEach(orb => {
        // Calculate mouse influence
        const dx = this.mouse.x - orb.baseX;
        const dy = this.mouse.y - orb.baseY;

        // Lerp towards influenced position
        const targetX = orb.baseX + (dx * orb.influence);
        const targetY = orb.baseY + (dy * orb.influence);

        orb.currentX += (targetX - orb.currentX) * 0.05;
        orb.currentY += (targetY - orb.currentY) * 0.05;

        // Apply position
        orb.element.style.left = orb.currentX + 'px';
        orb.element.style.top = orb.currentY + 'px';
      });
    }

    requestAnimationFrame(this.animate);
  }

  destroy() {
    if (this.container && this.container.parentNode) {
      this.container.parentNode.removeChild(this.container);
    }
  }
}

// Initialize on DOM ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    window.haloEngine = new HaloEngine();
  });
} else {
  window.haloEngine = new HaloEngine();
}
