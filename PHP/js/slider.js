(function () {
    YAHOO.namespace('example');

    var Dom = YAHOO.util.Dom;

    // Slider has a range of 300 pixels
    var range = 300;

    // Set up 15 pixel ticks
    var tickSize = 15;

    YAHOO.util.Event.onDOMReady(function () {
        var bselHour     = Dom.get("bselHour");
        var bselMin     = Dom.get("bselMin");
        var eselHour     = Dom.get("eselHour");
        var eselMin     = Dom.get("eselMin");

        // Create the DualSlider
        var slider = YAHOO.widget.Slider.getHorizDualSlider("demo_bg",
            "demo_min_thumb", "demo_max_thumb",
            range, tickSize);


        // Decorate the DualSlider instance with some new properties and
        // methods to maintain the highlight element
        YAHOO.lang.augmentObject(slider, {
            _status : 'Autre',
            _highlight : Dom.get("demo_highlight"),

            getStatus : function () { return this._status; },

            updateHighlight : function () {
                var delta = this.maxVal - this.minVal;
                if(this.minVal == 0 && this.maxVal == 150)
            this._status = 'Matin';
                else if(this.minVal == 150 && this.maxVal == 300)
            this._status  = 'Apres-midi';
                else if(this.minVal == 0 && this.maxVal == 300)
            this._status = 'Journee';
                else
            this._status = 'Autre';

        if (this.activeSlider === this.minSlider) {
            // If the min thumb moved, move the highlight's left edge
            Dom.setStyle(this._highlight,'left', (this.minVal + 12) + 'px');
        }
        // Adjust the width of the highlight to match inner boundary
        Dom.setStyle(this._highlight,'width', Math.max(delta - 12,0) + 'px');
            }
        },true);

        // Attach the highlight method to the slider's change event
        slider.subscribe('change',slider.updateHighlight,slider,true);

        // Create an event callback to update some display fields
        var report = function () {
            var bh = Math.floor(slider.minVal / 30);
            var bm = (slider.minVal % 30) / 7.5 ;
            var eh = Math.floor(slider.maxVal / 30);
            var em = (slider.maxVal % 30) / 7.5;
            bselHour.selectedIndex = bh;
            bselMin.selectedIndex = bm;
            eselHour.selectedIndex = eh;
            eselMin.selectedIndex = em;
            //reportSpan.innerHTML = bh + 'h' + bm + ' - ' + eh + 'h' + em;
            // Call our conversion function
            calculatedSpan.innerHTML =
                calculatedSpan.className = slider.getStatus();
        };

        // Subscribe to the slider's change event to report the status.
        slider.subscribe('change',report);

        // Attach the slider to the YAHOO.example namespace for public probing
        YAHOO.example.slider = slider;

        function onButtonDayClick(p_oEvent) {
            slider.setMinValue(0);
            slider.setMaxValue(300);
        }
        var button_day = new YAHOO.widget.Button("button_day");
        button_day.on("click", onButtonDayClick);

        function onButtonMorningClick(p_oEvent) {
            slider.setMinValue(0);
            slider.setMaxValue(150);
        }
        var button_morning = new YAHOO.widget.Button("button_morning");
        button_morning.on("click", onButtonMorningClick);

        function onButtonAfternoonClick(p_oEvent) {
            slider.setMinValue(150);
            slider.setMaxValue(300);
            slider.setMinValue(150);
        }
        var button_afternoon = new YAHOO.widget.Button("button_afternoon");
        button_afternoon.on("click", onButtonAfternoonClick);
    });
})();

