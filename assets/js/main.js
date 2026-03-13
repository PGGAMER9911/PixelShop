const services = [
  {
    title: "Minecraft Cinematic Video Creation",
    description: "Storyboarding, cinematic capture, and post-production that increases watch time.",
  },
  {
    title: "Minecraft Server Setup",
    description: "Stable, scalable setup with optimized performance and secure deployment.",
  },
  {
    title: "Minecraft Plugin Development",
    description: "Custom gameplay systems and utility plugins built for maintainability.",
  },
  {
    title: "Video Editing",
    description: "Retention-focused edits for long-form YouTube content and short clips.",
  },
];

function createServiceCard(service) {
  const article = document.createElement("article");
  article.className = "service-card";
  article.innerHTML = `
    <h3>${service.title}</h3>
    <p>${service.description}</p>
    <a class="text-link" href="contact.html">Request this service</a>
  `;
  return article;
}

function renderServices() {
  ["home-service-cards", "service-cards"].forEach((id) => {
    const container = document.getElementById(id);
    if (!container) return;

    services.forEach((service) => {
      container.appendChild(createServiceCard(service));
    });
  });
}

document.addEventListener("DOMContentLoaded", renderServices);
