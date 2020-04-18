$(document).ready(function() {
    
    //dashboard: change dropdown menu for mobile devices
    if(screen.width < 1025) {
        var menu = document.getElementById('dropdown-ul').children;
        var ul = document.getElementById("header-dashboard");
        var links = [];
        for(var i = 0; i < menu.length; i++) {
            var li = document.createElement("LI");
            var a = document.createElement("A");
            a.createAttribute = "href";
            if(menu[i].text == "Dashboard" || menu[i].text == "Log Out") {
                a.createAttribute = "lang";
                a.lang = "en";
            }
            a.href = menu[i].href;
            a.innerHTML = menu[i].text;
            li.appendChild(a);
            links.push(li);
        }
        $("#header-dashboard").empty();
        for(var i = 0; i < links.length; i++) {
            ul.appendChild(links[i]);
        }
        
        //dashboard: remove exceeded elements
        $("#search-icon").remove();
        $("#pipe").remove();
        $("#small-user-photo").remove();
        
        //dashboard: remove calendar description
        $("#dashboard-calendar-description").remove();
    }
    if(screen.width <= 1024) {
        //dashboard: remove calendar description
        $("#dashboard-calendar-description").remove();
    }
});
