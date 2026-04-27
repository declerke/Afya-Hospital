document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function() {
            const navLinks = document.querySelector('.nav-links');
            
            // Create mobile menu if it doesn't exist
            if (!document.querySelector('.mobile-nav')) {
                const mobileNav = document.createElement('div');
                mobileNav.className = 'mobile-nav';
                
                // Add styles to mobile nav
                mobileNav.style.position = 'fixed';
                mobileNav.style.top = '0';
                mobileNav.style.left = '0';
                mobileNav.style.width = '100%';
                mobileNav.style.height = '100vh';
                mobileNav.style.backgroundColor = 'white';
                mobileNav.style.padding = '2rem';
                mobileNav.style.zIndex = '1000';
                mobileNav.style.display = 'none';
                mobileNav.style.flexDirection = 'column';
                
                // Clone the navigation links
                const navClone = navLinks.cloneNode(true);
                navClone.style.display = 'flex';
                navClone.style.flexDirection = 'column';
                navClone.style.gap = '1.5rem';
                navClone.style.marginBottom = '2rem';
                
                // Add phone number to mobile menu
                const phoneNumber = document.querySelector('.phone-number').cloneNode(true);
                phoneNumber.style.display = 'flex';
                phoneNumber.style.marginBottom = '1.5rem';
                
                // Add appointment button
                const appointmentBtn = document.querySelector('.header-right .btn-primary').cloneNode(true);
                appointmentBtn.style.display = 'inline-flex';
                appointmentBtn.style.marginBottom = '2rem';
                
                mobileNav.appendChild(navClone);
                mobileNav.appendChild(phoneNumber);
                mobileNav.appendChild(appointmentBtn);
                
                document.body.appendChild(mobileNav);
                
                // Add close button
                const closeBtn = document.createElement('button');
                closeBtn.className = 'mobile-nav-close';
                closeBtn.innerHTML = '&times;';
                closeBtn.style.position = 'absolute';
                closeBtn.style.top = '1rem';
                closeBtn.style.right = '1rem';
                closeBtn.style.background = 'none';
                closeBtn.style.border = 'none';
                closeBtn.style.fontSize = '2rem';
                closeBtn.style.cursor = 'pointer';
                mobileNav.prepend(closeBtn);
                
                closeBtn.addEventListener('click', function() {
                    mobileNav.style.display = 'none';
                });
            }
            
            // Toggle mobile nav
            const mobileNav = document.querySelector('.mobile-nav');
            mobileNav.style.display = mobileNav.style.display === 'flex' ? 'none' : 'flex';
        });
    }
    
    // Testimonials slider
    const testimonialsSlider = document.querySelector('.testimonials-slider');
    
    if (testimonialsSlider) {
        // Auto scroll testimonials
        let scrollPosition = 0;
        const testimonialCards = document.querySelectorAll('.testimonial-card');
        const cardWidth = testimonialCards[0].offsetWidth + 32; // Card width + gap
        
        setInterval(() => {
            scrollPosition += cardWidth;
            
            // Reset scroll position when reaching the end
            if (scrollPosition >= testimonialsSlider.scrollWidth - testimonialsSlider.offsetWidth) {
                scrollPosition = 0;
            }
            
            testimonialsSlider.scrollTo({
                left: scrollPosition,
                behavior: 'smooth'
            });
        }, 5000);
    }
    
    // Chatbot functionality
    const chatbotButton = document.getElementById('chatbot-button');
    const chatbotContainer = document.getElementById('chatbot-container');
    const chatbotClose = document.getElementById('chatbot-close');
    const chatbotMessages = document.getElementById('chatbot-messages');
    const chatbotInput = document.getElementById('chatbot-input-field');
    const chatbotSend = document.getElementById('chatbot-send');
    
    if (chatbotButton && chatbotContainer) {
        // Toggle chatbot
        chatbotButton.addEventListener('click', function() {
            chatbotContainer.style.display = chatbotContainer.style.display === 'flex' ? 'none' : 'flex';
        });
        
        // Close chatbot
        chatbotClose.addEventListener('click', function() {
            chatbotContainer.style.display = 'none';
        });
        
        // Send message
        function sendMessage() {
            const message = chatbotInput.value.trim();
            
            if (message) {
                // Add user message
                addMessage(message, 'user');
                
                // Clear input
                chatbotInput.value = '';
                
                // Process message and get response
                processMessage(message);
            }
        }
        
        // Send message on button click
        chatbotSend.addEventListener('click', sendMessage);
        
        // Send message on Enter key
        chatbotInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
        
        // Add message to chat
        function addMessage(message, sender) {
            const messageElement = document.createElement('div');
            messageElement.className = `message ${sender}-message`;
            
            const messageContent = document.createElement('div');
            messageContent.className = 'message-content';
            messageContent.textContent = message;
            
            const messageTime = document.createElement('div');
            messageTime.className = 'message-time';
            
            // Get current time
            const now = new Date();
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            messageTime.textContent = `${hours}:${minutes}`;
            
            messageElement.appendChild(messageContent);
            messageElement.appendChild(messageTime);
            
            chatbotMessages.appendChild(messageElement);
            
            // Scroll to bottom
            chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
        }
        
        // Process message and get response from server
        function processMessage(message) {
            fetch('chatbot-process.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: message })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    addMessage(data.response, 'bot');
                } else {
                    addMessage("Sorry, I couldn't process that. Please try again or call 1-800-AFYA.", 'bot');
                }
            })
            .catch(() => {
                addMessage("Sorry, something went wrong. Please call us at 1-800-AFYA for assistance.", 'bot');
            });
        }
    }
});