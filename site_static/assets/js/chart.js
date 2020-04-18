$(document).ready(function() {
    var ctx = document.getElementById('chart').getContext('2d');
    var chart = new Chart(ctx, {
        // The type of chart we want to create
        type: 'bar',
        data: {
            labels: [
                "Matematica", 
                "Storia", 
                "Fisica", 
                "Inglese",
                "Francese"
            ],
            datasets: [{
                label: "Ore totali di lezione",
                borderColor: 'silver',
                data: [28, 50, 25, 23, 26],
                "backgroundColor":[
                        "rgb(229, 91, 91)",
                        "rgb(97, 156, 223)",
                        "rgb(255, 205, 86)",
                        "rgb(125, 210, 132)",
                        "rgb(231, 125, 189)"
                ]
            }],
        },
        options: {
        // Configuration options go here
        }
    });
});