/**
 * Forms and interactive elements helper for Brezoaele V2 Theme.
 * Handles frontend UI interactions and toggles.
 */
document.addEventListener("DOMContentLoaded", function() {
    // 1. Forum Topic Creation Form Toggle (archive-intrebare.php)
    const toggleFormBtn = document.getElementById("toggle-form-btn");
    const newTopicForm = document.getElementById("new-topic-form");
    
    if (toggleFormBtn && newTopicForm) {
        toggleFormBtn.addEventListener("click", function() {
            if (newTopicForm.style.display === "none" || newTopicForm.style.display === "") {
                newTopicForm.style.display = "block";
                toggleFormBtn.innerText = "✖️ Închide Formularul";
                toggleFormBtn.style.backgroundColor = "#dc2626";
                toggleFormBtn.style.borderColor = "#0f172a";
            } else {
                newTopicForm.style.display = "none";
                toggleFormBtn.innerText = "➕ Propune un Subiect Nou";
                toggleFormBtn.style.backgroundColor = "var(--color-primary)";
                toggleFormBtn.style.borderColor = "var(--color-border)";
            }
        });
    }

    // 2. Mobile Navigation Hamburger Menu Toggle
    const siteNavigation = document.getElementById("site-navigation");
    const menuToggle = document.querySelector(".menu-toggle");
    
    if (siteNavigation && menuToggle) {
        menuToggle.addEventListener("click", function() {
            siteNavigation.classList.toggle("toggled");
            const expanded = siteNavigation.classList.contains("toggled");
            menuToggle.setAttribute("aria-expanded", expanded ? "true" : "false");
        });
    }

    // 3. Mobile Dropdown Submenus Accordion Toggle
    const parentLinks = siteNavigation ? siteNavigation.querySelectorAll(".menu-item-has-children > a") : [];
    parentLinks.forEach(link => {
        // Creare buton de toggle sub-meniu
        const dropdownToggle = document.createElement("button");
        dropdownToggle.className = "dropdown-toggle-btn";
        dropdownToggle.innerHTML = "▾";
        dropdownToggle.setAttribute("aria-expanded", "false");
        dropdownToggle.setAttribute("aria-label", "Deschide Sub-meniu");
        
        // Introducerea lui imediat după link-ul părinte
        link.parentNode.insertBefore(dropdownToggle, link.nextSibling);
        
        dropdownToggle.addEventListener("click", function(e) {
            e.preventDefault();
            const subMenu = link.parentNode.querySelector("ul.sub-menu");
            if (subMenu) {
                subMenu.classList.toggle("toggled-on");
                dropdownToggle.classList.toggle("toggled-on");
                const isExpanded = subMenu.classList.contains("toggled-on");
                dropdownToggle.setAttribute("aria-expanded", isExpanded ? "true" : "false");
            }
        });
    });
});
