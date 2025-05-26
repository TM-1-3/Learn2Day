const openButton = document.getElementById("profile-button");
const profile = document.getElementById("profile-inner");
const notificationButton = document.getElementById("notification-button");
const notificationPopup = document.getElementById("notification-inner");
const filterButton = document.querySelector('.filter-button');
const filterOptions = document.querySelector('.filter-options');
const logoutForm = document.querySelector('#profile-inner form');

// Profile dropdown logic
openButton.addEventListener("click", (e) => {
    e.preventDefault();
    e.stopPropagation();
    profile.classList.toggle("open");
    // Close notification popup when opening profile
    notificationPopup.classList.remove("open");
});

// Notifications dropdown logic
notificationButton.addEventListener("click", (e) => {
    e.preventDefault();
    e.stopPropagation();
    notificationPopup.classList.toggle("open");
    // Close profile popup when opening notifications
    profile.classList.remove("open");
});

// Close dropdowns when clicking outside
document.addEventListener("click", (e) => {
    if (!profile.contains(e.target) && e.target !== openButton) {
        profile.classList.remove("open");
    }
    if (!notificationPopup.contains(e.target) && e.target !== notificationButton) {
        notificationPopup.classList.remove("open");
    }
    if (!filterOptions.contains(e.target) && e.target !== filterButton) {
        filterOptions.style.display = 'none';
    }
});

// Filter button logic
if (filterButton && filterOptions) {
    filterButton.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();
        // Toggle filter options visibility
        if (filterOptions.style.display === 'block') {
            filterOptions.style.display = 'none';
        } else {
            filterOptions.style.display = 'block';
        }
        // Close other popups when opening filters
        profile.classList.remove("open");
        notificationPopup.classList.remove("open");
    });
}

// Card click behavior
document.querySelectorAll('.card').forEach(card => {
    card.addEventListener('click', (e) => {
        e.stopPropagation();
        const onclick = card.getAttribute('onclick');
        if (onclick) {
            const match = onclick.match(/href='([^']+)'/);
            if (match && match[1]) {
                window.location.href = match[1];
            }
        }
    });
});

// Prevent filter options from closing when clicking inside
if (filterOptions) {
    filterOptions.addEventListener('click', (e) => {
        e.stopPropagation();
    });
}

// Ensure logout form submits properly
if (logoutForm) {
    logoutForm.addEventListener('submit', (e) => {
        e.stopPropagation();
    });
}

/*Navbar Logic*/