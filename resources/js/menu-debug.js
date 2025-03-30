// Debug script to check mobile menu functionality
console.log('Menu debug script loaded');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded');
    
    // Check if mobile menu elements exist
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    
    console.log('Mobile menu button exists:', !!mobileMenuButton);
    console.log('Mobile menu exists:', !!mobileMenu);
    
    // Add direct event listener for testing
    if (mobileMenuButton && mobileMenu) {
        console.log('Adding click event listener to mobile menu button');
        
        mobileMenuButton.addEventListener('click', function() {
            console.log('Mobile menu button clicked');
            
            // Force toggle the mobile menu visibility
            if (mobileMenu.classList.contains('hidden')) {
                console.log('Showing mobile menu');
                mobileMenu.classList.remove('hidden');
            } else {
                console.log('Hiding mobile menu');
                mobileMenu.classList.add('hidden');
            }
        });
    }
});
