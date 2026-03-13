async function renderPortfolio() {
  const target = document.getElementById("portfolio-cards");
  if (!target) return;

  try {
    const response = await fetch("assets/data/portfolio-projects.json", { cache: "no-store" });
    const projects = await response.json();

    projects.forEach((project) => {
      const card = document.createElement("article");
      card.className = "portfolio-card";
      card.innerHTML = `
        <h3>${project.title}</h3>
        <p>${project.summary}</p>
        <p><strong>Outcome:</strong> ${project.result}</p>
      `;
      target.appendChild(card);
    });
  } catch (error) {
    console.error("Portfolio data failed to load:", error);
  }
}

document.addEventListener("DOMContentLoaded", renderPortfolio);
