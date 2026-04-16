// ================= PAGE LOAD =================
window.addEventListener("load", () => {
  document.body.classList.add("loaded");
});

// ================= SAFE NAVIGATION TRANSITION =================
function go(url) {
  document.body.classList.remove("loaded");
  document.body.classList.add("fade-out");

  setTimeout(() => {
    window.location.href = url;
  }, 300);
}

// intercept links ONLY after DOM ready
document.addEventListener("DOMContentLoaded", () => {

  // auth guard
  const page = window.location.pathname.split('/').pop();
  if (page !== "login.html") {
    const user = JSON.parse(localStorage.getItem("logged_user") || "null");
    if (!user) window.location.href = "login.html";
  }

  // link interception
  document.querySelectorAll("a[href]").forEach(link => {
    const url = link.getAttribute("href");

    if (
      !url ||
      url.startsWith("#") ||
      url.startsWith("javascript") ||
      url.startsWith("http")
    ) return;

    link.addEventListener("click", (e) => {
      e.preventDefault();
      go(url);
    });
  });

  // logout fix
  document.querySelectorAll(".logout").forEach(el => {
    el.addEventListener("click", (e) => {
      e.preventDefault();

      document.body.classList.add("fade-out");

      setTimeout(() => {
        localStorage.removeItem("logged_user");
        window.location.href = "login.html";
      }, 300);
    });
  });

  // sidebar toggle
  const toggle = document.querySelector(".toggle");
  const nav = document.querySelector(".navigation");
  const main = document.querySelector(".main");

  if (toggle && nav && main) {
    toggle.addEventListener("click", () => {
      nav.classList.toggle("active");
      main.classList.toggle("active");
    });
  }
});