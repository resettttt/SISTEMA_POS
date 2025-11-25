// POS System JavaScript

// Function to handle dynamic functionality
document.addEventListener('DOMContentLoaded', function() {
    // Add any global JavaScript functionality here
    console.log('POS System loaded');
    
    // Handle active navigation
    const currentUrl = window.location.href;
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    
    navLinks.forEach(link => {
        if (currentUrl.includes(link.getAttribute('href'))) {
            link.classList.add('active');
        }
    });
});