function getBasePrefix() {
  return window.location.pathname.includes("/blog/") ? "../" : "";
}

async function injectComponent(targetId, componentPath) {
  const mount = document.getElementById(targetId);
  if (!mount) return;

  try {
    const response = await fetch(componentPath, { cache: "no-store" });
    if (!response.ok) throw new Error("Component request failed");
    mount.innerHTML = await response.text();
  } catch (error) {
    console.error("Component load error:", error);
  }
}

async function loadShell() {
  const base = getBasePrefix();
  await injectComponent("site-navbar", `${base}components/navbar.html`);
  await injectComponent("site-footer", `${base}components/footer.html`);

  const menuToggle = document.querySelector(".menu-toggle");
  const navLinks = document.getElementById("site-menu");
  if (menuToggle && navLinks) {
    menuToggle.addEventListener("click", () => {
      const expanded = menuToggle.getAttribute("aria-expanded") === "true";
      menuToggle.setAttribute("aria-expanded", String(!expanded));
      navLinks.classList.toggle("open");
    });
  }

  const currentPage = window.location.pathname.endsWith("/")
    ? "/index.html"
    : window.location.pathname;
  document.querySelectorAll(".nav-links a").forEach((link) => {
    const href = link.getAttribute("href");
    if (href === currentPage) {
      link.setAttribute("aria-current", "page");
    }
  });
}

loadShell();
