// Theme initialization on page load
document.addEventListener('DOMContentLoaded', function() {
    // Check for saved theme preference or use the system preference
    if (localStorage.getItem('color-theme') === 'dark' || 
        (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
    
    // Log that Alpine.js is handling the mobile menu and theme toggle
    console.log('Mobile menu and theme toggle are now handled by Alpine.js');
});
