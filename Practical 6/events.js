document.addEventListener("DOMContentLoaded", function () {
    var events = [];
    var currentPage = 1;
    var perPage = 5;

    var list = document.getElementById("eventList");
    var search = document.getElementById("eventSearch");
    var category = document.getElementById("eventCategory");
    var sort = document.getElementById("eventSort");
    var status = document.getElementById("eventStatus");
    var pageInfo = document.getElementById("pageInfo");

    fetch("../data/events.json")
        .then(function (response) {
            if (!response.ok) {
                throw new Error("Events data could not be loaded.");
            }
            return response.json();
        })
        .then(function (data) {
            events = data;

            var categories = [];
            events.forEach(function (event) {
                if (!categories.includes(event.category)) {
                    categories.push(event.category);
                }
            });

            categories.sort().forEach(function (item) {
                var option = document.createElement("option");
                option.value = item;
                option.textContent = item;
                category.appendChild(option);
            });

            status.textContent = events.length + " events loaded.";
            showEvents();
        })
        .catch(function (error) {
            status.textContent = "Error: " + error.message;
        });

    function showEvents() {
        var filtered = events.filter(function (event) {
            var text = search.value.toLowerCase();
            var matchesSearch = event.title.toLowerCase().includes(text) ||
                event.location.toLowerCase().includes(text);
            var matchesCategory = category.value === "all" || event.category === category.value;
            return matchesSearch && matchesCategory;
        });

        filtered.sort(function (a, b) {
            if (sort.value === "title") {
                return a.title.localeCompare(b.title);
            }
            if (sort.value === "category") {
                return a.category.localeCompare(b.category);
            }
            return a.date.localeCompare(b.date);
        });

        var totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
        if (currentPage > totalPages) {
            currentPage = totalPages;
        }

        var start = (currentPage - 1) * perPage;
        var pageEvents = filtered.slice(start, start + perPage);
        list.innerHTML = "";

        if (pageEvents.length === 0) {
            list.innerHTML = "<p>No events found.</p>";
        } else {
            pageEvents.forEach(function (event) {
                var box = document.createElement("div");
                box.className = "event-item";
                box.innerHTML =
                    "<h3>" + event.title + "</h3>" +
                    "<p><strong>Date:</strong> " + event.date + "</p>" +
                    "<p><strong>Category:</strong> " + event.category + "</p>" +
                    "<p><strong>Location:</strong> " + event.location + "</p>";
                list.appendChild(box);
            });
        }

        pageInfo.textContent = "Page " + currentPage + " of " + totalPages;
        document.getElementById("previousPage").disabled = currentPage === 1;
        document.getElementById("nextPage").disabled = currentPage === totalPages;
    }

    search.addEventListener("input", function () {
        currentPage = 1;
        showEvents();
    });

    category.addEventListener("change", function () {
        currentPage = 1;
        showEvents();
    });

    sort.addEventListener("change", function () {
        currentPage = 1;
        showEvents();
    });

    document.getElementById("previousPage").addEventListener("click", function () {
        if (currentPage > 1) {
            currentPage--;
            showEvents();
        }
    });

    document.getElementById("nextPage").addEventListener("click", function () {
        currentPage++;
        showEvents();
    });
});
