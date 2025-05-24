const openButton = document.getElementById("profile-button");
const profile = document.getElementById("profile-inner");
const filterButton = document.querySelector('.filter-button');
const filterOptions = document.querySelector('.filter-options');
const logoutForm = document.querySelector('#profile-inner form');

// Profile dropdown logic
openButton.addEventListener("click", (e) => {
    e.preventDefault(); // Prevent form submission or button default
    e.stopPropagation();
    profile.classList.toggle("open");
});

// Close dropdowns when clicking outside
document.addEventListener("click", (e) => {
    if (!profile.contains(e.target) && e.target !== openButton) {
        profile.classList.remove("open");
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
        e.stopPropagation(); // Prevent event from bubbling up
        // The form will submit normally to actions/logout.php
    });
}

/*Navbar Logic*/