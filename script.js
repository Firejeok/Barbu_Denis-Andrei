
const canvas = document.getElementById('particle-canvas');
// Verifică dacă elementul canvas există
if (canvas) {
    const ctx = canvas.getContext('2d');
    let particles = [];

    // Culorile tale principale (folosite și în CSS)
    const PRIMARY_COLOR = '#ffffffff'; // Cyan
    const SECONDARY_COLOR = '#ffffffff'; // Navy Blue

    // Configurări (poți schimba aceste valori)
    const NUM_PARTICLES = 100; // Numărul de particule
    const MAX_DISTANCE = 100; // Distanța maximă pentru a trasa o linie

    // Ajustează dimensiunea canvas-ului la dimensiunea ferestrei
    function resizeCanvas() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }

    // Constructor pentru o particulă
    class Particle {
        constructor() {
            // Poziție aleatorie
            this.x = Math.random() * canvas.width;
            this.y = Math.random() * canvas.height;

            // Viteză mică și aleatorie
            this.vx = (Math.random() - 0.5) * 0.4;
            this.vy = (Math.random() - 0.5) * 0.4;

            this.radius = Math.random() * 2 + 1; // Rază între 1 și 3
            this.color = Math.random() > 0.5 ? PRIMARY_COLOR : SECONDARY_COLOR;
        }

        // Metoda de desenare a particulei
        draw() {
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
            ctx.fillStyle = this.color;
            ctx.fill();
        }

        // Metoda de actualizare a poziției și a marginilor (rebound)
        update() {
            this.x += this.vx;
            this.y += this.vy;

            if (this.x < 0 || this.x > canvas.width) this.vx *= -1;
            if (this.y < 0 || this.y > canvas.height) this.vy *= -1;
        }
    }

    // Inițializarea particulelor
    function init() {
        resizeCanvas();
        for (let i = 0; i < NUM_PARTICLES; i++) {
            particles.push(new Particle());
        }
    }

    // Funcția pentru desenarea liniilor între particule
    function drawLines() {
        for (let i = 0; i < NUM_PARTICLES; i++) {
            for (let j = i; j < NUM_PARTICLES; j++) {
                const p1 = particles[i];
                const p2 = particles[j];

                const dx = p1.x - p2.x;
                const dy = p1.y - p2.y;
                const distance = Math.sqrt(dx * dx + dy * dy);

                if (distance < MAX_DISTANCE) {
                    const opacity = 1 - (distance / MAX_DISTANCE);

                    ctx.beginPath();
                    // Folosim o culoare deschisă pentru linii (Cyan)
                    ctx.strokeStyle = '#a1a1a1ff';
                    ctx.lineWidth = 1;
                    ctx.moveTo(p1.x, p1.y);
                    ctx.lineTo(p2.x, p2.y);
                    ctx.stroke();
                }
            }
        }
    }

    // Funcția principală de animație
    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        drawLines();

        for (let i = 0; i < NUM_PARTICLES; i++) {
            particles[i].update();
            particles[i].draw();
        }

        requestAnimationFrame(animate);
    }

    // Gestionează redimensionarea ferestrei
    window.addEventListener('resize', resizeCanvas);

    // Pornirea aplicației
    init();
    animate();
}

