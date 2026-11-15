import Alpine from 'alpinejs';

/**
 * Line editor shared by quotations and invoices.
 * Totals shown here are a preview only — the server recalculates every amount on save.
 */
window.documentForm = (config) => ({
    lines: config.lines,
    catalogue: config.catalogue,
    contacts: config.contacts,
    defaultVat: config.defaultVat,
    customerId: config.customerId,
    contactId: config.contactId,

    availableContacts() {
        return this.contacts[this.customerId] ?? [];
    },

    addLine() {
        this.lines.push({
            service_id: '',
            name: '',
            description: '',
            unit: 'item',
            quantity: '1',
            unit_price: '0.00',
            discount_percent: '0',
            vat_rate: this.defaultVat,
        });
    },

    removeLine(index) {
        this.lines.splice(index, 1);
    },

    applyService(index, serviceId) {
        const service = this.catalogue.find((s) => String(s.id) === String(serviceId));

        if (!service) {
            this.lines[index].service_id = '';
            return;
        }

        this.lines[index].service_id = service.id;
        this.lines[index].name = service.name;
        this.lines[index].description = service.description ?? '';
        this.lines[index].unit = service.unit;
        this.lines[index].unit_price = service.price;
        this.lines[index].vat_rate = service.vat_rate;
    },

    lineNet(line) {
        const gross = (parseFloat(line.quantity) || 0) * (parseFloat(line.unit_price) || 0);
        return gross - gross * ((parseFloat(line.discount_percent) || 0) / 100);
    },

    lineVat(line) {
        return this.lineNet(line) * ((parseFloat(line.vat_rate) || 0) / 100);
    },

    lineTotal(line) {
        return this.lineNet(line) + this.lineVat(line);
    },

    subtotal() {
        return this.lines.reduce((sum, line) => sum + this.lineNet(line), 0);
    },

    discountTotal() {
        return this.lines.reduce((sum, line) => {
            const gross = (parseFloat(line.quantity) || 0) * (parseFloat(line.unit_price) || 0);
            return sum + gross * ((parseFloat(line.discount_percent) || 0) / 100);
        }, 0);
    },

    vatTotal() {
        return this.lines.reduce((sum, line) => sum + this.lineVat(line), 0);
    },

    grandTotal() {
        return this.subtotal() + this.vatTotal();
    },

    formatMoney(value) {
        return new Intl.NumberFormat('sv-SE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(value || 0);
    },
});

window.Alpine = Alpine;

Alpine.start();

const initializeWaterParticles = () => {
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    document.querySelectorAll('.water-particles').forEach((canvas) => {
        const context = canvas.getContext('2d');

        if (!context) {
            return;
        }

        let width = 0;
        let height = 0;
        let pixelRatio = 1;
        let animationFrame = 0;
        let particles = [];

        const createParticles = () => Array.from(
            { length: Math.min(138, Math.max(60, Math.round(width * height / 11500))) },
            () => ({ x: Math.random(), y: Math.random(), depth: Math.random(), speed: reduceMotion ? 0 : 0.0014 + Math.random() * 0.0024 }),
        );

        const resize = () => {
            width = window.innerWidth;
            height = window.innerHeight;
            pixelRatio = Math.min(window.devicePixelRatio || 1, 2);
            canvas.width = Math.round(width * pixelRatio);
            canvas.height = Math.round(height * pixelRatio);
            context.setTransform(pixelRatio, 0, 0, pixelRatio, 0, 0);
            particles = createParticles();
        };

        const render = () => {
            context.clearRect(0, 0, width, height);

            const projected = particles.map((particle) => {
                particle.depth += particle.speed;

                if (particle.depth > 1) {
                    particle.depth = 0;
                    particle.x = Math.random();
                    particle.y = Math.random();
                }

                const scale = 0.3 + particle.depth * 1.05;

                return {
                    x: width / 2 + (particle.x - 0.5) * width * scale,
                    y: height / 2 + (particle.y - 0.5) * height * scale,
                    radius: 0.7 + particle.depth * 1.45,
                    depth: particle.depth,
                };
            });

            projected.forEach((point, index) => {
                for (let nextIndex = index + 1; nextIndex < projected.length; nextIndex += 1) {
                    const next = projected[nextIndex];
                    const distance = Math.hypot(point.x - next.x, point.y - next.y);

                    if (distance < 88) {
                        context.beginPath();
                        context.moveTo(point.x, point.y);
                        context.lineTo(next.x, next.y);
                        context.strokeStyle = `rgba(124, 111, 247, ${(1 - distance / 88) * 0.16 * Math.min(point.depth + 0.25, next.depth + 0.25)})`;
                        context.lineWidth = 0.7;
                        context.stroke();
                    }
                }

                context.beginPath();
                context.arc(point.x, point.y, point.radius, 0, Math.PI * 2);
                context.fillStyle = `rgba(125, 111, 245, ${0.13 + point.depth * 0.48})`;
                context.fill();
            });

            if (!reduceMotion && !document.hidden) {
                animationFrame = window.requestAnimationFrame(render);
            }
        };

        window.addEventListener('resize', resize, { passive: true });
        document.addEventListener('visibilitychange', () => {
            if (document.hidden && animationFrame) {
                window.cancelAnimationFrame(animationFrame);
                animationFrame = 0;
            } else if (!document.hidden && !reduceMotion && !animationFrame) {
                animationFrame = window.requestAnimationFrame(render);
            }
        });

        resize();
        render();
    });
};

initializeWaterParticles();
