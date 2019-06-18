
    let link_tabs = document.getElementsByClassName("link-tab");
    let item_tabs = document.getElementsByClassName("item-tab");
    console.log(link_tabs);
    for (e = 0; e < link_tabs.length; e++) {
        link_tabs[e].addEventListener("click", function (event) {
            event.preventDefault();
            console.log(this.getAttribute("data-item"));
            displayTab(this.getAttribute("data-item"));
            activeMenu();
            this.classList.add("active");
        }, false);
    }

function displayTab(element) {

    for (i = 0; i < item_tabs.length; i++) {
        item_tabs[i].classList.remove("active");
    }
    document.getElementById(element).classList.add("active");
}

function activeMenu() {
    for (a = 0; a < link_tabs.length; a++) {
       link_tabs[a].classList.remove("active");
    }
}

