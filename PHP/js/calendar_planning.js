YAHOO.namespace("example.calendar");

YAHOO.example.calendar.init = function() {

    function mySelectHandler(type,args,obj) {
        var selected = args[0];
        var selDate = this.toDate(selected[0]);
        var affichage = document.getElementById("affichage").value;
        var n = document.getElementById("n").value;

        var url = "planning.php?d=" + selDate.getDate()
            + "&m=" + (selDate.getMonth()+1)
            + "&y=" + (selDate.getFullYear())
            + "&affichage=" + affichage
            + "&n=" + n;
        window.location=url;
    };

    YAHOO.example.calendar.cal1 = new YAHOO.widget.Calendar("cal1","cal1Container");
    var date = document.getElementById("semaine_debut").innerHTML.split('/'); 
    YAHOO.example.calendar.cal1.select(date[1] + "/" + date[0] + "/" + date[2]); 
    YAHOO.example.calendar.cal1.cfg.setProperty("pagedate", date[1] + "/" + date[2]);

    YAHOO.example.calendar.cal1.selectEvent.subscribe(mySelectHandler, YAHOO.example.calendar.cal1, true);
    YAHOO.example.calendar.cal1.render();
}


YAHOO.util.Event.onDOMReady(YAHOO.example.calendar.init);

