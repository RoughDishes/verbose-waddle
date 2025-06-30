document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM content loaded. Initializing mainscript.js...");

    function showFlashMessage(message, type = 'info') {
        const flashMessageDiv = document.getElementById('flashMessage');
        if (flashMessageDiv) {
            flashMessageDiv.textContent = message;
            flashMessageDiv.className = 'flash-message ' + type;
            flashMessageDiv.style.display = 'block';
            setTimeout(() => {
                flashMessageDiv.style.display = 'none';
            }, 3000);
        } else {
            console.warn("Flash message div not found. Message: ", message);
        }
    }

    const uploadProductModal = document.getElementById('uploadProductModal');
    const openUploadProductModalBtn = document.getElementById('openUploadProductModalBtn');
    const closeUploadProductModalBtn = document.querySelector('#uploadProductModal .close');

    if (openUploadProductModalBtn) {
        openUploadProductModalBtn.addEventListener('click', function() {
            uploadProductModal.style.display = "flex";
        });
    }

    if (closeUploadProductModalBtn) {
        closeUploadProductModalBtn.addEventListener('click', function() {
            uploadProductModal.style.display = "none";
        });
    }

    window.addEventListener('click', function(event) {
        if (event.target === uploadProductModal) {
            uploadProductModal.style.display = "none";
        }
    });

    const productImageUpload = document.getElementById('productImageUpload');
    if (productImageUpload) {
        productImageUpload.addEventListener('change', function() {
            const fileName = this.files[0] ? this.files[0].name : "Choose Product Image";
            const customFileUploadLabel = this.previousElementSibling;
            if (customFileUploadLabel && customFileUploadLabel.classList.contains('custom-file-upload')) {
                customFileUploadLabel.textContent = fileName;
            }
        });
    }

    const uploadProductForm = document.getElementById('uploadProductForm');
    if (uploadProductForm) {
        uploadProductForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            const formData = new FormData(this);

            try {
                const response = await fetch('/api/upload_product.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.status === 'success') {
                    showFlashMessage(data.message, 'success');
                    uploadProductModal.style.display = 'none';
                    uploadProductForm.reset();
                } else {
                    showFlashMessage(data.message, 'error');
                }
            } catch (error) {
                console.error('Error uploading product:', error);
                showFlashMessage('Network error: Could not upload product.', 'error');
            }
        });
    }


    const authModal = document.getElementById('authModal');
    const loginButton = document.getElementById('login');
    const closeModalAuth = document.querySelector('#authModal .close');
    const loginSection = document.getElementById('loginSection');
    const registerSection = document.getElementById('registerSection');
    const switchToRegisterLink = document.getElementById('switchToRegister');
    const switchToLoginLink = document.getElementById('switchToLogin');

    if (loginButton) {
        loginButton.addEventListener('click', function() {
            authModal.style.display = 'flex';
            loginSection.classList.add('active');
            registerSection.classList.remove('active');
        });
    }

    if (closeModalAuth) {
        closeModalAuth.addEventListener('click', function() {
            authModal.style.display = 'none';
        });
    }

    window.addEventListener('click', function(event) {
        if (event.target === authModal) {
            authModal.style.display = 'none';
        }
    });

    if (switchToRegisterLink) {
        switchToRegisterLink.addEventListener('click', function(event) {
            event.preventDefault();
            loginSection.classList.remove('active');
            registerSection.classList.add('active');
        });
    }

    if (switchToLoginLink) {
        switchToLoginLink.addEventListener('click', function(event) {
            event.preventDefault();
            registerSection.classList.remove('active');
            loginSection.classList.add('active');
        });
    }

    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            const formData = new FormData(this);

            try {
                const response = await fetch('/api/login.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.status === 'success') {
                    showFlashMessage(data.message, 'success');
                    authModal.style.display = 'none';
                    window.location.reload();
                } else {
                    showFlashMessage(data.message, 'error');
                }
            } catch (error) {
                console.error('Error logging in:', error);
                showFlashMessage('Network error: Could not log in.', 'error');
            }
        });
    }

    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            const password = formData.get('password');
            const confirmPassword = formData.get('confirm_password');

            if (password !== confirmPassword) {
                showFlashMessage('Passwords do not match.', 'error');
                return;
            }

            try {
                const response = await fetch('/api/register.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.status === 'success') {
                    showFlashMessage(data.message, 'success');
                    loginSection.classList.add('active');
                    registerSection.classList.remove('active');
                    registerForm.reset();
                } else {
                    showFlashMessage(data.message, 'error');
                }
            } catch (error) {
                console.error('Error registering:', error);
                showFlashMessage('Network error: Could not register.', 'error');
            }
        });
    }


    const profilePicModal = document.getElementById('profilePicModal');
    const openProfileModalBtn = document.getElementById('openProfileModalBtn');
    const closeProfilePicModalBtn = document.querySelector('#profilePicModal .close');

    if (openProfileModalBtn) {
        openProfileModalBtn.addEventListener('click', function() {
            profilePicModal.style.display = 'flex';
        });
    }

    if (closeProfilePicModalBtn) {
        closeProfilePicModalBtn.addEventListener('click', function() {
            profilePicModal.style.display = 'none';
        });
    }

    window.addEventListener('click', function(event) {
        if (event.target === profilePicModal) {
            profilePicModal.style.display = 'none';
        }
    });

    const profilePicUpload = document.getElementById('profilePicUpload');
    if (profilePicUpload) {
        profilePicUpload.addEventListener('change', function() {
            const fileName = this.files[0] ? this.files[0].name : "Choose New Picture";
            const customFileUploadLabel = this.previousElementSibling;
            if (customFileUploadLabel && customFileUploadLabel.classList.contains('custom-file-upload')) {
                customFileUploadLabel.textContent = fileName;
            }
        });
    }

    const profilePicForm = document.getElementById('profilePicForm');
    if (profilePicForm) {
        profilePicForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            const formData = new FormData(this);

            try {
                const response = await fetch('/api/upload_profile_pic.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.status === 'success') {
                    showFlashMessage(data.message, 'success');
                    profilePicModal.style.display = 'none';
                    profilePicForm.reset();
                    const headerProfilePic = document.querySelector('.user-actions .profile-pic');
                    if (headerProfilePic && data.new_pic_url) {
                        headerProfilePic.src = data.new_pic_url;
                    }
                } else {
                    showFlashMessage(data.message, 'error');
                }
            } catch (error) {
                console.error('Error uploading profile picture:', error);
                showFlashMessage('Network error: Could not upload profile picture.', 'error');
            }
        });
    }

    const productCards = document.querySelectorAll('.product-card');
    if (productCards && productCards.length > 0) {
        console.log("Product cards found. Attaching click listeners.");
        productCards.forEach(card => {
            card.addEventListener('click', function() {
                console.log("Product Card Clicked (no chat action defined).");
            });
        });
    }

});