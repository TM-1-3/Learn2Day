const container = document.getElementById("container");
const overlayBtn = document.getElementById("overlayBtn");
const urlParams = new URLSearchParams(window.location.search);

window.addEventListener('DOMContentLoaded', function() {
  if (urlParams.has('student')) {
    container.classList.add('right-panel-active');
    overlayBtn.textContent = "Register as Tutor";
    overlayBtn.style.color = "#32533D";
  } else if (urlParams.has('tutor')) {
    overlayBtn.textContent = "Register as Student";
    overlayBtn.style.color = '#03254E';
  }
});

overlayBtn.addEventListener("click", () => {
  container.classList.toggle("right-panel-active");

  overlayBtn.textContent = container.classList.contains("right-panel-active")
    ? "Register as Tutor"
    : "Register as Student";

  overlayBtn.style.color = container.classList.contains("right-panel-active")
    ? "#32533D"
    : "#03254E";
});

/*That cool animation that makes the login and register forms switch places*/
