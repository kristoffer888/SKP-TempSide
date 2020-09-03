(function () {
    if (!window.moment) throw new Error("moment.js not found; please place it in the global scope.");
    if (!window.jQuery && !window.$) throw new Error("jQuery not found; please place it in the global scope.");


    var getLocale = function (locale) {
        var loc;
        switch (locale) {
            case "sv":
            case "se":
                loc = {
                    OPEN_PICKER_SINGLE: "Välj vecka...",
                    OPEN_PICKER: "Välj veckor...",
                    CLEAR: "Rensa",
                    NUM_SELECTED: "$num_selected veckor valda",
                    WEEK_SELECTED: "Vecka $weeknum, $year"
                }
                break;
            case "en":
            default:
                loc = {
                    OPEN_PICKER_SINGLE: "Choose a week...",
                    OPEN_PICKER: "Open picker...",
                    CLEAR: "Clear",
                    NUM_SELECTED: "$num_selected weeks selected",
                    WEEK_SELECTED: "Week $weeknum, $year"
                }
        }
        return loc;
    }

    var getDate = function (week, year) {
        return moment().year(year).isoWeek(week).format("YYYY-MM-DD");
    }

    var $ = window.jQuery || window.$;
    var moment = window.moment;

    $.fn.weekPicker = function (method) {
        var ret = this;
        var args = arguments;

        // Methods to be accessed by: $( ... ).weekPicker(methodName)
        var methods = {
            init: function () {
                var headthis = this;
                var $this = $(this);
                $this.empty();

                var mode = this.weekPicker.mode = $this.data("mode") || "multi";

                var locale = getLocale($this.data("locale"));

                // TODO: Fix html to multiple rows for readability
                $this.append("<div class='_week-picker'><input readonly placeholder='" + (mode == "single" ? locale.OPEN_PICKER_SINGLE : locale.OPEN_PICKER) + "' /><div class='_middle'><div class='_popup' style='display:none'><div class='_oh'><a href='javascript:void(0)' class='_arrow _left'>&lt;</a><p class='_yeardisp'></p><a href='javascript:void(0)' class='_arrow _right'>&gt;</a></div><table class='_weekTable' /><div class='_uh'><a class='_clear' href='javascript:void(0)'>" + locale.CLEAR + "</a></div></div></div></div>");
                var popup = $this.find("._popup");

                $this.weekPicker("changeYear", new Date().getFullYear());

                $this.find("input").on("change input", function (e) {
                    e.target.value = "";
                });

                $this.find("._clear").on("click", function () {
                    headthis.weekPicker.chosen = [];
                    $this.weekPicker("updateSelection");
                })

                if (this.weekPicker.mode != "single") {
                    var $divs = $this.children("div");

                    var rangeStart;
                    var rangeEnd;
                    var rangeIsAdding;
                    var holding = false;

                    // Mouse events
                    $divs.on("mousedown", "td", function () {
                        holding = true;
                        var wk = $(this).data("week");
                        var yr = $this.data("year");
                        rangeStart = moment().year(yr).isoWeek(wk).startOf("isoWeek");
                        rangeIsAdding = !$(this).hasClass("_active");
                    })
                    $divs.on("mousemove", "td", function () {
                        if (holding) {
                            var wk = $(this).data("week");
                            var yr = $this.data("year");
                            rangeEnd = moment().year(yr).isoWeek(wk).startOf("isoWeek");
                            $this.weekPicker("setSelectedRange", rangeStart, rangeEnd, rangeIsAdding);
                        }
                    })
                    $divs.on("mouseup", "td", function () {
                        holding = false;
                        if (!rangeStart) return;

                        var wk = $(this).data("week");
                        var yr = $this.data("year");

                        var first = rangeStart;
                        var stop = moment().year(yr).isoWeek(wk).startOf("isoWeek");

                        var rangeIsGoingForward = stop.isAfter(first);

                        var from = rangeIsGoingForward ? first : stop;
                        var to = rangeIsGoingForward ? stop : first;

                        $this.weekPicker("setRange", from, to, rangeIsAdding);
                    })

                    // Touch events
                    $divs.on("touchstart", "td", function () {
                        holding = true;
                        var wk = $(this).data("week");
                        var yr = $this.data("year");
                        rangeStart = moment().year(yr).isoWeek(wk).startOf("isoWeek");
                        rangeIsAdding = !$(this).hasClass("_active");
                    })
                    $divs.on("touchmove", "td", function (e) {
                        if (holding) {
                            var touchLocation = e.originalEvent.changedTouches[0];
                            var elem = $(document.elementFromPoint(touchLocation.clientX, touchLocation.clientY));

                            var wk = elem.data("week");
                            var yr = $this.data("year");
                            rangeEnd = moment().year(yr).isoWeek(wk).startOf("isoWeek");
                            $this.weekPicker("setSelectedRange", rangeStart, rangeEnd, rangeIsAdding);
                        }
                    })
                    $divs.on("touchend", "td", function (e) {
                        holding = false;
                        if (!rangeStart) return;

                        var touchLocation = e.originalEvent.changedTouches[0];
                        var elem = $(document.elementFromPoint(touchLocation.clientX, touchLocation.clientY));

                        var wk = elem.data("week");
                        var yr = $this.data("year");

                        var first = rangeStart;
                        var stop = moment().year(yr).isoWeek(wk).startOf("isoWeek");

                        var rangeIsGoingForward = stop.isAfter(first);

                        var from = rangeIsGoingForward ? first : stop;
                        var to = rangeIsGoingForward ? stop : first;

                        $this.weekPicker("setRange", from, to, rangeIsAdding);
                    })
                } else {
                    $this.children("div").on("click", "td", function () {
                        var $td = $(this);
                        var isActive = $td.hasClass("_active");
                        $this.find("._active").removeClass("_active");
                        if (!isActive) $td.addClass("_active");

                        var week = $td.data("week");
                        var year = $this.data("year");

                        $this.weekPicker("toggleWeek", week, year);

                        var event = new CustomEvent("change", {
                            detail: {
                                range: [getDate(week, year)],
                                mode: !isActive ? "select" : "deselect"
                            }
                        });
                        headthis.dispatchEvent(event);
                    });
                }

                $this.on("dragstart", "td", function () {
                    return false;
                });
                $this.find("._oh").on("click", "._arrow", function (e) {
                    var r = $(e.target).hasClass("_right");
                    var yr = Number($this.data("year"));
                    $this.weekPicker("changeYear", yr + (r ? 1 : -1))
                });
                $this.children().children().on("click focus", function () {
                    popup.show();
                });

                $(document).on("click", function (e) {
                    var children = $this.children().children();
                    if (children.find(e.target).length === 0 && !children.is(e.target)) {
                        popup.hide();
                    }
                });
                $this.on("keypress", function (e) {
                    if (e.code == "Enter") {
                        e.target
                    }
                })
            },
            clear: function () {
                this.weekPicker.chosen = [];
                $(this).weekPicker("updateSelection");
            },
            value: function () {
                return this.weekPicker.chosen;
            },
            addDate: function (date) {
                var ind = this.weekPicker.chosen.indexOf(date);
                if (ind === -1) {
                    this.weekPicker.chosen.push(date);
                }
            },
            removeDate: function (date) {
                var ind = this.weekPicker.chosen.indexOf(date);
                if (ind !== -1) {
                    this.weekPicker.chosen.splice(ind, 1);
                }
            },
            setRange: function (dateBegin, dateEnd, setActive) {
                var $this = $(this);
                var dates = [];

                while (dateBegin <= dateEnd) {
                    dates.push(dateBegin.format("YYYY-MM-DD"));
                    dateBegin.add(1, "week");
                }

                dates.forEach(function (date) {
                    $this.weekPicker(setActive ? "addDate" : "removeDate", date);
                })

                var event = new CustomEvent("change", {
                    detail: {
                        range: dates,
                        mode: setActive ? "select" : "deselect"
                    }
                });
                this.dispatchEvent(event);

                $this.weekPicker("updateSelection");
            },
            setSelectedRange: function (from, to, setActive) {
                var fromWeek = from.isoWeek();
                var toWeek = to.isoWeek();

                var first = Math.min(fromWeek, toWeek);
                var last = Math.max(fromWeek, toWeek);

                var weeks = $(this).find("._weekTable td");
                weeks.removeClass("_rangeAdd _rangeDel");
                var slicedWeeks = weeks.slice(first - 1, last);
                slicedWeeks.each(function () {
                    $(this).addClass(setActive ? "_rangeAdd" : "_rangeDel");
                })
            },
            toggleWeek: function (week, year) {
                var date = moment().year(year).isoWeek(week).startOf("isoWeek").format("YYYY-MM-DD");
                firstDateOfWeek = date;
                startCall();
                var ind = this.weekPicker.chosen.indexOf(date);

                if (ind !== -1) {
                    this.weekPicker.chosen.splice(ind, 1);
                } else if (this.weekPicker.mode == "single") {
                    this.weekPicker.chosen = [date];
                } else {
                    this.weekPicker.chosen.push(date);
                }
                $(this).weekPicker("updateInputVal");
            },
            updateSelection: function () {
                var $this = $(this);

                var table = $this.find("table");
                table.empty();

                var year = $this.data("year");

                var totalWeeks = moment().year(year).isoWeeksInYear();

                $this.find("._yeardisp").text(year);

                var row;
                for (var i = 1; i < totalWeeks + 1; i++) {
                    if (i % 7 == 1) {
                        table.append("<tr />");
                        row = table.find("tr").last();
                    }
                    var occupied = this.weekPicker.chosen.indexOf(moment().year(year).isoWeek(i).startOf("isoWeek").format("YYYY-MM-DD")) !== -1;
                    var today = year == new Date().getFullYear() && i == moment().isoWeek();
                    row.append("<td class='" + (occupied ? "_active " : "") + (today ? "_current" : "") + "' data-week='" + i + "'>" + i + "</td>");
                }
                $this.weekPicker("updateInputVal");
            },
            changeYear: function (year) {
                if (!year) throw new Error("need year");
                if (typeof year == "string") year = Number(year);

                var $this = $(this);
                $this.data("year", year);
                $this.weekPicker("updateSelection");
            },
            updateInputVal: function () {
                var $this = $(this);

                var locale = getLocale($this.data("locale"));

                var input = $this.find("input");

                if (this.weekPicker.chosen.length !== 0) {
                    if (this.weekPicker.mode == "single") {
                        var date = moment(this.weekPicker.chosen[0]);
                        input.val(locale.WEEK_SELECTED.replace("$weeknum", date.isoWeek()).replace("$year", date.year()));
                    } else {
                        input.val(locale.NUM_SELECTED.replace("$num_selected", this.weekPicker.chosen.length));
                    }
                } else {
                    input.val("");
                }
            }
        }

        this.each(function () {
            if (!this.weekPicker) {
                this.weekPicker = {
                    chosen: [],
                    disabled: []
                }
            }
            if (methods[method]) {
                // Apply arguiments to the function specified
                // For example: '$( ... ).weekPicker("method", value)' becomes 'method(value)'
                ret = methods[method].apply(this, Array.prototype.slice.call(args, 1))
            }
        })
        return ret;
    }
    $(document).ready(function () {
        $(".week-picker").each(function () {
            $(this).weekPicker("init");
        })
    });
})();
