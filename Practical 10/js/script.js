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

        document.querySelectorAll("tr").forEach(function (el) {
            el.style.backgroundColor = dark ? "#333" : "";
        });

        document.querySelectorAll("th").forEach(function (el) {
            el.style.backgroundColor = dark ? "#25c1c1" : "";
            el.style.color = "white";
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
    var menuButton = document.getElementById("menuButton");
    if (menu && menuButton) {
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


const registrationForm = document.getElementById("registrationForm");

if (registrationForm) {
    registrationForm.addEventListener("submit", function (e) {
        e.preventDefault();

        const name = document.getElementById("name").value.trim();
        const email = document.getElementById("email").value.trim();
        const mobile = document.getElementById("mobile").value.trim();
        const password = document.getElementById("password").value;
        const confirmPassword = document.getElementById("confirmPassword").value;
        const course = document.getElementById("course").value;
        const year = document.getElementById("year").value;
        const gender = document.querySelector('input[name="gender"]:checked');
        const terms = document.getElementById("terms").checked;

        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const mobilePattern = /^[0-9]{10}$/;

        if (name === "") {
            alert("Please enter your name.");
            document.getElementById("name").focus();
            return;
        }

        if (!emailPattern.test(email)) {
            alert("Please enter a valid email address.");
            document.getElementById("email").focus();
            return;
        }

        if (!mobilePattern.test(mobile)) {
            alert("Please enter a valid 10-digit mobile number.");
            document.getElementById("mobile").focus();
            return;
        }

        if (password.length < 6) {
            alert("Password must contain at least 6 characters.");
            document.getElementById("password").focus();
            return;
        }

        if (password !== confirmPassword) {
            alert("Password and Confirm Password do not match.");
            document.getElementById("confirmPassword").focus();
            return;
        }

        if (course === "") {
            alert("Please select a course.");
            document.getElementById("course").focus();
            return;
        }

        if (year === "") {
            alert("Please select your year.");
            document.getElementById("year").focus();
            return;
        }

        if (!gender) {
            alert("Please select your gender.");
            return;
        }

        if (!terms) {
            alert("Please accept the terms and conditions.");
            return;
        }

        registrationForm.submit();
    });
}
