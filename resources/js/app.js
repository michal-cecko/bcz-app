import './bootstrap';

window.countdown = function (targetIso) {
    return {
        days: 0,
        hours: 0,
        minutes: 0,
        seconds: 0,
        interval: null,

        start() {
            this.update();
            this.interval = setInterval(() => this.update(), 1000);
        },

        update() {
            const now = new Date().getTime();
            const target = new Date(targetIso).getTime();
            const diff = Math.max(0, target - now);

            this.days = Math.floor(diff / (1000 * 60 * 60 * 24));
            this.hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            this.minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            this.seconds = Math.floor((diff % (1000 * 60)) / 1000);

            if (diff <= 0 && this.interval) {
                clearInterval(this.interval);
                window.location.reload();
            }
        },

        destroy() {
            if (this.interval) clearInterval(this.interval);
        },
    };
};
