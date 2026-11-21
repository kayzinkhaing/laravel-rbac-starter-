import "./bootstrap";

import Alpine from "alpinejs";

window.Alpine = Alpine;

Alpine.start();

document.querySelectorAll("[data-live-search]").forEach((form) => {
    const results = document.querySelector(form.dataset.results);
    const status = form.parentElement.querySelector("[data-live-status]");
    if (!results || !status) return;

    let timer;
    let controller;
    let requestNumber = 0;

    const buildUrl = () => {
        const url = new URL(form.action, window.location.href);
        const params = new URLSearchParams();

        new FormData(form).forEach((value, key) => {
            if (typeof value === "string" && value.trim() !== "") params.set(key, value);
        });

        url.search = params.toString();
        return url;
    };

    const loadResults = async (url = buildUrl()) => {
        clearTimeout(timer);
        controller?.abort();
        controller = new AbortController();
        const currentRequest = ++requestNumber;
        results.setAttribute("aria-busy", "true");
        status.setAttribute("role", "status");
        status.classList.remove("text-rose-700");
        status.textContent = "Loading results…";

        try {
            const response = await fetch(url, {
                headers: { "X-Requested-With": "XMLHttpRequest", Accept: "text/html" },
                signal: controller.signal,
            });
            if (response.redirected || !response.ok) throw new Error(`Request failed (${response.status}).`);

            results.innerHTML = await response.text();
            window.history.replaceState({}, "", url);
            status.textContent = "Results updated.";
        } catch (error) {
            if (error.name === "AbortError" || currentRequest !== requestNumber) return;

            status.setAttribute("role", "alert");
            status.classList.add("text-rose-700");
            status.textContent = "Could not load results. Please try again.";
        } finally {
            if (currentRequest === requestNumber) results.setAttribute("aria-busy", "false");
        }
    };

    form.addEventListener("submit", (event) => {
        event.preventDefault();
        clearTimeout(timer);
        loadResults();
    });

    form.addEventListener("input", (event) => {
        if (!event.target.matches("[data-live-input]")) return;
        clearTimeout(timer);
        timer = setTimeout(() => loadResults(), 300);
    });

    form.addEventListener("change", (event) => {
        if (event.target.matches("[data-live-filter]")) loadResults();
    });

    form.addEventListener("click", (event) => {
        const clearButton = event.target.closest("[data-live-clear]");
        if (clearButton) {
            event.preventDefault();
            form.querySelectorAll('input[type="search"], select').forEach((input) => { input.value = ""; });
            form.querySelector('[name="sort"]').value = form.dataset.defaultSort;
            form.querySelector('[name="direction"]').value = form.dataset.defaultDirection;
            loadResults();
            return;
        }

        const sortButton = event.target.closest("[data-sort]");
        if (sortButton) {
            const sortInput = form.querySelector('[name="sort"]');
            const directionInput = form.querySelector('[name="direction"]');
            directionInput.value = sortInput.value === sortButton.dataset.sort
                ? (directionInput.value === "asc" ? "desc" : "asc")
                : form.dataset.defaultDirection;
            sortInput.value = sortButton.dataset.sort;
            loadResults();
        }
    });

    results.addEventListener("click", (event) => {
        const link = event.target.closest("a[href]");
        if (!link || !link.href) return;

        const url = new URL(link.href, window.location.href);
        if (url.origin !== window.location.origin || url.pathname !== new URL(form.action).pathname || !url.searchParams.has("page")) return;

        event.preventDefault();
        loadResults(url);
    });
});
