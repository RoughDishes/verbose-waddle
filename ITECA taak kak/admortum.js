
document.addEventListener('DOMContentLoaded', function() {
    

  
    var uploadProductModal = document.getElementById('uploadProductModal');
    var openUploadProductModalBtn = document.getElementById('openUploadProductModalBtn'); 
    var closeUploadProductModalBtn = document.querySelector('.closeUploadProductModalBtn');

    
    if (openUploadProductModalBtn) {
        openUploadProductModalBtn.onclick = function() {
            uploadProductModal.style.display = "block";
        }
    }

    
    if (closeUploadProductModalBtn) {
        closeUploadProductModalBtn.onclick = function() {
            uploadProductModal.style.display = "none";
        }
    }

    
    window.addEventListener('click', function(event) {
        if (event.target == uploadProductModal) {
            uploadProductModal.style.display = "none";
        }
    });

    
});

document.addEventListener('DOMContentLoaded', function() {
    
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
        if (event.target == authModal) {
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

    
    const profilePicModal = document.getElementById('profilePicModal');
    const openProfileModalBtn = document.getElementById('openProfileModalBtn');
    const closeProfilePicModalBtn = document.getElementsByClassName('closeProfilePicModalBtn')[0];

    
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
        if (event.target == profilePicModal) {
            profilePicModal.style.display = 'none';
        }
    });

    const flashMessage = document.getElementById('flashMessage');
    if (flashMessage) {
        setTimeout(() => {
            flashMessage.style.display = 'none';
        }, 3000);
    }

    
    const profilePicUpload = document.getElementById('profilePicUpload');
    if (profilePicUpload) { 
        profilePicUpload.addEventListener('change', function() {
            var fileName = this.files[0] ? this.files[0].name : "Choose New Picture";
            var customFileUploadLabel = profilePicModal.querySelector('.custom-file-upload');
            if (customFileUploadLabel) {
                customFileUploadLabel.textContent = fileName;
            }
        });
    }
});