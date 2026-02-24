import './bootstrap';

const METRICS_TO_TRACK = ['LCP', 'INP', 'CLS'];

const pendingMetrics = new Map();
let flushTimeout = null;

const getEndpoint = () => document.querySelector('meta[name="web-vitals-endpoint"]')?.content ?? null;

const getConnectionType = () => {
    const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;

    if (!connection) {
        return null;
    }

    return connection.effectiveType || connection.type || null;
};

const normalizeMetricValue = (metricName, value) => {
    if (metricName === 'CLS') {
        return Math.round(value * 10000) / 10000;
    }

    return Math.round(value);
};

const queueMetric = (metricName, value) => {
    if (!METRICS_TO_TRACK.includes(metricName)) {
        return;
    }

    pendingMetrics.set(metricName, {
        path: window.location.pathname,
        metric: metricName,
        value: normalizeMetricValue(metricName, value),
        connection_type: getConnectionType(),
        recorded_at: new Date().toISOString(),
    });

    scheduleFlush();
};

const scheduleFlush = () => {
    if (flushTimeout) {
        return;
    }

    flushTimeout = window.setTimeout(() => {
        flushTimeout = null;
        sendMetrics();
    }, 1200);
};

const sendMetrics = () => {
    const endpoint = getEndpoint();

    if (!endpoint || pendingMetrics.size === 0) {
        return;
    }

    for (const payload of pendingMetrics.values()) {
        try {
            const body = JSON.stringify(payload);

            if (navigator.sendBeacon) {
                const blob = new Blob([body], { type: 'application/json' });
                navigator.sendBeacon(endpoint, blob);
            } else {
                fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body,
                    keepalive: true,
                    credentials: 'same-origin',
                }).catch(() => {});
            }
        } catch {
            // swallow telemetry errors
        }
    }

    pendingMetrics.clear();
};

const observeLcp = () => {
    let latestEntry = null;

    const observer = new PerformanceObserver((list) => {
        const entries = list.getEntries();
        latestEntry = entries[entries.length - 1];
    });

    observer.observe({ type: 'largest-contentful-paint', buffered: true });

    const finalize = () => {
        if (!latestEntry) {
            return;
        }

        queueMetric('LCP', latestEntry.startTime);
        observer.disconnect();
    };

    window.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'hidden') {
            finalize();
        }
    }, { once: true });

    window.addEventListener('pagehide', finalize, { once: true });
};

const observeCls = () => {
    let clsValue = 0;

    const observer = new PerformanceObserver((list) => {
        for (const entry of list.getEntries()) {
            if (!entry.hadRecentInput) {
                clsValue += entry.value;
            }
        }
    });

    observer.observe({ type: 'layout-shift', buffered: true });

    const finalize = () => {
        queueMetric('CLS', clsValue);
        observer.disconnect();
    };

    window.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'hidden') {
            finalize();
        }
    }, { once: true });

    window.addEventListener('pagehide', finalize, { once: true });
};

const observeInp = () => {
    let maxInteraction = 0;

    const observer = new PerformanceObserver((list) => {
        for (const entry of list.getEntries()) {
            if (entry.interactionId && entry.duration > maxInteraction) {
                maxInteraction = entry.duration;
            }
        }
    });

    observer.observe({ type: 'event', buffered: true, durationThreshold: 40 });

    const finalize = () => {
        if (maxInteraction > 0) {
            queueMetric('INP', maxInteraction);
        }

        observer.disconnect();
    };

    window.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'hidden') {
            finalize();
        }
    }, { once: true });

    window.addEventListener('pagehide', finalize, { once: true });
};

const startWebVitalsTracking = () => {
    if (!('PerformanceObserver' in window)) {
        return;
    }

    observeLcp();
    observeCls();
    observeInp();

    window.addEventListener('beforeunload', sendMetrics);
    window.addEventListener('pagehide', sendMetrics);
};

startWebVitalsTracking();
