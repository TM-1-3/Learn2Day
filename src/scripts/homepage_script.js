const openButton = document.getElementById("profile-button");
const profile = document.getElementById("profile-inner")

document.querySelectorAll('.card').forEach(card => {
    card.addEventListener('click', (e) => {
        e.stopPropagation();
        window.location.href = card.getAttribute('onclick').match(/href='([^']+)'/)[1];
    });
});

openButton.addEventListener("click", (e) => {
    e.stopPropagation();
    profile.classList.toggle("open");
});

document.addEventListener("click", (e) => {
    if (!profile.contains(e.target) && e.target !== openButton) {
        profile.classList.remove("open");
    }
});

document.querySelector('.filter-button').addEventListener('click', (e) => {
    e.stopPropagation();
    // Submit the form when filter options are selected
    document.querySelector('.search-bar form').submit();
});