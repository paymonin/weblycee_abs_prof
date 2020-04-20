YAHOO.namespace("example.calendar");

YAHOO.example.calendar.init = function() {

    function handleSelect(type,args,obj) {
        var dates = args[0]; 
        var date = dates[0];
        var year = date[0], month = date[1], day = date[2];

        var selMonth = document.getElementById("selMonth");
        var selDay = document.getElementById("selDay");
        var selYear = document.getElementById("selYear");
        var eselMonth = document.getElementById("eselMonth");
        var eselDay = document.getElementById("eselDay");
        var eselYear = document.getElementById("eselYear");

        selMonth.selectedIndex = month;
        selDay.selectedIndex = day;
        eselMonth.selectedIndex = month;
        eselDay.selectedIndex = day;

        for (var y=0;y<selYear.options.length;y++) {
            if (selYear.options[y].text == year) {
                selYear.selectedIndex = y;
                break;
            }
        }

        for (var y=0;y<eselYear.options.length;y++) {
            if (eselYear.options[y].text == year) {
                eselYear.selectedIndex = y;
                break;
            }
        }
    }

    function updateCal() {
        var selMonth = document.getElementById("selMonth");
        var selDay = document.getElementById("selDay");
        var selYear = document.getElementById("selYear");

        var month = parseInt(selMonth.options[selMonth.selectedIndex].text);
        var day = parseInt(selDay.options[selDay.selectedIndex].value);
        var year = parseInt(selYear.options[selYear.selectedIndex].value);

        if (! isNaN(month) && ! isNaN(day) && ! isNaN(year)) {
            var date = month + "/" + day + "/" + year;

            YAHOO.example.calendar.cal1.select(date);
            YAHOO.example.calendar.cal1.cfg.setProperty("pagedate", month + "/" + year);
            YAHOO.example.calendar.cal1.render();
        }
    }

    YAHOO.example.calendar.cal1 = new YAHOO.widget.Calendar("cal1","cal1Container", 
            { mindate:"1/1/2010",
                maxdate:"12/31/2016" });
    YAHOO.example.calendar.cal1.selectEvent.subscribe(handleSelect, YAHOO.example.calendar.cal1, true);
    YAHOO.example.calendar.cal1.render();

    YAHOO.util.Event.addListener(["selMonth","selDay","selYear"], "change", updateCal);
}

YAHOO.util.Event.onDOMReady(YAHOO.example.calendar.init);

