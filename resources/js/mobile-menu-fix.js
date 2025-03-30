// Direct mobile menu fix
document.addEventListener('DOMContentLoaded', function() {
    // Get references to the mobile menu elements
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuOpenIcon = document.getElementById('menu-open-icon');
    const menuCloseIcon = document.getElementById('menu-close-icon');
    
    if (mobileMenuButton && mobileMenu) {
        console.log('Mobile menu elements found');
        
        // Set up a direct click handler for the mobile menu button
        mobileMenuButton.addEventListener('click', function() {
            console.log('Mobile menu button clicked');
            
            // Toggle the menu visibility
            const isMenuVisible = mobileMenu.classList.contains('hidden');
            
            if (isMenuVisible) {
                // Show the menu
                mobileMenu.classList.remove('hidden');
                menuOpenIcon.classList.add('hidden');
                menuCloseIcon.classList.remove('hidden');
                mobileMenuButton.setAttribute('aria-expanded', 'true');
                console.log('Mobile menu shown');
            } else {
                // Hide the menu
                mobileMenu.classList.add('hidden');
                menuOpenIcon.classList.remove('hidden');
                menuCloseIcon.classList.add('hidden');
                mobileMenuButton.setAttribute('aria-expanded', 'false');
                console.log('Mobile menu hidden');
            }
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!mobileMenu.contains(event.target) && 
                !mobileMenuButton.contains(event.target) && 
                !mobileMenu.classList.contains('hidden')) {
                
                // Hide the menu
                mobileMenu.classList.add('hidden');
                menuOpenIcon.classList.remove('hidden');
                menuCloseIcon.classList.add('hidden');
                mobileMenuButton.setAttribute('aria-expanded', 'false');
                console.log('Mobile menu hidden (clicked outside)');
            }
        });
    } else {
        console.error('Mobile menu elements not found');
    }
});
