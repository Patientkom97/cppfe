// ===== Menu mobile =====
const menuBtn = document.querySelector(".menu-btn");
const nav = document.getElementById("nav");

if (menuBtn && nav) {
  menuBtn.addEventListener("click", () => {
    const open = nav.classList.toggle("open");
    menuBtn.setAttribute("aria-expanded", open);
    menuBtn.textContent = open ? "Fermer" : "Menu";
  });
  nav.querySelectorAll("a").forEach((link) =>
    link.addEventListener("click", () => {
      nav.classList.remove("open");
      menuBtn.setAttribute("aria-expanded", "false");
      menuBtn.textContent = "Menu";
    })
  );
}

// ===== Formulaire de contact (enregistré dans le panel admin) =====
const form = document.getElementById("contact-form");

if (form) {
  const statusBox = document.getElementById("form-status");
  const button = form.querySelector("button[type=submit]");

  const setError = (field, message) => {
    const box = form.querySelector(`[data-for="${field}"]`);
    if (box) box.textContent = message;
  };
  const clearErrors = () => ["nom", "email", "message"].forEach((f) => setError(f, ""));

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    clearErrors();
    statusBox.textContent = "";
    statusBox.classList.remove("is-error");

    const nom = form.nom.value.trim();
    const email = form.email.value.trim();
    const message = form.message.value.trim();
    let valid = true;

    if (nom.length < 2) { setError("nom", "Indiquez votre nom."); valid = false; }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { setError("email", "Indiquez une adresse courriel valide."); valid = false; }
    if (message.length < 10) { setError("message", "Votre message doit contenir au moins 10 caractères."); valid = false; }
    if (!valid) return;

    button.disabled = true;
    try {
      const res = await fetch(form.action, { method: "POST", body: new FormData(form) });
      const data = await res.json();
      if (data.ok) {
        statusBox.textContent = "Merci, votre message a bien été envoyé. Nous vous répondrons rapidement.";
        form.reset();
      } else if (data.errors) {
        Object.entries(data.errors).forEach(([f, m]) => setError(f, m));
      } else {
        statusBox.textContent = data.error || "L’envoi a échoué. Réessayez.";
        statusBox.classList.add("is-error");
      }
    } catch (err) {
      statusBox.textContent = "Connexion impossible. Vérifiez votre réseau et réessayez.";
      statusBox.classList.add("is-error");
    } finally {
      button.disabled = false;
    }
  });
}
