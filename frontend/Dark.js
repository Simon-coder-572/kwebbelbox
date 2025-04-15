// Function dark mode moet nog gekoppeld worden aan settings database
function toggleDarkMode() {
    // Toggle the dark-mode class on the body element
    document.body.classList.toggle("dark-mode");

    // Save the dark mode status in localStorage
    if (document.body.classList.contains("dark-mode")) {
        localStorage.setItem("darkMode", "enabled");
    } else {
        localStorage.setItem("darkMode", "disabled");
    }
}

// controle of darkmode al aan stond
if (localStorage.getItem("darkMode") === "enabled") {
    document.body.classList.add("dark-mode");
}