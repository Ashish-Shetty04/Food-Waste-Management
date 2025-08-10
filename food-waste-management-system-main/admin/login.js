document.addEventListener('DOMContentLoaded', function() {
    const container = document.querySelector(".container"),
          pwShowHide = document.querySelectorAll(".showHidePw"),
          pwFields = document.querySelectorAll("#password"),
          signUp = document.querySelector(".signup-link"),
          login = document.querySelector(".login-link");

    //   js code to show/hide password and change icon
    if (pwShowHide) {
        pwShowHide.forEach(eyeIcon =>{
            eyeIcon.addEventListener("click", ()=>{
                if (pwFields) {
                    pwFields.forEach(pwField =>{
                        if(pwField.type ==="password"){
                            pwField.type = "text";

                            pwShowHide.forEach(icon =>{
                                icon.classList.replace("uil-eye-slash", "uil-eye");
                            })
                        }else{
                            pwField.type = "password";

                            pwShowHide.forEach(icon =>{
                                icon.classList.replace("uil-eye", "uil-eye-slash");
                            })
                        }
                    }) 
                }
            })
        })
    }

    if (signUp) {
        signUp.addEventListener("click", ( )=>{
            if (container) {
                container.classList.add("active");
            }
        });
    }
    
    if (login) {
        login.addEventListener("click", ( )=>{
            if (container) {
                container.classList.remove("active");
            }
        });
    }
});
