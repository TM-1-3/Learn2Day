const openButton = document.getElementById("log-btn");
const profile = document.getElementById("profile-inner");
const overlay = document.getElementById("popup-overlay");

document.querySelectorAll(".card").forEach((card) => {
  card.addEventListener("click", (e) => {
    e.stopPropagation();
    window.location.href = card
      .getAttribute("onclick")
      .match(/href='([^']+)'/)[1];
  });
});

openButton.addEventListener("click", (e) => {
  e.stopPropagation();
  profile.classList.add("open");
  overlay.classList.toggle("open");
});

document.addEventListener("click", (e) => {
  if (!profile.contains(e.target) && e.target !== openButton) {
    profile.classList.remove("open");
    overlay.classList.remove("open");
  }
});
