const container = document.getElementById("container");
const overlayBtn = document.getElementById("overlayBtn");

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
