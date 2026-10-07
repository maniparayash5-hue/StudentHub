document.addEventListener("DOMContentLoaded", function () {
    var isHomePage = document.title === "StudentHub - Home";

    // Dark mode button is shown only on the index page
    var themeButton;

    function applyTheme(dark) {
        document.body.style.backgroundColor = dark ? "#222" : "#f4f7fb";
        document.body.style.color = dark ? "white" : "#222";

        document.querySelectorAll("h1, h2, h3, p, label, td, summary").forEach(function (el) {
            el.style.color = dark ? "white" : "";
        });

        document.querySelectorAll("table, .content-box, .form-box, .loginpage, details").forEach(function (el) {
            el.style.backgroundColor = dark ? "#333" : "white";
            el.style.color = dark ? "white" : "#222";
        });

        document.querySelectorAll("input, select").forEach(function (el) {
            el.style.backgroundColor = dark ? "#444" : "";
            el.style.color = dark ? "white" : "";
        });

        if (themeButton) {
            themeButton.textContent = dark ? "Light Mode" : "Dark Mode";
        }
    }

    var savedTheme = localStorage.getItem("studentHubTheme") === "dark";
    applyTheme(savedTheme);

    if (isHomePage) {
        themeButton = document.createElement("button");
        themeButton.textContent = savedTheme ? "Light Mode" : "Dark Mode";
        themeButton.type = "button";
        themeButton.style.marginLeft = "auto";

        var header = document.querySelector(".header");
        if (header) {
            header.appendChild(themeButton);
        }

        themeButton.addEventListener("click", function () {
            var dark = localStorage.getItem("studentHubTheme") !== "dark";
            localStorage.setItem("studentHubTheme", dark ? "dark" : "light");
            applyTheme(dark);
        });

        // Simple notification banner
        var main = document.querySelector("main");
        if (main) {
            var notice = document.createElement("p");
            notice.innerHTML = "<strong>Notice:</strong> Welcome to StudentHub! Use the buttons below to explore the portal.";
            notice.setAttribute("role", "alert");
            main.insertBefore(notice, main.firstChild);

            // Simple content slider
            var slider = document.createElement("div");
            slider.innerHTML =
                "<h3>StudentHub Features</h3>" +
                "<p id='slideText'>Check your courses and assignments.</p>" +
                "<button id='previousSlide' type='button'>Previous</button> " +
                "<button id='nextSlide' type='button'>Next</button>";

            main.appendChild(slider);

            var slides = [
                "Check your courses and assignments.",
                "View your attendance and result.",
                "Use the FAQ section for common questions."
            ];
            var slide = 0;
            var slideText = document.getElementById("slideText");

            document.getElementById("nextSlide").addEventListener("click", function () {
                slide = (slide + 1) % slides.length;
                slideText.textContent = slides[slide];
            });

            document.getElementById("previousSlide").addEventListener("click", function () {
                slide = (slide - 1 + slides.length) % slides.length;
                slideText.textContent = slides[slide];
            });

            // Simple modal popup
            var modalButton = document.createElement("button");
            modalButton.textContent = "Open Notice";
            modalButton.type = "button";
            modalButton.style.margin = "15px 0";
            main.insertBefore(modalButton, slider);

            var dialog = document.createElement("dialog");
            dialog.innerHTML =
                "<h3>StudentHub Notice</h3>" +
                "<p>Welcome to the StudentHub Portal.</p>" +
                "<button id='closeDialog' type='button'>Close</button>";
            document.body.appendChild(dialog);

            modalButton.addEventListener("click", function () {
                dialog.showModal();
            });

            dialog.querySelector("#closeDialog").addEventListener("click", function () {
                dialog.close();
            });
        }
    }

    // Hamburger menu on dashboard
    var menu = document.querySelector(".dashboard-menu");
    if (menu) {
        var menuButton = document.createElement("button");
        menuButton.textContent = "☰ Menu";
        menuButton.type = "button";
        menuButton.style.margin = "10px";
        document.querySelector("h1").after(menuButton);

        menuButton.addEventListener("click", function () {
            if (menu.style.display === "none") {
                menu.style.display = "flex";
            } else {
                menu.style.display = "none";
            }
        });
    }

    // FAQ event handling
    document.querySelectorAll("details").forEach(function (item) {
        item.addEventListener("toggle", function () {
            if (item.open) {
                document.querySelectorAll("details").forEach(function (other) {
                    if (other !== item) {
                        other.removeAttribute("open");
                    }
                });
            }
        });
    });
});

// Practical 5 - Registration form validation
var registrationForm = document.getElementById("registrationForm");

if (registrationForm) {
    var name = document.getElementById("name");
    var email = document.getElementById("email");
    var mobile = document.getElementById("mobile");
    var password = document.getElementById("password");
    var confirmPassword = document.getElementById("confirmPassword");
    var course = document.getElementById("course");
    var year = document.getElementById("year");
    var terms = document.getElementById("terms");

    function showError(id, message) {
        var error = document.getElementById(id);
        error.textContent = message;
        error.style.color = "red";
    }

    function clearError(id) {
        document.getElementById(id).textContent = "";
    }

    function checkPasswordStrength() {
        var value = password.value;
        var strength = document.getElementById("passwordStrength");

        if (value.length === 0) {
            strength.textContent = "";
        } else if (value.length < 6) {
            strength.textContent = "Password strength: Weak";
        } else if (/[A-Z]/.test(value) && /[0-9]/.test(value) && /[^A-Za-z0-9]/.test(value)) {
            strength.textContent = "Password strength: Strong";
        } else {
            strength.textContent = "Password strength: Medium";
        }
    }

    password.addEventListener("input", checkPasswordStrength);

    registrationForm.addEventListener("submit", function (event) {
        event.preventDefault();
        var valid = true;

        var namePattern = /^[A-Za-z ]{2,50}$/;
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        var mobilePattern = /^[0-9]{10}$/;
        var passwordPattern = /^(?=.*[A-Za-z])(?=.*[0-9]).{6,}$/;

        if (!namePattern.test(name.value.trim())) {
            showError("nameError", "Enter a valid name.");
            valid = false;
        } else {
            clearError("nameError");
        }

        if (!emailPattern.test(email.value.trim())) {
            showError("emailError", "Enter a valid email.");
            valid = false;
        } else {
            clearError("emailError");
        }

        if (!mobilePattern.test(mobile.value.trim())) {
            showError("mobileError", "Enter a 10-digit mobile number.");
            valid = false;
        } else {
            clearError("mobileError");
        }

        if (!passwordPattern.test(password.value)) {
            showError("passwordError", "Password must be at least 6 characters and contain a letter and number.");
            valid = false;
        } else {
            clearError("passwordError");
        }

        if (confirmPassword.value !== password.value || confirmPassword.value === "") {
            showError("confirmPasswordError", "Passwords do not match.");
            valid = false;
        } else {
            clearError("confirmPasswordError");
        }

        if (course.value === "") {
            showError("courseError", "Please select a course.");
            valid = false;
        } else {
            clearError("courseError");
        }

        if (year.value === "") {
            showError("yearError", "Please select your year.");
            valid = false;
        } else {
            clearError("yearError");
        }

        if (!document.querySelector('input[name="gender"]:checked')) {
            showError("genderError", "Please select your gender.");
            valid = false;
        } else {
            clearError("genderError");
        }

        if (!terms.checked) {
            showError("termsError", "Please accept the terms and conditions.");
            valid = false;
        } else {
            clearError("termsError");
        }

        if (valid) {
            alert("Registration successful!");
            registrationForm.reset();
            document.getElementById("passwordStrength").textContent = "";
        }
    });
}

