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
