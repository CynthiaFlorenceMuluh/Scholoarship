document.addEventListener("DOMContentLoaded", () => {
    const body = document.body;
    const panel = document.getElementById("accessibilityPanel");
    const trigger = document.querySelector(".accessibility-trigger");
    const close = document.querySelector(".panel-close");

    const defaults = {
        fontSize: "normal",
        contrast: false,
        motion: false,
        simple: false
    };

    const getPrefs = () => {
        try {
            return {...defaults, ...JSON.parse(localStorage.getItem("shopEaseAccessibility") || "{}")};
        } catch {
            return {...defaults};
        }
    };

    const savePrefs = prefs => {
        localStorage.setItem("shopEaseAccessibility", JSON.stringify(prefs));
        document.getElementById("settingsSaved")?.replaceChildren(document.createTextNode("Preferences saved."));
        setTimeout(() => {
            const el = document.getElementById("settingsSaved");
            if (el) el.textContent = "";
        }, 1800);
    };

    const applyPrefs = prefs => {
        body.classList.remove("large", "xlarge", "high-contrast", "reduced-motion", "simplified");
        if (prefs.fontSize === "large") body.classList.add("large");
        if (prefs.fontSize === "xlarge") body.classList.add("xlarge");
        if (prefs.contrast) body.classList.add("high-contrast");
        if (prefs.motion) body.classList.add("reduced-motion");
        if (prefs.simple) body.classList.add("simplified");

        const font = document.getElementById("fontSizeSetting");
        const contrast = document.getElementById("contrastSetting");
        const motion = document.getElementById("motionSetting");
        const simple = document.getElementById("simpleSetting");
        if (font) font.value = prefs.fontSize;
        if (contrast) contrast.checked = prefs.contrast;
        if (motion) motion.checked = prefs.motion;
        if (simple) simple.checked = prefs.simple;
    };

    let prefs = getPrefs();
    applyPrefs(prefs);

    if (trigger && panel) {
        trigger.addEventListener("click", () => {
            const isOpen = !panel.hasAttribute("hidden");
            if (isOpen) {
                panel.setAttribute("hidden", "");
                trigger.setAttribute("aria-expanded", "false");
            } else {
                panel.removeAttribute("hidden");
                trigger.setAttribute("aria-expanded", "true");
                document.getElementById("fontSizeSetting")?.focus();
            }
        });
    }

    close?.addEventListener("click", () => {
        panel.setAttribute("hidden", "");
        trigger?.setAttribute("aria-expanded", "false");
        trigger?.focus();
    });

    document.getElementById("fontSizeSetting")?.addEventListener("change", e => {
        prefs.fontSize = e.target.value;
        applyPrefs(prefs); savePrefs(prefs);
    });
    document.getElementById("contrastSetting")?.addEventListener("change", e => {
        prefs.contrast = e.target.checked;
        applyPrefs(prefs); savePrefs(prefs);
    });
    document.getElementById("motionSetting")?.addEventListener("change", e => {
        prefs.motion = e.target.checked;
        applyPrefs(prefs); savePrefs(prefs);
    });
    document.getElementById("simpleSetting")?.addEventListener("change", e => {
        prefs.simple = e.target.checked;
        applyPrefs(prefs); savePrefs(prefs);
    });
    document.getElementById("resetAccessibility")?.addEventListener("click", () => {
        prefs = {...defaults};
        localStorage.setItem("shopEaseAccessibility", JSON.stringify(prefs));
        applyPrefs(prefs);
        savePrefs(prefs);
    });

    document.querySelectorAll(".password-toggle").forEach(btn => {
        btn.addEventListener("click", () => {
            const input = document.getElementById(btn.dataset.target);
            if (!input) return;
            const showing = input.type === "text";
            input.type = showing ? "password" : "text";
            btn.textContent = showing ? "Show" : "Hide";
            btn.setAttribute("aria-label", showing ? "Show password" : "Hide password");
        });
    });

    document.querySelectorAll("[data-confirm]").forEach(el => {
        el.addEventListener("click", e => {
            if (!confirm(el.dataset.confirm)) e.preventDefault();
        });
    });

    document.querySelectorAll("[data-modal-open]").forEach(btn => {
        btn.addEventListener("click", () => {
            document.getElementById(btn.dataset.modalOpen)?.classList.add("open");
        });
    });
    document.querySelectorAll("[data-modal-close]").forEach(btn => {
        btn.addEventListener("click", () => {
            btn.closest(".modal-backdrop")?.classList.remove("open");
        });
    });
    document.querySelectorAll(".modal-backdrop").forEach(backdrop => {
        backdrop.addEventListener("click", e => {
            if (e.target === backdrop) backdrop.classList.remove("open");
        });
    });

    // Profile image preview
    const avatarInput = document.getElementById("profileImage");
    const avatarPreview = document.getElementById("avatarPreview");
    avatarInput?.addEventListener("change", () => {
        const file = avatarInput.files?.[0];
        if (!file || !avatarPreview) return;
        const reader = new FileReader();
        reader.onload = e => avatarPreview.src = e.target.result;
        reader.readAsDataURL(file);
    });
});
