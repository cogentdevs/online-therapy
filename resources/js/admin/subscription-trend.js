const chartContainers = document.querySelectorAll('[data-subscription-trend-chart]');

chartContainers.forEach((container) => {
    const canvas = container.querySelector('canvas');
    const tooltip = container.querySelector('[data-subscription-trend-tooltip]');
    const labels = JSON.parse(container.dataset.labels || '[]');
    const values = JSON.parse(container.dataset.values || '[]').map(Number);
    let points = [];

    if (!(canvas instanceof HTMLCanvasElement) || labels.length !== values.length || labels.length === 0) {
        return;
    }

    const draw = () => {
        const ratio = window.devicePixelRatio || 1;
        const width = Math.max(container.clientWidth, 320);
        const height = 288;
        const padding = { top: 20, right: 18, bottom: 42, left: 42 };
        const chartWidth = width - padding.left - padding.right;
        const chartHeight = height - padding.top - padding.bottom;
        const maximum = Math.max(...values, 1);
        const yMaximum = Math.max(1, Math.ceil(maximum));
        const context = canvas.getContext('2d');

        canvas.width = Math.round(width * ratio);
        canvas.height = Math.round(height * ratio);
        context.setTransform(ratio, 0, 0, ratio, 0, 0);
        context.clearRect(0, 0, width, height);
        context.font = '12px Arial, sans-serif';
        context.lineWidth = 1;
        context.textBaseline = 'middle';

        const tickCount = Math.min(5, yMaximum);
        for (let tick = 0; tick <= tickCount; tick += 1) {
            const value = Math.round((yMaximum / tickCount) * tick);
            const y = padding.top + chartHeight - ((value / yMaximum) * chartHeight);
            context.strokeStyle = '#e4e4e2';
            context.beginPath();
            context.moveTo(padding.left, y);
            context.lineTo(width - padding.right, y);
            context.stroke();
            context.fillStyle = '#737078';
            context.textAlign = 'right';
            context.fillText(String(value), padding.left - 8, y);
        }

        points = values.map((value, index) => ({
            x: padding.left + ((chartWidth / (values.length - 1)) * index),
            y: padding.top + chartHeight - ((value / yMaximum) * chartHeight),
            value,
            label: labels[index],
        }));

        context.strokeStyle = '#3f6ca1';
        context.lineWidth = 2;
        context.beginPath();
        points.forEach((point, index) => index === 0
            ? context.moveTo(point.x, point.y)
            : context.lineTo(point.x, point.y));
        context.stroke();

        points.forEach((point) => {
            context.fillStyle = '#3f6ca1';
            context.beginPath();
            context.arc(point.x, point.y, 3, 0, Math.PI * 2);
            context.fill();
        });

        context.fillStyle = '#737078';
        context.textAlign = 'center';
        labels.forEach((label, index) => {
            if (index % 5 === 0 || index === labels.length - 1) {
                context.fillText(label, points[index].x, height - 17);
            }
        });
    };

    const hideTooltip = () => {
        tooltip.hidden = true;
    };

    canvas.addEventListener('mousemove', (event) => {
        const bounds = canvas.getBoundingClientRect();
        const mouseX = event.clientX - bounds.left;
        const nearest = points.reduce((closest, point) => (
            Math.abs(point.x - mouseX) < Math.abs(closest.x - mouseX) ? point : closest
        ));

        if (Math.abs(nearest.x - mouseX) > 12) {
            hideTooltip();
            return;
        }

        tooltip.replaceChildren();
        const date = document.createElement('strong');
        date.textContent = nearest.label;
        tooltip.append(date, document.createElement('br'), `Subscriptions: ${nearest.value}`);
        tooltip.style.left = `${nearest.x}px`;
        tooltip.style.top = `${nearest.y}px`;
        tooltip.hidden = false;
    });
    canvas.addEventListener('mouseleave', hideTooltip);

    if ('ResizeObserver' in window) {
        new ResizeObserver(draw).observe(container);
    } else {
        window.addEventListener('resize', draw);
    }
    draw();
});
