/**
 * InfinityBinary - Halo Background Effects
 */

document.addEventListener('DOMContentLoaded', function() {
    // Create halo background container
    const haloContainer = document.createElement('div');
    haloContainer.className = 'halo-bg';
    document.body.prepend(haloContainer);

    // Create halo elements
    const halos = [
        { class: 'halo-1', color: '#00d4ff' },
        { class: 'halo-2', color: '#7b2fff' },
        { class: 'halo-3', color: '#ff6b35' }
    ];

    halos.forEach(halo => {
        const haloEl = document.createElement('div');
        haloEl.className = `halo ${halo.class}`;
        haloContainer.appendChild(haloEl);
    });

    // Mouse movement effect (subtle)
    let mouseX = 0;
    let mouseY = 0;
    let currentX = 0;
    let currentY = 0;

    document.addEventListener('mousemove', function(e) {
        mouseX = e.clientX / window.innerWidth - 0.5;
        mouseY = e.clientY / window.innerHeight - 0.5;
    });

    // Smooth animation loop
    function animate() {
        currentX += (mouseX - currentX) * 0.05;
        currentY += (mouseY - currentY) * 0.05;

        const halosElements = document.querySelectorAll('.halo');
        halosElements.forEach((halo, index) => {
            const depth = (index + 1) * 20;
            halo.style.transform = `translate(${currentX * depth}px, ${currentY * depth}px)`;
        });

        requestAnimationFrame(animate);
    }

    animate();
});
