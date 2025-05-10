function throttle(limit) {
    let lastRan = 0;

    return function () {
        const now = Date.now();
        if (now - lastRan >= limit) {
            fetch("reset_session.php", { method: "POST" });
            lastRan = now;
        }
    };
}

const throttledFetch = throttle(60000);

window.addEventListener("scroll", throttledFetch);
window.addEventListener("mousemove", throttledFetch);
window.addEventListener("keypress", throttledFetch);
window.addEventListener("click", throttledFetch);
