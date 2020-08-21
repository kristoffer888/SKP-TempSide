# week-picker

> Week-picker is an *ISO-8601* compliant picker for easily selecting weeks.

# Requirements

* [jQuery](https://jquery.com/)
* [moment.js](https://momentjs.com/)

# Initialize a picker

All elements with the class `week-picker` are automatically initialized on page load.

For manual initialization, `$(elem).weekPicker("init")` initialises an element.

``` html
<div class="week-picker"></div> <!-- Use defaults -->
<div class="week-picker" data-locale="se"></div> <!-- Swedish locale -->
<div class="week-picker" data-mode="single"></div> <!-- Single mode -->
```

Localization is supported for `se` and `en`. Defaults to `en`.

The possible modes are `multi` and `single`. Defaults to `multi`.

# Usage

## Value

``` js
$(elem).weekPicker("value") // [ "2017-01-09", "2017-02-13", "2017-02-27", "2017-03-20", "2017-05-29", ... ]
```

Returns the date of every weeks monday in format YYYY-MM-DD

## Clear

You can clear the picker programmatically (same as pressing "clear").

``` js
$(elem).weekPicker("clear")
```

## Events

When the picker is changed, a "change" event is dispatched. `event.details` contains data about the change, like the range selected and the mode (`select` or `deselect`).

``` js
// Multi-mode example (default)
$(".week-picker").on("change", function (event) {
	console.log(event.detail) // { mode: "select", range: ["2017-06-19", "2017-06-26", "2017-07-03"] }
})

// Single-mode example
$(".week-picker").on("change", function (event) {
	console.log(event.detail) // { mode: "deselect", range: ["2017-06-12"] }
})
```

## Demo

[Here](https://follgad.github.io/week-picker)