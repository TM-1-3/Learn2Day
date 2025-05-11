const openButton = document.getElementById("profile-button");
const profile = document.getElementById("profile-inner")

openButton.addEventListener("click", (e) => {
    e.stopPropagation();
    profile.classList.add("open");
});

document.addEventListener("click", (e) => {
    if (!profile.contains(e.target) && e.target !== openButton) {
        profile.classList.remove("open");
    }
});
